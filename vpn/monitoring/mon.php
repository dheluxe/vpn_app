<?php
/*include '/var/www/html/vpn/includes/phpseclib/Net/SSH2.php';
$ssh = new Net_SSH2('198.211.127.72');
if (!$ssh->login('root', 'kdcsev113@pass')) {
    exit('Login Failed');
}
echo "success";
*/?>

<?php
include '/var/www/html/vpn/includes/config.php';
include '/var/www/html/vpn/includes/connection.php';
require '/var/www/html/vpn/api/api_function.php';

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'phpseclib');
include_once('Net/SSH2.php');

$srv = shell_exec("status serverphp_runner");
if(strpos($srv, 'stop/waiting') !== false) {
    shell_exec("start serverphp_runner");
}
/*
$strig = shell_exec("status socket_trigger_runner");
if(strpos($strig, 'stop/waiting') !== false) {
    shell_exec("start socket_trigger_runner");
}*/
$strig = shell_exec("status deduct_cash_runner");
if(strpos($strig, 'stop/waiting') !== false) {
    shell_exec("start deduct_cash_runner");
}

$strig2 = shell_exec("status process_complete_runner");
if(strpos($strig2, 'stop/waiting') !== false) {
    shell_exec("start process_complete_runner");
}

$db = new mysqli($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME']);
$servers = $db->query('SELECT * FROM `remote_server_list` WHERE `is_monitored` = 1');

while($server=$servers->fetch_assoc())
{
    $utime = date("Y-m-d H:i:s");
    $statusmsg = "";
    $ip = trim($server['remote_ip']);
    $ssh_username = trim($server['ssh_username']);
    $ssh_password = trim($server['ssh_password']);

    //print_r($ip);
    try {
        $ssh = new Net_SSH2($ip);
        if (!$ssh->login($ssh_username, $ssh_password)) {
            $db->query("UPDATE `remote_server_list` SET `current_status`= 'Login Failed' WHERE `id` = " . $server['id']);
            continue;
        }
    }
    catch (Exception $e)
    {
        $db->query("UPDATE `remote_server_list` SET `current_status`= 'NOT ACCESIBLE' WHERE `id` = " . $server['id']);
        continue;
    }
    $running = true;
    $r = $ssh->exec("status test_runner");
    if (strpos($r, 'Unknown job') !== false) {
        $running = false;
        $statusmsg .= "no test_runner found! <br/>";
    } else if(strpos($r, 'stop/waiting') !== false) {
        $sr = $ssh->exec("start test_runner");
        if(strpos($sr, 'start/running') === false)
        {
            $statusmsg .= "Could not start test_runner <br/>";
            $running = false;
        }
    }

    if($running)
    {
        $statusmsg .= "test_runner OK";
    }
    $rs = $ssh->exec("php /var/resmon.php");
    //print_r($rs);
    $db->query("UPDATE `remote_server_list` SET `last_alive`='" . $utime . "',`ressnap`='" . $rs . "',`current_status`= '" . $statusmsg . "' WHERE `id` = " . $server['id']);
}
?>