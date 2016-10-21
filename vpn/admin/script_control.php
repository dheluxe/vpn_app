<?php
//set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'phpseclib');
//set_include_path(get_include_path() . PATH_SEPARATOR . '/var/www/html/vpn/vpn/demovpn-new/app/includes/phpseclib');

function run_ssh_command($cmd)
{
    include('Net'.DIRECTORY_SEPARATOR.'SSH2.php');
    $ssh = new Net_SSH2(MAIN_SERVER_IP);
    if (!$ssh->login(MAIN_SERVER_SSH_USER_NAME, MAIN_SERVER_SSH_USER_PASS)) {
        return 'Login Failed';
    }
    $ssh->enableQuietMode();
    $result = '<b>killall phython:</b></br>';
    $result .=  nl2br($ssh->exec("killall -9 python"));
    $result .= '<b>killall php:</b></br>';
    $result .=  nl2br($ssh->exec("killall -9 php"));
    if(strcasecmp($cmd, "restart") == 0) {
        $result = $result . '<b>php test.php:</b></br>';
        $result = $result . nl2br($ssh->exec("cd /var/www/html/vpn/remote_server_script \r\n nohup php test.php > test.php.log.out 2> test.php.log.err < /dev/null &"));
        $result = $result . '<b>php server.php:</b></br>';
        $result = $result . nl2br($ssh->exec("cd /var/www/html/vpn/socket \r\n nohup php server.php > server.php.log.out 2> server.php.log.err < /dev/null &"));
        $result = $result . '<b>python socket_trigger.py:</b></br>';
        $result = $result . nl2br($ssh->exec("cd /var/www/html/vpn/socket \r\n nohup python socket_trigger1.py > socket_trigger1.py.log.out 2> socket_trigger1.py.log.err < /dev/null &"));
    }
    $ssh->disconnect();
    return $result;
}

if(!empty($_POST)){
    $check = trim($_POST['check']);
    if(!empty($check)) {
        $bashresult = run_ssh_command($command);
    }
    else {
        $command = trim($_POST['command']);
        if (!empty($command))
            $bashresult = run_ssh_command($command);
    }
}

$tphpsock = socket_create(AF_INET, SOCK_STREAM, 0);
$srvphpsock = socket_create(AF_INET, SOCK_STREAM, 0);
$pythonsock = socket_create(AF_INET, SOCK_STREAM, 0);

include 'elements/admin_header.php';
?>
<script type="text/javascript">
    $(document).ready( function() {
        aler("HURRA");
    });
</script>

<div class="row border-bottom">
    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
            <span class="sub-page-title">Service Control</span>
        </div>
    </nav>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row wrapper">
        <table class="footable table table-stripped toggle-arrow-tiny" data-page-size="8">
            <thead>
            <tr>
                <th>Service</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
                <tr id="testphp">
                    <td>test.php</td>
                    <td>
                        <?php if(socket_connect($tphpsock , MAIN_SERVER_IP, 7575)){
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
                        <?php if(socket_connect($srvphpsock , MAIN_SERVER_IP, 7272)){
                            echo '<span style="color: green;">Running</span>';
                        }
                        else
                        {
                            echo '<span style="color: red;">Down</span>';
                        }
                        ?>
                    </td>
                </tr>
                <tr id="strigger">
                    <td>socket_trigger.py</td>
                    <td>
                        <?php if(socket_connect($pythonsock , MAIN_SERVER_IP, 7373)){
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
                <button type="submit" class="btn btn-success" name="command" value="restart">Restart</button>
                <button type="submit" class="btn btn-success" name="command" value="stop">Stop</button>
            </div>
        </form>
    </div>
    <?php if(!empty($bashresult)): ?>
        <div class="row wrapper">
            <h3>Bash output:</h3>
            <div class="row wrapper" style="background-color: black; color: white">
                <?php echo $bashresult; ?>
            </div>
        </div>
    <?php endif;?>
</div>
         <?php include 'elements/admin_footer.php';?>


