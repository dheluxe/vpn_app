<?php
/*require "thread.php";
require 'class.PHPWebSocket.php';*/
include '../includes/config.php';
include '../includes/connection.php';
require '../api/api_function.php';
$location_arr=array();

$sql=$db->query("SELECT `id`,`server_name` FROM `remote_server_list` WHERE `remote_group`<>'a'");
while($row=$sql->fetch_assoc()){
    $location_arr[$row['id']] = $row['server_name'];
}

$sql="INSERT INTO `job_queue` (`tunnel_id`,`action`,`new_data`,`old_data`,`remote_server_id`,`is_complete_action`,`group`,`token`,`is_seen`) VALUES ('".$tunnel_id."','".$action."','".$new_data."','".$old_data."','".$remote_server_id."','".$is_complete_action."','".$group."','".$token."','".$is_seen."')";


$id="";
$action="add_new_tunnel";
$group="a";
$token="7a0a9627ead232bf25a90c54fbac5999";

$tunnel=array();
$tunnel[0]['uname']="aaa";
$tunnel[0]['upass']="123456";
$tunnel[0]['cloud_id']=1;
$tunnel[0]['email']="kdcsev113@gmail.com";
$tunnel[0]['display_name']="";
$tunnel[0]['bidirectional_mode']=0;
$tunnel[0]['gateway_mode']=0;
$tunnel[0]['cloud_ip']="12.12.12.12";
$tunnel[0]['tunnel_type']="server";
$tunnel[0]['group_id']=0;
$tunnel[0]['username']="aaa";
$tunnel[0]['password']="123456";
$tunnel[0]['token']="7a0a9627ead232bf25a90c54fbac5999";
$sql="INSERT INTO `job_queue` (`tunnel_id`, `action`, `group`, `new_data`, `token`) VALUES ('".$id."', '".$action."', '".$group."', '".serialize($tunnel)."', '".$token."')";
/*
if($db->query($sql)){
    return true;
}
else{
    echo mysql_error();
}

print_r($location_arr);die;*/
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/*$cloud_id="8";
$token="7a0a9627ead232bf25a90c54fbac5999";
$tunnel = "SELECT `tunnels_data`.*, `remote_server_list`.`server_name` `location`, `real_ip_list`.`is_active` `active` FROM `tunnels_data` left join `real_ip_list` on `tunnels_data`.`real_ip`=`real_ip_list`.`real_ip` left join `remote_server_list` on `tunnels_data`.`location`=`remote_server_list`.`id` WHERE `tunnels_data`.`cloud_id`='".$db->real_escape_string($cloud_id)."' and `tunnels_data`.`user_token`='".$db->real_escape_string($token)."' and `tunnels_data`.`is_deleted`=0";
$sql=$db->query($tunnel." order by group_id asc, group_id");
$data=array();
while($row=$sql->fetch_assoc()){
    $data[]=$row;
}
print_r($data);*/

$tunnel_id=$_REQUEST['job_id'];
/*$acl_data=get_acl_info($tunnel_id);
print_r(json_encode($acl_data));die;*/


