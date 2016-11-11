<?php include 'elements/admin_header.php';

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'phpseclib');
include_once('Net/SSH2.php');
include_once('Net/SFTP.php');
$check="";
function run_ssh_command($cmd, $ip,$ssh_username,$ssh_password)
{
    $result = "Openning ssh to " . $ip . "<br/>";
    $ssh = new Net_SSH2($ip);
    if (!$ssh->login($ssh_username, $ssh_password)) {
        return $result . 'Login Failed';
    }
    $result .= "Logged in. Executing commands..." . "<br/>";
    //$ssh->enableQuietMode();

    if(strcasecmp($cmd, "start") == 0) {
        $result = $result . '<b>start test_runner:</b></br>';
        $result = $result . nl2br($ssh->exec("start test_runner"));
        //$result = $result . '<b>start resourcesmon_runner:</b></br>';
        $ssh->exec("start resourcesmon_runner");
        $ssh->disconnect();
        $result .=  "<br/>" . "Done.";
    }else{
        $result .= '<b>stop test_runner:</b></br>';
        $result .=  nl2br($ssh->exec("stop test_runner"));
        //$result .= '<b>stop resourcesmon_runner:</b></br>';
        $ssh->exec("stop resourcesmon_runner");
        if(strcasecmp($cmd, "restart") == 0) {
            $result = $result . '<b>start test_runner:</b></br>';
            $result = $result . nl2br($ssh->exec("start test_runner"));
            //$result = $result . '<b>start resourcesmon_runner:</b></br>';
            $ssh->exec("start resourcesmon_runner");
        }
        $ssh->disconnect();
        $result .=  "<br/>" . "Done.";
    }


    return $result;
}

function restart_webserver_scripts($cmd)
{
    $result = "Openning ssh to ".MAIN_SERVER_IP."<br/>";
    $ssh = new Net_SSH2(MAIN_SERVER_IP);
    if (!$ssh->login(MAIN_SERVER_SSH_USER_NAME, MAIN_SERVER_SSH_USER_PASS)) {
        return $result . 'Login Failed';
    }
    //$ssh->enableQuietMode();
    $result .= '<b>stop mon_runner:</b></br>';
    $result .=  nl2br($ssh->exec("stop mon_runner"));
    $result .= '<b>stop serverphp_runner:</b></br>';
    $result .=  nl2br($ssh->exec("stop serverphp_runner"));
    $result .= '<b>stop deduct_cash_runner:</b></br>';
    $result .=  nl2br($ssh->exec("stop deduct_cash_runner"));
    $result .= '<b>stop process_complete_runner:</b></br>';
    $result .=  nl2br($ssh->exec("stop process_complete_runner"));
    if(strcasecmp($cmd, "restart") == 0) {
        $result = $result . '<b>start mon_runner:</b></br>';
        $result = $result . nl2br($ssh->exec("start mon_runner"));
    }
    $ssh->disconnect();
    $result .=  "<br/>" . "Done.";
    return $result;
}

