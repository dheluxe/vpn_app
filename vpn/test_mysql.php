<?php
require "thread.php";
//require 'class.PHPWebSocket.php';
include '../includes/config.php';
include '../includes/connection.php';
require '../api/api_function.php';

$sql=$db->query("SELECT `id`,`server_name` FROM `remote_server_list` WHERE `remote_group`<>'a'");
$location_arr=array('aaa'=>"bbb");

while($row=$sql->fetch_assoc()){
    $location_arr[$row['id']] = $row['server_name'];
}
print_r($location_arr);
?>