$db = mysql_connect($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME']) or die(mysql_error());
mysql_select_db($config['DB_NAME'], $db) or die(mysql_error());

$sql_job=mysql_query("SELECT * FROM `job_queue` WHERE `job_id`=1109");
$res_job=mysql_fetch_assoc($sql_job);
$arr_val=unserialize($res_job['new_data']);

/*
$last_id = 116;
$arr_val['tunnel']['tunnel_id']=$last_id;
print_r($arr_val['acl_info']);
print_r("<br/>");
foreach($arr_val['acl_info'] as $key => $value){
    if(mysql_query("INSERT INTO `tunnel_acl_relation` SET `tunnel_id`=".$arr_val['tunnel']['tunnel_id'])){
        $last_id = mysql_insert_id();
        $table="";
        $field="";
        $arr=array();
        foreach ($value as $k => $val) {
            $table=$k;
            if(is_array($val)){
                foreach ($val as $t => $v) {
                    if(($table=="source" || $table=="destination") && $t=="specific_tunnel" && $v['value']==96){
                        $field.=" `".$table."-".$t."` = '".$arr_val['tunnel']['tunnel_id']."', ";
                    }else{
                        $field.=" `".$table."-".$t."` = '".$v['value']."', ";
                    }
                }
                $query="INSERT INTO `".$table."` SET `acl_id` = ".$last_id.", ".rtrim($field, ", ");
                print_r($query);
                print_r("<br/>");

                mysql_query($query);
                $field="";
            }
        }
    }
}*/
/*
$sql="INSERT INTO `tunnels_data`(`type`,`email`,`internet_tunnel`,`cloud_id`,`user_token`,`status`,`servers_ACL`,`last_login`,`traffic`,`bidirectional_mode`,`group_id`,`display_name`,`is_deleted`,`last_updated`,`no_of_revision`, `location`, `is_complete_action`, `is_updated`, `tunnel_type`, `plan_id`) VALUES(
                    ".$arr_val['tunnel']['type'].",
                    '".$arr_val['tunnel']['email']."',
                    ".$arr_val['tunnel']['internet_tunnel'].",
                    ".$arr_val['tunnel']['cloud_id'].",
                    '".$arr_val['tunnel']['user_token']."',
                    ".$arr_val['tunnel']['status'].",
                    '".$arr_val['tunnel']['servers_ACL']."',
                    now(),
                    ".$arr_val['tunnel']['traffic'].",
                    ".$arr_val['tunnel']['bidirectional_mode'].",
                    ".$arr_val['tunnel']['group_id'].",
                    '".$arr_val['tunnel']['display_name']."',
                    0,
                    now(),
                    0,
                    '".$arr_val['tunnel']['location']."', 0, 0, 'client',
                    ".$arr_val['tunnel']['plan_id']."
                    )";
print_r($sql);
print_r('<br/>');

$qry=mysql_query($sql) or die(mysql_error());
$last_id = mysql_insert_id();
print_r($last_id);
print_r('<br/>');
$parent_tunnel_id=$arr_val['tunnel']['tunnel_id'];
$arr_val['tunnel']['tunnel_id']=$last_id;
$sqlPackage=mysql_query("SELECT SUM(`tunnel` + `bidirection`) total FROM `package_data` WHERE `plan_id`=".$arr_val['tunnel']['plan_id']);
$rowPackage=mysql_fetch_assoc($sqlPackage);
$arr_val['tunnel']['cost']=$rowPackage['total'];
mysql_query("UPDATE `client_subnets` SET `used_ips`=1 WHERE `subnet`='".$arr_val['tunnel']['cloud_ip']."'");
foreach($arr_val['acl_info'] as $key => $value){
    if(mysql_query("INSERT INTO `tunnel_acl_relation` SET `tunnel_id`=".$arr_val['tunnel']['tunnel_id'])){
        $last_id = mysql_insert_id();
        $table="";
        $field="";
        $arr=array();
        foreach ($value as $k => $val) {
            $table=$k;
            if(is_array($val)){
                foreach ($val as $t => $v) {
                    if(($table=="source" || $table=="destination") && $t=="specific_tunnel" && $v['value']==$parent_tunnel_id){
                        $field.=" `".$table."-".$t."` = '".$arr_val['tunnel']['tunnel_id']."', ";
                    }else{
                        $field.=" `".$table."-".$t."` = '".$v['value']."', ";
                    }
                }
                $query="INSERT INTO `".$table."` SET `acl_id` = ".$last_id.", ".rtrim($field, ", ");
                mysql_query($query);
                $field="";
            }
        }
    }
}*/

/*
$tunnel = "SELECT `tunnels_data`.*, `remote_server_list`.`server_name` `location`, `real_ip_list`.`is_active` `active` FROM `tunnels_data` left join `real_ip_list` on `tunnels_data`.`real_ip`=`real_ip_list`.`real_ip` left join `remote_server_list` on `tunnels_data`.`location`=`remote_server_list`.`id` WHERE `tunnels_data`.`tunnel_id`=104";
$sql=mysql_query($tunnel." order by group_id asc, group_id");
$data=array();
while($row=mysql_fetch_assoc($sql)){
    $data[]=$row;
}
print_r($data);*/

$cloud_id=15;
$group_ids=array();
$sql="select `group_id` from `tunnels_data` where `cloud_id`=".$cloud_id." group by `group_id` order by `group_id`";
$res2=mysql_query($sql);
while($row=mysql_fetch_assoc($res2)){
    if(count($row)>0){
        $group_ids[]=$row['group_id'];
    }
}
print_r($group_ids);
?>