function install_remote_server($remote_server_id,$ip,$ssh_username,$ssh_password,$remote_group){
    global $db;
    $result="";
    /////////////////////////////////////////////upload test.php/////////////////////////////////////////////////
    $local_directory = dirname(dirname(__FILE__))."/backend_jobs/";
    $lamp_install_file="lamp_install.sh";
    $db_install_file="db_install.php";
    $remote_test_file="test.php";
    $remote_thread_file="thread.php";
    $remote_resmon_file="resmon.php";
    $daemons_file="daemons";

    $remote_directory = '/var/';

    $conf_file="test_runner.conf";
    $conf_directory = '/etc/init/';

    $sftp = new Net_SFTP($ip);
    if (!$sftp->login($ssh_username, $ssh_password))
    {
        $result.='Login Failed';
        $result.="<br/>";
        return $result;
    }
    $upload_result1 = $sftp->put($remote_directory.$lamp_install_file, $local_directory.$lamp_install_file, NET_SFTP_LOCAL_FILE);
    $upload_result1 = $sftp->put($remote_directory.$db_install_file, $local_directory.$db_install_file, NET_SFTP_LOCAL_FILE);
    $upload_result2 = $sftp->put($remote_directory.$remote_test_file, $local_directory.$remote_test_file, NET_SFTP_LOCAL_FILE);
    $upload_result3 = $sftp->put($remote_directory.$remote_thread_file, $local_directory.$remote_thread_file, NET_SFTP_LOCAL_FILE);
    $upload_result4 = $sftp->put($remote_directory.$remote_resmon_file, $local_directory.$remote_resmon_file, NET_SFTP_LOCAL_FILE);
    $upload_result5 = $sftp->put($conf_directory.$conf_file, $local_directory.$conf_file, NET_SFTP_LOCAL_FILE);
    $sftp->put($remote_directory.$daemons_file,$local_directory.$daemons_file, NET_SFTP_LOCAL_FILE);
    $sftp->disconnect();
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $ssh = new Net_SSH2($ip);
    if (!$ssh->login($ssh_username, $ssh_password)) {
        $result.= 'Login Failed';
        $result.="<br/>";
        return $result;
    }

    $ssh->exec("sudo chmod +x /var/lamp_install.sh");
    $result.=$ssh->exec("sudo /var/lamp_install.sh");
    sleep(3);
    /*if($remote_group=="a"){*/
        $ssh->exec("sudo chmod +x /var/db_install.php");
        $result.=$ssh->exec("php /var/db_install.php");
    //}
    sleep(3);

    $result.=$ssh->exec("start test_runner");

    $utime = date("Y-m-d H:i:s");
    $rs = $ssh->exec("php /var/resmon.php");
    $statusmsg="test_runner OK";
    $db->query("UPDATE `remote_server_list` SET `last_alive`='" . $utime . "',`ressnap`='" . $rs . "',`current_status`= '" . $statusmsg . "' WHERE `remote_ip` = '".$ip."'");

    //////////////////////for ospf///////////////////////////////////////////////
    $ssh->exec("apt-get -y install quagga-*");
    $ssh->exec("cp /usr/share/doc/quagga/examples/zebra.conf.sample /etc/quagga/zebra.conf");
    $ssh->exec("cp /usr/share/doc/quagga/examples/ospfd.conf.sample /etc/quagga/ospfd.conf");
    $ssh->exec("chown quagga.quaggavty /etc/quagga/*.conf");
    $ssh->exec("chmod 640 /etc/quagga/*.conf");

    $ssh->exec("rm /etc/quagga/daemons");
    $ssh->exec("cp /var/daemons /etc/quagga/");
    $ssh->exec("chmod 640 /etc/quagga/daemons");

    $ssh->exec("echo VTYSH_PAGER=more > /etc/environment");
    $ssh->exec("/etc/init.d/quagga restart");
    //$ssh->exec("reboot"); //todo
    $ssh->disconnect();
    /*sleep(5);

    $ssh = new Net_SSH2($ip);
    if (!$ssh->login($ssh_username, $ssh_password)) {
        //$result.= 'Login Failed';
        $result.="<br/>";
        return $result;
    }
    $ssh->exec("vtysh");
    $ssh->exec("configure terminal");
    $ssh->exec("router ospf");
    $ssh->exec("router-id ".$remote_server_id);
    $ssh->exec("exit");
    $ssh->exec("exit");
    $ssh->exec("exit");
    $ssh->disconnect();*/
    /////////////////////////////////////////////////////////////////////
    $result.="<br/>"."Done.";
    return $result;
}

