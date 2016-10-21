<?php
//ob_start();
include 'elements/admin_header.php';

if(!empty($_POST)){
    $file = '../includes/config.php';
    $content = file_get_contents ($file);
    $str_to_find = "";
    $str_to_replace = "";
    $command = $_POST['command'];
    $str_to_replaceed="";
    if($command == "site")
    {
        $str_to_find = "'SITE_STATUS' => ".($config['SITE_STATUS'] ? "true" : "false");
        $str_to_replace = "'SITE_STATUS' => ".($config['SITE_STATUS'] ? "false" : "true");
        $str_to_replaceed=str_replace($str_to_find, $str_to_replace, $content);
        file_put_contents ($file, $str_to_replaceed);
        $config['SITE_STATUS']=$config['SITE_STATUS'] ? false : true;
    }
    else if($command == "reg")
    {
        $str_to_find = "'REG_STATUS' => ".($config['REG_STATUS']==1 ? "1" : "0");
        $str_to_replace = "'REG_STATUS' => ".($config['REG_STATUS']==1 ? "0" : "1");
        $str_to_replaceed=str_replace($str_to_find, $str_to_replace, $content);
        file_put_contents ($file, $str_to_replaceed);
        $config['REG_STATUS']=$config['REG_STATUS'] ? 0 : 1;
    }
    sleep(1);
    //ob_clean();
    //print_r($str_to_replaceed);
    $_POST=array();
    header('Location: ../vpn/admin/global_settings.php');
    //die;
}

?>
<div class="row border-bottom">
    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
            <span class="sub-page-title">Global Settings</span>
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
                <tr id="site_status">
                    <td>site status</td>
                    <td>
                        <?php echo $config['SITE_STATUS'] ? '<span style="color: green;">Online</span>' : '<span style="color: red;">Offline</span>' ?>
                    </td>
                    <td>
                        <form method="post">
                            <button type="submit" class="btn btn-success" name="command" value="site"><?php echo $config['SITE_STATUS'] ? 'Go offline' : 'Go online'; ?></button>
                        </form>

                    </td>
                </tr>
                <tr id="serverphp">
                    <td>registration status</td>
                    <td>
                        <?php echo $config['REG_STATUS'] ? '<span style="color: green;">Enabled</span>' : '<span style="color: red;">Disabled</span>' ?>
                    </td>
                    <td>
                        <form method="post">
                            <button type="submit" class="btn btn-success" name="command" value="reg"><?php echo $config['REG_STATUS'] ? 'Disable' : 'Enable'; ?></button>
                        </form>

                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php include 'elements/admin_footer.php';?>