if(!empty($_POST)) {
    if (!empty($_POST['command'])) {
        $ssh_username="root";
        $ssh_password="kdcsev113@pass";
        $res = $db->query("SELECT * FROM `remote_server_list` WHERE `id`='".$_POST['id']."'");
        if($res->num_rows>0){
            $remote_data=$res->fetch_assoc();
            $ssh_username=$remote_data['ssh_username'];
            $ssh_password=$remote_data['ssh_password'];
        }

        $command = trim($_POST['command']);
        if($command=="stop"){
            $db->query("UPDATE `remote_server_list` SET `is_monitored`='0' WHERE id=" . trim($_POST['id']));
        }else{
            $db->query("UPDATE `remote_server_list` SET `is_monitored`='1' WHERE id=" . trim($_POST['id']));
        }
        $bashresult = run_ssh_command($command, trim($_POST['ip']),$ssh_username,$ssh_password);
    }
    else if(!empty($_POST['maincommand'])) {
        $command = trim($_POST['maincommand']);
        $bashresult = restart_webserver_scripts($command);
    }
    else {
        $res = $db->query("SELECT * FROM `remote_server_list` WHERE `remote_ip`='" . $_POST['remote_ip'] . "'");
        if ($res->num_rows > 0) {
            if ($db->query("UPDATE `remote_server_list` SET `ssh_username`='" . $_POST['ssh_username'] . "', `ssh_password`='" . $_POST['ssh_password'] . "', `server_name`='" . $_POST['sname'] . "', `remote_group`='" . $_POST['remote_grp'] . "' WHERE `remote_ip`='" . $_POST['remote_ip'] . "'")) {
                $check = 1;
            }
        } else {
            $query=$db->query("INSERT INTO `remote_server_list` (`email`, `remote_ip`, `ssh_username`, `ssh_password`, `server_name`, `remote_group`) VALUES('" . $_POST['email'] . "', '" . $_POST['remote_ip'] . "', '" . $_POST['ssh_username'] . "', '" . $_POST['ssh_password'] . "', '" . $_POST['sname'] . "', '" . $_POST['remote_grp'] . "')");
            $remote_server_id=$db->insert_id;
            $check = 1;
            //$bashresult=install_remote_server($remote_server_id,$_POST['remote_ip'],$_POST['ssh_username'],$_POST['ssh_password'],$_POST['remote_grp']);
        }
        $bashresult=install_remote_server($remote_server_id,$_POST['remote_ip'],$_POST['ssh_username'],$_POST['ssh_password'],$_POST['remote_grp']);
    }
}
$arr=$db->query("SELECT * FROM `remote_server_list`");
$serverphp = false;
$socket_trigger = false;
$deduct_cash = false;
$process_complete = false;
$mon = false;

$sshcon = new Net_SSH2(MAIN_SERVER_IP);

if ($sshcon->login(MAIN_SERVER_SSH_USER_NAME, MAIN_SERVER_SSH_USER_PASS)) {
    $monstr = $sshcon->exec("status mon_runner");
    //print_r($monstr);
    if(strpos($monstr, 'stop/waiting') === false){
        $mon = true;
    }

    $serverphpstr = $sshcon->exec("status serverphp_runner");
    //print_r($serverphpstr);
    if(strpos($serverphpstr, 'stop/waiting') === false){
        $serverphp = true;
    }

    $socket_triggerstr = $sshcon->exec("status socket_trigger_runner");
    if(strpos($socket_triggerstr, 'stop/waiting') === false)
        $socket_trigger = true;

    $deduct_cashstr = shell_exec("status deduct_cash_runner");
    if(strpos($deduct_cashstr, 'stop/waiting') === false) {
        $deduct_cash = true;
    }

    $process_completestr = shell_exec("status process_complete_runner");
    if(strpos($process_completestr, 'stop/waiting') === false) {
        $process_complete = true;
    }
}

?>
<div class="row border-bottom">
    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
            <span class="sub-page-title">Remote Data</span>
        </div>
    </nav>
</div>
<?php if(!empty($bashresult)): ?>
    <div class="row wrapper">
        <h3>Bash output:</h3>
        <div class="row wrapper" style="background-color: black; color: white">
            <?php echo $bashresult; ?>
        </div>
    </div>
<?php endif;?>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <h3 style="display: none;">All Remote Servers</h3>
            <table class="table table-bordered" style="margin-top: 12px;display: none;">
                <thead>
                <tr>
                    <th>Remote Server IP</th>
                    <th>Server name</th>
                    <th>SSH user name</th>
                    <th>Group</th>
                    <th>ScriptStatus</th>
                    <th>ResourceUsage</th>
                    <th></th>
                    <!-- <th>Edit information</th> -->
                </tr>
                </thead>
                <tbody>
                <?php
                $remote_server_data=array();
                while($val=$arr->fetch_assoc()){
                    $remote_server_data[]=$val;
                    ?>
                    <tr class="remote_list_<?php echo $val['id']; ?>">
                        <td>
                            <?php echo $val['remote_ip']; ?>
                            <i class="fa fa-pencil-square-o edit" data-pk="<?php echo $val['id']; ?>" style="color:#4285F4; margin-left:10px; cursor:pointer;"></i>
                            <i class="fa fa-trash-o delete" data-pk="<?php echo $val['id']; ?>" style="color:#E40303; margin-left:10px; cursor:pointer;"></i>
                            <?php if($val['is_active']==0){ ?>
                                <a href="javascript:void()" class="status stat_<?php echo $val['id']; ?>" data="1" data-pk="<?php echo $val['id']; ?>" value="Active"><i class="fa fa-circle" style="color:#D6465F;margin-left:10px;"></i></a>
                            <?php }else{
                                ?>
                                <a href="javascript:void()" class="status stat_<?php echo $val['id']; ?>" data="0" data-pk="<?php echo $val['id']; ?>" value="Inactive"><i class="fa fa-circle" style="color:#449D44;margin-left:10px;"></i></a>
                            <?php
                            } ?>
                        </td>
                        <td><?php echo $val['server_name']; ?></td>
                        <td><?php echo $val['ssh_username']; ?></td>
                        <td><?php echo $val['remote_group']; ?></td>
                        <td><?php echo $val['current_status']; ?></td>
                        <td style="max-width: 300px;"><?php echo $val['ressnap']; ?></td>
                        <td>
                            <form role="form" action="" method="post">
                                <div class="form-group" id="scroll_div">
                                    <input type="hidden" name="ip" value="<?php echo $val['remote_ip']; ?>" />
                                    <input type="hidden" name="id" value="<?php echo $val['id']; ?>" />
                                    <input type="hidden" name="monstat" value="<?php echo $val['is_monitored']; ?>" />
                                    <button type="submit" class="btn btn-success" name="command" value="restart">Restart</button>
                                    <?php
                                    if($val['is_monitored']==0){
                                        ?>
                                        <button type="submit" class="btn btn-success" name="command" value="start">Start</button>
                                    <?php
                                    }else{
                                        ?>
                                        <button type="submit" class="btn btn-success" name="command" value="stop">Stop</button>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </form>
                        </td>
                        <!-- <td><input type="button" class="edit btn btn-primary" data-pk="<?php echo $val['id']; ?>" value="Edit"/></td> -->
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <div class="alert alert-success" role="alert" id="diagram_save_success_message" style="display: none;"></div>
            <div class="row" style="margin-bottom: 5px;margin-top: 10px;">
                <div class="col-md-6" style="text-align: left">
                    <h5 style="font-size: 15px;font-weight: bold;padding-left: 10px;">All Remote Servers</h5>
                </div>
                <div class="col-md-6" style="text-align: right">
                    <button id="add_remote_server_btn" class="btn btn-success">Add remote server</button>
                </div>
            </div>

            <link href="../assets/plugins/gojs/go-1.css" rel="stylesheet">
            <script src="../assets/plugins/jquery/jquery-1.11.3.min.js" type="text/javascript"></script>
            <script src="../assets/plugins/gojs/go-1.js" type="text/javascript"></script>
            <script src="../assets/plugins/gojs/go-sample.js" type="text/javascript"></script>
            <div id="myDiagramDiv" style="border: solid 1px black; width: 100%; height: 400px"></div>
            <div id="gojs-contextMenu">
                <ul>
                    <li id="run_ospf" onclick="cxcommand(this.textContent)"><a href="#" target="_self">Run OSPF</a></li>
                    <li id="stop_ospf" onclick="cxcommand(this.textContent)"><a href="#" target="_self">Stop OSPF</a></li>
                    <li id="control" onclick="cxcommand(this.textContent)"><a href="#" target="_self">Control</a></li>
                    <li id="edit" onclick="cxcommand(this.textContent)"><a href="#" target="_self">Edit</a></li>
                    <li id="delete" onclick="cxcommand(this.textContent)"><a href="#" target="_self">Delete</a></li>
                </ul>
            </div>
            <div>
                <div style="text-align: right; margin-top: 5px;">
                    <button id="SaveButton" class="btn btn-success" onclick="save()">Save</button>
                </div>
                <?php
                $diagram_data_arr=array("nodeKeyProperty"=>"id",
                    "nodeDataArray"=>array(),
                    "linkDataArray"=>array()
                );

                $node_ids=array();
                $diagram_node_sql="select * from `diagram_nodes`";
                $diagram_node_query=$db->query($diagram_node_sql);
                while($row=$diagram_node_query->fetch_assoc()){
                    $diagram_data_arr['nodeDataArray'][]=$row;
                    $node_ids[]=$row['id'];
                }

                $diagram_link_sql="select * from `diagram_links`";
                $diagram_link_query=$db->query($diagram_link_sql);
                while($row=$diagram_link_query->fetch_assoc()){
                    $row1=array('from'=>$row['from_node'],'to'=>$row['to_node'],'text'=>$row['text'],'curviness'=>intval($row['curviness']),'color'=>$row['color'],'points'=>array());
                    $points_str_array=explode(",",$row['points']);
                    foreach($points_str_array as $pt){
                        $row1['points'][]=doubleval($pt);
                    }
                    if($row1['from']==0 || $row1['to']==0){
                        $row1['color']="#ff0000";
                    }else{
                        $row1['color']="#ff00ff";
                    }
                    $row1['color']="#ff00ff";
                    //$row1['color']="#000000";
                    $diagram_data_arr['linkDataArray'][]=$row1;
                }

                foreach($remote_server_data as $remote_server){
                    if(!in_array($remote_server['id'],$node_ids)){
                        $text="RS_A";
                        $color="#00ff00";
                        if($remote_server['remote_group']=="b"){
                            $text="RS_B";
                            $color="#00ffff";
                        }
                        if($remote_server['current_status']=="Login Failed" || $remote_server['ressnap']==""){
                            $color="#777777";
                        }
                        $text=$remote_server['server_name'];
                        $row=array("id"=>$remote_server['id'],"loc"=>"0 0", "text"=>$text,"color"=>$color);
                        $diagram_data_arr['nodeDataArray'][]=$row;
                        $node_ids[]=$remote_server['id'];
                    }
                }
                if(!in_array(0,$node_ids)){
                    $diagram_data_arr['nodeDataArray'][]=array("id"=>0,"loc" => "300 0", "text"=>"MS" ,"color"=>"#ff0000");
                }
                ?>
                <textarea id="mySavedModel" style="width:100%;height:300px;display:none;">
                    <?php echo(json_encode($diagram_data_arr)); ?>
                </textarea>
            </div>

            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>MainScript control</h5>
                </div>
                <div class="ibox-content" style="padding-bottom: 0px;">
                    <div class="row wrapper">
                        <table class="footable table table-stripped toggle-arrow-tiny" data-page-size="8">
                            <thead>
                            <tr>
                                <th>Service</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr id="monrunner">
                                <td>moitor</td>
                                <td>
                                    <?php if($mon){
                                        echo '<span style="color: green;">Running</span>';
                                    }
                                    else
                                    {
                                        echo '<span style="color: red;">Down</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr id="serverphp">
                                <td>server.php</td>
                                <td>
                                    <?php if($serverphp){
                                        echo '<span style="color: green;">Running</span>';
                                    }
                                    else
                                    {
                                        echo '<span style="color: red;">Down</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr id="deduct_cash">
                                <td>deduct_cash caller</td>
                                <td>
                                    <?php if($deduct_cash){
                                        echo '<span style="color: green;">Running</span>';
                                    }
                                    else
                                    {
                                        echo '<span style="color: red;">Down</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr id="deduct_cash">
                                <td>process_complete caller</td>
                                <td>
                                    <?php if($process_complete){
                                        echo '<span style="color: green;">Running</span>';
                                    }
                                    else
                                    {
                                        echo '<span style="color: red;">Down</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row wrapper">
                        <form role="form" action="" method="post">
                            <div class="form-group" id="scroll_div">
                                <button type="submit" class="btn btn-success" name="maincommand" value="restart">Restart</button>
                                <button type="submit" class="btn btn-success" name="maincommand" value="stop">Stop</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="add_remote_server_modal" tabindex="-1" role="dialog" aria-labelledby="add_remote_server_modal" aria-hidden="true">
                <div class="modal-dialog" style="z-index:1200">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title exampleModalLabel">Add Remote Server</h4>
                        </div>
                        <div id="add_remote_server">
                            <div class="modal-body">
                                <div>
                                    <div id="scroll_div" style=" min-height: 537px;">
                                        <form role="form" action="" method="post" autocomplete="off">
                                            <div class="form-group">
                                                <?php if($check==1){
                                                    ?>
                                                    <div id="msg" class="bg-success" style="padding:7px;mergin-top:5px;"><strong>Changes applied successfully</strong></div>
                                                <?php
                                                } ?>
                                                <label for="email">Remote IP:</label>
                                                <input type="text" class="form-control" id="remote_id" name="remote_ip" placeholder="Enter remote ip" autocomplete="off" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="pwd">Server name:</label>
                                                <input type="text" class="form-control" id="sname" name="sname" placeholder="Enter server name" autocomplete="off" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="pwd">SSH user name:</label>
                                                <input type="text" class="form-control" id="ssh_username" name="ssh_username" placeholder="Enter username" autocomplete="off" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="pwd">SSH password:</label>
                                                <input type="password" class="form-control" id="ssh_password" name="ssh_password" placeholder="Enter password" autocomplete="off" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="pwd">Email ID:</label>
                                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" autocomplete="off" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="sel1">Remote group:</label>
                                                <select class="form-control" id="sel1" name="remote_grp" required>
                                                    <option>Select a group</option>
                                                    <option id="a" value="a">a</option>
                                                    <option id="b" value="b">b</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-success btn_install">Install</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="remote_server_control_modal" tabindex="-1" role="dialog" aria-labelledby="remote_server_control_modal" aria-hidden="true">
                <div class="modal-dialog" style="z-index:1200">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="exampleModalLabel">Control remote server</h4>
                        </div>
                        <div id="remote_server_control">
                            <div class="modal-body">
                                <div class="alert alert-success" role="alert" id="manual_add_success_message" style="display: none;"></div>
                                <div class="alert alert-danger" role="alert" id="manual_add_error_message" style="display: none;"></div>

                                <div class="form-group">
                                    <label for="remote_ip" class="control-label">Remote ip:</label>
                                    <input type="text" class="form-control" name="remote_ip" id="remote_ip" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="server_name" class="control-label">Server name:</label>
                                    <input type="text" class="form-control" name="server_name" id="server_name" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="script_status" class="control-label">Script status:</label>
                                    <input type="text" class="form-control" name="script_status" id="script_status" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="number_successful_commands" class="control-label">Successful commands number:</label>
                                    <input type="text" class="form-control" name="number_successful_commands" id="number_successful_commands" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="resource_usage" class="control-label">Resource usage:</label>
                                    <textarea class="form-control" name="resource_usage" id="resource_usage" disabled></textarea>
                                </div>

                                <input type="hidden" name="server_id" id="server_id" value="">
                                <div class="form-group" style="text-align: right;">
                                    <form role="form" action="" method="post">
                                        <div class="form-group" id="scroll_div">
                                            <input type="hidden" name="ip" class="ip" value="" />
                                            <input type="hidden" name="id" class="id" value="" />
                                            <input type="hidden" name="monstat" class="monstat" value="" />
                                            <button type="submit" class="btn btn-success" name="command" value="restart" style="margin-bottom: 0px;">Restart</button>
                                            <button type="submit" class="btn btn-success command" name="command" value="stop" style="margin-bottom: 0px;">Stop</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'elements/admin_footer.php';?>


