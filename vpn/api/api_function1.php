<?php
function remote_server_delete($data){
    set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'phpseclib');
    include_once(ROOT_PATH.'/includes/phpseclib/Net/SSH2.php');
    include_once(ROOT_PATH.'/includes/phpseclib/Net/SFTP.php');
    global $db;

    $remote_data_query=$db->query("SELECT * FROM `remote_server_list` WHERE `id`=".$data['id']);
    if($remote_data_query->num_rows>0){
        $remote_data=$remote_data_query->fetch_assoc();
        $ip=$remote_data['remote_ip'];
        $ssh_username=$remote_data['ssh_username'];
        $ssh_password=$remote_data['ssh_password'];

        $ssh = new Net_SSH2($ip);
        if (!$ssh->login($ssh_username, $ssh_password)) {
            $db->query("DELETE FROM `remote_server_list` WHERE `id`=".$data['id']);
            return 1;
        }
        $ssh->exec("stop test_runner");

        $local_directory = dirname(dirname(__FILE__))."/backend_jobs/";
        $remote_test_file="test.php";
        $remote_thread_file="thread.php";
        $remote_resmon_file="resmon.php";
        $remote_directory = '/var/';

        $conf_file="test_runner.conf";
        $conf_directory = '/etc/init/';

        $ssh->exec("rm ".$conf_directory.$conf_file);
        $ssh->exec("rm ".$remote_directory.$remote_test_file);
        $ssh->exec("rm ".$remote_directory.$remote_thread_file);
        $ssh->exec("rm ".$remote_directory.$remote_resmon_file);

        $db->query("DELETE FROM `remote_server_list` WHERE `id`=".$data['id']);
        return 1;
    }
}
function test_all_remote(){
  global $db;
  $arr=array();
  $sql=$db->query("SELECT * FROM `remote_server_list`");
  if($sql->num_rows>0){
      while($res=$sql->fetch_assoc()){
          /*$conn = new mysqli($res['remote_ip'], $res['server_uname'], $res['server_pass']);
          if ($conn->connect_error) {
              if($db->query("UPDATE `remote_server_list` SET `is_active`=0 WHERE `id`=".$res['id'])){
                  $arr[$res["id"]]=array("result"=>0);
              }
          }else{
              if($db->query("UPDATE `remote_server_list` SET `is_active`=1 WHERE `id`=".$res['id'])){
                  $arr[$res["id"]]=array("result"=>1);
              }
          }
          $subject ='Remote Server';
           $message ='<html>
               <head>
                   <title>"'.$subject.'"</title>
               </head>
               <body>
                    Dear sir/madam,<br>
                        "'.$res['remote_ip'].'" Remote server status has been changed, please check.<br><br>
                    Thank you,<br>
                    Demovpn team.
               </body>
           </html>';
           $headers = "MIME-Version: 1.0" . "\r\n";
           $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
           $headers .= 'From:DemoVPN <demovpn@comenzarit.com>' . "\r\n";
           mail($_POST['email'],$subject,$message,$headers);
          $conn->close();*/
      }
      return $arr;
  }
}
function get_acl_detail($data){
    $acl_id=$data['id'];
    global $db;

    $main_query="SELECT * FROM `tunnel_acl_relation`
        JOIN `destination` ON `tunnel_acl_relation`.`id` = `destination`.`acl_id`
        JOIN `d_final` ON `tunnel_acl_relation`.`id` = `d_final`.`acl_id`
        JOIN `c_firewall` ON `tunnel_acl_relation`.`id` = `c_firewall`.`acl_id`
        JOIN `c_forwarding` ON `tunnel_acl_relation`.`id` = `c_forwarding`.`acl_id`
        JOIN `c_qos` ON `tunnel_acl_relation`.`id` = `c_qos`.`acl_id`
        JOIN `c_routing` ON `tunnel_acl_relation`.`id` = `c_routing`.`acl_id`
        JOIN `source` ON `tunnel_acl_relation`.`id` = `source`.`acl_id`
        JOIN `s_firewall` ON `tunnel_acl_relation`.`id` = `s_firewall`.`acl_id`
        JOIN `s_aliasing` ON `tunnel_acl_relation`.`id` = `s_aliasing`.`acl_id`
        JOIN `s_qos` ON `tunnel_acl_relation`.`id` = `s_qos`.`acl_id`
        JOIN `s_tos` ON `tunnel_acl_relation`.`id` = `s_tos`.`acl_id`
        WHERE `tunnel_acl_relation`.`id`=".$acl_id;
    $res1 = $db->query($main_query);
    $value = $res1->fetch_assoc();

    $acl_info = array();

    $id = $value['acl_id'];
    $acl_info[$id]=array();
    $acl_info[$id]["tunnel_id"]=$value['tunnel_id'];

    $acl_info[$id]["acl_name"]=$value['acl_name'];
    $acl_info[$id]["acl_description"]=$value['acl_description'];
    $acl_info[$id]["is_searchable"]=$value['is_searchable'];

    unset($value['id']);
    unset($value['acl_id']);
    unset($value['tunnel_id']);
    unset($value['creation_time']);
    unset($value['is_active']);
    unset($value['acl_name']);
    unset($value['acl_description']);
    unset($value['is_searchable']);

    foreach ($value as $k => $val) { //k: this is database name
        $key = explode("-", $k);
        $base = $key[0];
        if(!isset($acl_info[$id][$base])){
            $acl_info[$id][$base]=array();
        }
        $label = ucwords(str_replace('_', ' ', $key[1]));
        $acl_info[$id][$base][$key[1]] = array('label'=>readLabel($key), 'value'=>$val);
    }
    return $acl_info[$id];
}
function get_acl_info($id){
    global $db;
    $default_acl_id=0;
    $default_acl_res = $db->query("SELECT `default_acl` FROM `tunnels_data` WHERE `tunnel_id`=".$id);
    if($default_acl_res->num_rows>0){
        $default_acl=$default_acl_res->fetch_assoc();
        $default_acl_id=$default_acl['default_acl'];
    }

    $arr=array();
    $cur_tunnel_id=$id;
    $res = $db->query("SELECT `id` FROM `tunnel_acl_relation` WHERE `tunnel_id`=".$id);
    $val = '';
    $res_shared_acl = $db->query("SELECT `acl_id` FROM `user_acl_relation` WHERE `tunnel_id`=".$id);
    $val_shared_acl="";
    if($res->num_rows > 0 || $res_shared_acl->num_rows > 0){
      while($row = $res->fetch_assoc()){
          $val.=$row['id'].",";
      }
      while($row_shared_acl = $res_shared_acl->fetch_assoc()){
          $val.=$row_shared_acl['acl_id'].",";
      }

      $val=rtrim($val, ",");

        //print_r($val);

      $main_query="SELECT * FROM `tunnel_acl_relation`
            JOIN `destination` ON `tunnel_acl_relation`.`id` = `destination`.`acl_id`
            JOIN `d_final` ON `tunnel_acl_relation`.`id` = `d_final`.`acl_id`
            JOIN `c_firewall` ON `tunnel_acl_relation`.`id` = `c_firewall`.`acl_id`
            JOIN `c_forwarding` ON `tunnel_acl_relation`.`id` = `c_forwarding`.`acl_id`
            JOIN `c_qos` ON `tunnel_acl_relation`.`id` = `c_qos`.`acl_id`
            JOIN `c_routing` ON `tunnel_acl_relation`.`id` = `c_routing`.`acl_id`
            JOIN `source` ON `tunnel_acl_relation`.`id` = `source`.`acl_id`
            JOIN `s_firewall` ON `tunnel_acl_relation`.`id` = `s_firewall`.`acl_id`
            JOIN `s_aliasing` ON `tunnel_acl_relation`.`id` = `s_aliasing`.`acl_id`
            JOIN `s_qos` ON `tunnel_acl_relation`.`id` = `s_qos`.`acl_id`
            JOIN `s_tos` ON `tunnel_acl_relation`.`id` = `s_tos`.`acl_id`
            WHERE `tunnel_acl_relation`.`id`
            IN (".$val.")";
      $res1 = $db->query($main_query);
      while($row1 = $res1->fetch_assoc()){
          $arr[] = $row1;
      }
        //print_r($arr);
      $acl_info = array();
      foreach ($arr as $key => $value) {

          $id = $value['acl_id'];
          $acl_info[$id]=array();
          $acl_info[$id]["tunnel_id"]=$value['tunnel_id'];
          $acl_info[$id]["is_installed"]=is_acl_installed($cur_tunnel_id,$value['acl_id']);

          if($acl_info[$id]["is_installed"]==1){
              $acl_info[$id]["is_subscribed"] = 0;
          }else{
              $acl_info[$id]["is_subscribed"]=is_acl_subscribed($cur_tunnel_id,$value['acl_id']);
          }

          $acl_info[$id]["status"]=check_acl_status($cur_tunnel_id,$value['acl_id']);
          $acl_info[$id]["acl_name"]=$value['acl_name'];
          $acl_info[$id]["acl_description"]=$value['acl_description'];
          $acl_info[$id]["is_searchable"]=$value['is_searchable'];
          $acl_info[$id]["default_acl_id"]=$default_acl_id;

          unset($value['id']);
          unset($value['acl_id']);
          unset($value['tunnel_id']);
          unset($value['creation_time']);
          unset($value['is_active']);
          unset($value['acl_name']);
          unset($value['acl_description']);
          unset($value['is_searchable']);

          foreach ($value as $k => $val) { //k: this is database name
            $key = explode("-", $k);
            $base = $key[0];
            if(!isset($acl_info[$id][$base])){
                $acl_info[$id][$base]=array();
            }
            $label = ucwords(str_replace('_', ' ', $key[1]));
            //if($val>0){
                $acl_info[$id][$base][$key[1]] = array('label'=>readLabel($key), 'value'=>$val);
            //}
          }
      }
      //print_r($acl_info);die;
      return $acl_info;
  }
  else{
      return array('status' =>0 , 'message'=>'No ACL found for this Tunnel');
  }

}
function get_own_acl_info($id){
    global $db;
    $arr=array();
    $cur_tunnel_id=$id;
    $res = $db->query("SELECT `id` FROM `tunnel_acl_relation` WHERE `tunnel_id`=".$id);
    $val = '';
    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            $val.=$row['id'].",";
        }

        $val=rtrim($val, ",");

        $main_query="SELECT * FROM `tunnel_acl_relation`
            JOIN `destination` ON `tunnel_acl_relation`.`id` = `destination`.`acl_id`
            JOIN `d_final` ON `tunnel_acl_relation`.`id` = `d_final`.`acl_id`
            JOIN `c_firewall` ON `tunnel_acl_relation`.`id` = `c_firewall`.`acl_id`
            JOIN `c_forwarding` ON `tunnel_acl_relation`.`id` = `c_forwarding`.`acl_id`
            JOIN `c_qos` ON `tunnel_acl_relation`.`id` = `c_qos`.`acl_id`
            JOIN `c_routing` ON `tunnel_acl_relation`.`id` = `c_routing`.`acl_id`
            JOIN `source` ON `tunnel_acl_relation`.`id` = `source`.`acl_id`
            JOIN `s_firewall` ON `tunnel_acl_relation`.`id` = `s_firewall`.`acl_id`
            JOIN `s_aliasing` ON `tunnel_acl_relation`.`id` = `s_aliasing`.`acl_id`
            JOIN `s_qos` ON `tunnel_acl_relation`.`id` = `s_qos`.`acl_id`
            JOIN `s_tos` ON `tunnel_acl_relation`.`id` = `s_tos`.`acl_id`
            WHERE `tunnel_acl_relation`.`id`
            IN (".$val.")";
        $res1 = $db->query($main_query);
        while($row1 = $res1->fetch_assoc()){
            $arr[] = $row1;
        }
        $acl_info = array();
        foreach ($arr as $key => $value) {
            $id = $value['id'];
            $acl_info[$id]=array();
            $acl_info[$id]["tunnel_id"]=$value['tunnel_id'];

            unset($value['id']);
            unset($value['acl_id']);
            unset($value['tunnel_id']);
            unset($value['creation_time']);
            unset($value['is_active']);

            foreach ($value as $k => $val) {
                $key = explode("-", $k);
                $base = $key[0];
                if(!isset($acl_info[$id][$base])){
                    $acl_info[$id][$base]=array();
                }
                $label = ucwords(str_replace('_', ' ', $key[1]));
                $acl_info[$id][$base][$key[1]] = array('label'=>readLabel($key), 'value'=>$val);
            }
        }
        return $acl_info;
    }
    else{
        return array();
    }

}
function readLabel($key) {
    $label = ucwords(str_replace('_', ' ', $key[1]));
    $result = array(
     'full' => $label,
     'short' => ucwords(mb_substr($key[1], 0, 1, 'utf-8'))
     );
    if($key[0] == 'destination')
    {
        if($key[1] == 'real_ip'){
            $result = array(
                'full' => 'Real Ip',
                'short' => 'R'
            );
        }elseif($key[1] == 'this_tunnel'){
            $result = array(
                'full' => 'This tunnel',
                'short' => 'T'
            );
        }

    }
    elseif($key[0] == 'source' && $key[1] == 'my_cloud')
    {
        if($key[1] == 'my_cloud')
            $result = array(
                'full' => 'this cloud',
                'short' => 'T'
            );
    }
    else if($key[0] == 'source')
    {
        if($key[1] == 'my_cloud')
            $result = array(
                'full' => $label,
                'short' => 'C'
            );
        if($key[1] == 'specific_tunnel')
            $result = array(
                'full' => $label,
                'short' => 'S'
            );
        if($key[1] == 'specific_group')
            $result = array(
                'full' => $label,
                'short' => 'G'
            );
    }
    else if($key[1] == 'country')
        $result = array(
            'full' => 'Path',
            'short' => 'P'
        );
    return $result;
}
function create_new_acl($data){
    global $db;
    $res=remote($data['id'], "create_new_acl", $data, "a", $data["token"]);
    if($res==1){
      return array("toclient"=>$_SESSION['token'], "status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'create_new_acl', 'value'=>$data['id']);
    }
}
function acl_update($data){
  global $db;
  if($db->query("UPDATE `".$data['type']."` SET `".$data['type']."-".$data['name']."`='".$data['val']."' WHERE `acl_id`=".$data['id'])){
      return true;
  }
}
function save_acl_name_description($data){
    global $db;
    $sql="UPDATE `tunnel_acl_relation` SET `".$data['field']."`='".$data['value']."' WHERE `id`=".$data['acl_id'];
    if($db->query($sql)){
        return true;
    }
    return false;
}
function change_searchable($data){
    global $db;
    $sql="";
    if($data['database']=="customers_data"){
        $sql="UPDATE `".$data['database']."` SET `".$data['field']."`='".$data['value']."' WHERE `customer_id`=".$data['id'];
    }elseif($data['database']=="clouds_data"){
        $sql="UPDATE `".$data['database']."` SET `".$data['field']."`='".$data['value']."' WHERE `cloud_id`=".$data['id'];
    }elseif($data['database']=="tunnels_data"){
        $sql="UPDATE `".$data['database']."` SET `".$data['field']."`='".$data['value']."' WHERE `tunnel_id`=".$data['id'];
    }elseif($data['database']=="tunnel_acl_relation"){
        $sql="UPDATE `".$data['database']."` SET `".$data['field']."`='".$data['value']."' WHERE `id`=".$data['id'];
    }
    if($sql==""){
        return false;
    }

    if($db->query($sql)){
        return true;
    }
    return false;
}
function get_acl_val($data){
  global $db;
    if($data['type']=="destination" && $data['name']=="real_ip"){
        $real_ips=array();
        $installed_real_ips=array();
        $current_real_ip=0;
        $tunnel_id=0;
        $acl_ids=array();
        $installed_acl_ids=array();
        $sql="select td.* from tunnels_data as td, tunnel_acl_relation as tar where tar.tunnel_id=td.tunnel_id and tar.id='".$data['id']."' and td.is_deleted='0'";
        $res=$db->query($sql);
        $result=$res->fetch_assoc();
        if(count($result)>0){
            if($result['real_ip']!=""){
                $real_ips[]=$result['real_ip'];
            }
            $tunnel_id=$result['tunnel_id'];
        }
        $sql="select id from tunnel_acl_relation where tunnel_id='".$tunnel_id."'";
        $res=$db->query($sql);
        while($row=$res->fetch_assoc()){
            $acl_ids[]=$row['id'];
        }
        if(count($acl_ids)>0){
            $acl_ids_str=implode(",",$acl_ids);
            $sql="SELECT `destination-real_ip` FROM `destination` WHERE `acl_id` IN (".$acl_ids_str.") GROUP BY `destination-real_ip`";
            $res=$db->query($sql);
            while($row=$res->fetch_assoc()){
                if(!in_array($row['destination-real_ip'],$real_ips)){
                    if($row['destination-real_ip']!=""){
                        $real_ips[]=$row['destination-real_ip'];
                    }
                }
            }
        }

/*        $sql="select * from `tunnels_data` where `is_deleted`='1'";
        $res=$db->query($sql);
        if($res->num_rows>0){
            while($row=$res->fetch_assoc()){
                $tunnel_id=$row['tunnel_id'];
                $sql3="select * from `tunnel_acl_relation` where `tunnel_id`='".$tunnel_id."'";
                $res3=$db->query($sql3);
                while($row3=$res3->fetch_assoc()){
                    $acl_id=$row3['id'];

                    $sql4="delete from `s_tos` where `acl_id`=$acl_id";
                    $db->query($sql4);
                    $sql4="delete from `s_qos` where `acl_id`=$acl_id";
                    $db->query($sql4);
                    $sql4="delete from `s_firewall` where `acl_id`=$acl_id";
                    $db->query($sql4);
                    $sql4="delete from `s_aliasing` where `acl_id`=$acl_id";
                    $db->query($sql4);
                    $sql4="delete from `source` where `acl_id`=$acl_id";
                    $db->query($sql4);
                    $sql4="delete from `d_final` where `acl_id`=$acl_id";
                    $db->query($sql4);
                    $sql4="delete from `destination` where `acl_id`=$acl_id";
                    $db->query($sql4);
                    $sql4="delete from `c_routing` where `acl_id`=$acl_id";
                    $db->query($sql4);
                    $sql4="delete from `c_qos` where `acl_id`=$acl_id";
                    $db->query($sql4);
                    $sql4="delete from `c_forwarding` where `acl_id`=$acl_id";
                    $db->query($sql4);
                    $sql4="delete from `c_firewall` where `acl_id`=$acl_id";
                    $db->query($sql4);

                }

                $sql4="delete from `tunnel_acl_relation` where `tunnel_id`='".$tunnel_id."'";
                $db->query($sql4);
                $sql4="delete from `user_acl_relation` where `tunnel_id`='".$tunnel_id."'";
                $db->query($sql4);
            }
        }
        $sql4="delete from `tunnels_data` where `is_deleted`='1'";
        $db->query($sql4);
        print_r("end:");die;*/

        $sql="select `acl_id` from `user_acl_relation` where `tunnel_id`='".$tunnel_id."' and `status`='1'";
        $res=$db->query($sql);
        if($res->num_rows>0){
            while($row=$res->fetch_assoc()){
                $installed_acl_ids[]=$row['acl_id'];
            }
        }

        if(count($installed_acl_ids)>0){
            $acl_ids_str=implode(",",$installed_acl_ids);
            $sql="SELECT `destination-real_ip` FROM `destination` WHERE `acl_id` IN (".$acl_ids_str.") GROUP BY `destination-real_ip`";
            $res=$db->query($sql);
            while($row=$res->fetch_assoc()){
                if(!in_array($row['destination-real_ip'],$installed_real_ips)){
                    if($row['destination-real_ip']!=""){
                        $installed_real_ips[]=$row['destination-real_ip'];
                    }
                }
            }
        }

        $sql="SELECT `destination-real_ip` FROM `destination` WHERE `acl_id`=".$data['id'];
        $res=$db->query($sql);
        $row=$res->fetch_assoc();
        $current_real_ip=$row['destination-real_ip'];
        return json_encode(array('real_ips'=>$real_ips,'installed_real_ips'=>$installed_real_ips,'cur_real_ip'=>$current_real_ip));
    }else{
        $res = $db->query("SELECT `".$data['type']."-".$data['name']."` FROM `".$data['type']."` WHERE `acl_id`=".$data['id']);
        $result = $res->fetch_assoc();
        return $result[$data['type']."-".$data['name']];
    }

}
function chk_res($data){
  global $db;
  $arr = array();
  $res = $db->query("SELECT `".$data['type']."-".$data['val']."` FROM `".$data['type']."` WHERE `acl_id`=".$data['id']);
  $result = $res->fetch_assoc();
  $arr['option_val'] = $result[$data['type']."-".$data['val']];

  if($data['type']=="destination"){
    $data['type']="source";
  } else if($data['type']=="source"){
    $data['type']="destination";
  }
  $res1 = $db->query("SELECT `".$data['type']."-".$data['val']."` FROM `".$data['type']."` WHERE `acl_id`=".$data['id']);
  $result = $res1->fetch_assoc();
  $arr['exist_tunnel'] = $result[$data['type']."-".$data['val']];

    $tunnel_id=$_REQUEST['tunnel'];
    $sql="select `cloud_id` from `tunnels_data` where `tunnel_id`=".$tunnel_id;
    $res2=$db->query($sql);
    $result=$res2->fetch_assoc();
    $cloud_id=$result['cloud_id'];
    $group_ids=array();
    $sql="select `group_id` from `tunnels_data` where `cloud_id`=".$cloud_id." group by `group_id` order by `group_id`";
    $res2=$db->query($sql);
    while($row=$res2->fetch_assoc()){
        if(count($row)){
            $group_ids[]=$row['group_id'];
        }
    }
    $arr['cloud_group_ids'] = $group_ids;
  return $arr;
}
function create_acl_clone($data){
    global $db;
    $arr=array();
    $acl_info = array();

    $res1 = $db->query("SELECT *FROM `tunnel_acl_relation`
          JOIN `destination` ON `tunnel_acl_relation`.`id` = `destination`.`acl_id`
          JOIN `d_final` ON `tunnel_acl_relation`.`id` = `d_final`.`acl_id`
          JOIN `c_firewall` ON `tunnel_acl_relation`.`id` = `c_firewall`.`acl_id`
          JOIN `c_forwarding` ON `tunnel_acl_relation`.`id` = `c_forwarding`.`acl_id`
          JOIN `c_qos` ON `tunnel_acl_relation`.`id` = `c_qos`.`acl_id`
          JOIN `c_routing` ON `tunnel_acl_relation`.`id` = `c_routing`.`acl_id`
          JOIN `source` ON `tunnel_acl_relation`.`id` = `source`.`acl_id`
          JOIN `s_firewall` ON `tunnel_acl_relation`.`id` = `s_firewall`.`acl_id`
          JOIN `s_aliasing` ON `tunnel_acl_relation`.`id` = `s_aliasing`.`acl_id`
          JOIN `s_qos` ON `tunnel_acl_relation`.`id` = `s_qos`.`acl_id`
          JOIN `s_tos` ON `tunnel_acl_relation`.`id` = `s_tos`.`acl_id`
          WHERE `tunnel_acl_relation`.`id`=".$data['id']);
    while($row1 = $res1->fetch_assoc()){
        $arr[] = $row1;
    }


    foreach ($arr as $key => $value) {

        $id = $value['id'];
        $acl_info[$id]=array();
        $acl_info[$id]["tunnel_id"]=$value['tunnel_id'];
        unset($value['id']);
        unset($value['acl_id']);
        unset($value['tunnel_id']);
        unset($value['creation_time']);
        unset($value['is_active']);

        foreach ($value as $k => $val) {
          $key = explode("-", $k);
          $base = $key[0];
          if(!isset($acl_info[$id][$base])){
              $acl_info[$id][$base]=array();
          }
          $label = ucwords(str_replace('_', ' ', $key[1]));
          //if($val>0){
              $acl_info[$id][$base][$key[1]] = array('label'=>$label, 'value'=>$val);
          //}
        }
    }
    //print_r($acl_info);die;

    $res=remote($data['tid'], "create_acl_clone", $acl_info, "a", $data["token"]);
    if($res==1){
      return array("toclient"=>$_SESSION['token'], "status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'create_acl_clone', 'value'=>$data['tid']);
    }
}
function delete_acl($data){
    global $db;

    $res=remote($data['tid'], "delete_acl", $data, "a", $data["token"]);
    if($res==1){
      return array("toclient"=>$_SESSION['token'], "status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'delete_acl', 'value'=>$data['id']);
    }
    /*if($db->query("DELETE FROM `tunnel_acl_relation` WHERE `id`=".$id)){
        if($db->query("DELETE FROM `destination` WHERE `acl_id`=".$id)){
            if($db->query("DELETE FROM `d_final` WHERE `acl_id`=".$id)){
                if($db->query("DELETE FROM `c_firewall` WHERE `acl_id`=".$id)){
                    if($db->query("DELETE FROM `c_forwarding` WHERE `acl_id`=".$id)){
                        if($db->query("DELETE FROM `c_qos` WHERE `acl_id`=".$id)){
                            if($db->query("DELETE FROM `c_routing` WHERE `acl_id`=".$id)){
                                if($db->query("DELETE FROM `source` WHERE `acl_id`=".$id)){
                                    if($db->query("DELETE FROM `s_aliasing` WHERE `acl_id`=".$id)){
                                        if($db->query("DELETE FROM `s_firewall` WHERE `acl_id`=".$id)){
                                            if($db->query("DELETE FROM `s_qos` WHERE `acl_id`=".$id)){
                                                if($db->query("DELETE FROM `s_tos` WHERE `acl_id`=".$id)){
                                                   return true;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }*/
}
function clear_acl_values($data){
    global $db;

    $res=remote($data['tid'], "clear_acl_values", $data, "a", $data["token"]);
    if($res==1){
      return array("toclient"=>$_SESSION['token'], "status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'clear_acl_values', 'value'=>$data['id']);
    }
}
function save_acl_values($data){
    //print_r($data);
    global $db;
    if(array_key_exists($data['id'],$data['data'])){
        $res=remote($data['tid'], "save_acl_values", $data, "a", $data["token"]);
        if($res==1){
            return array("toclient"=>$_SESSION['token'], "status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'save_acl_values', 'value'=>$data['id']);
        }
    }else{
        return array("toclient"=>$_SESSION['token'], "status" => 0, 'data' => 'Your request either submitted, or not changed yet, please try again', 'message_type'=>'reply', 'type'=>'save_acl_values', 'value'=>$data['id']);
    }

}
function change_acl($data){
    global $db;

    $res=remote($data['tid'], "change_acl", $data, "a", $data["token"]);
    if($res==1){
      return array("toclient"=>$_SESSION['token'], "status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'change_acl', 'value'=>$data['id']);
    }
}
function set_default_acl($data){
    global $db;
    $query=$db->query("UPDATE `tunnels_data` SET `default_acl`=".$data['id']." WHERE `tunnel_id`=".$data['tid']);

        return array("toclient"=>$_SESSION['token'], "status" => 1, 'data' => 'This acl is set as default.', 'message_type'=>'reply', 'type'=>'set_default_acl', 'value'=>$data['id']);

    /*$res=remote($data['tid'], "set_default_acl", $data, "a", $data["token"]);
    if($res==1){
        return array("toclient"=>$_SESSION['token'], "status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'change_acl', 'value'=>$data['id']);
    }*/

}
function point($data){
  global $db;
  $point_set = $db->query("SELECT `settings_value` FROM `settings` WHERE `settings_name`='cast_to_point'");
  $res_point = $point_set->fetch_assoc();

  $res = $db->query("SELECT * FROM `customers_data` WHERE `Cash_amount` >= ".$data['point']/$res_point['settings_value']." AND `customer_id`=".$data['id']);
  if($res->num_rows>0){
    $cust_chk = $db->query("SELECT * FROM `customers_data` WHERE `email`='".$data['email']."'");
    if($cust_chk->num_rows>0){
      if($db->query("UPDATE `customers_data` SET `Cash_amount` = (`Cash_amount`)+".$data['point']/$res_point['settings_value']." WHERE `email`='".$data['email']."'")){
          if($db->query("UPDATE `customers_data` SET `Cash_amount` = (`Cash_amount`)-".$data['point']/$res_point['settings_value']." WHERE `customer_id`=".$data['id'])){
            return 1;
          }
      }else{
        return 0;
      }
    }else{
      return 0;
    }
  }else{
    return 0;
  }
}
function send_point_to_friend($data){
    global $db;
    $point_set = $db->query("SELECT `settings_value` FROM `settings` WHERE `settings_name`='cast_to_point'");
    $res_point = $point_set->fetch_assoc();

    $res = $db->query("SELECT * FROM `customers_data` WHERE `Cash_amount` >= ".$data['point']/$res_point['settings_value']." AND `customer_id`=".$data['my_id']);
    if($res->num_rows>0){
        $cust_chk = $db->query("SELECT * FROM `customers_data` WHERE `customer_id`='".$data['friend_id']."'");
        if($cust_chk->num_rows>0){
            if($db->query("UPDATE `customers_data` SET `Cash_amount` = (`Cash_amount`)+".$data['point']/$res_point['settings_value']." WHERE `customer_id`='".$data['friend_id']."'")){
                if($db->query("UPDATE `customers_data` SET `Cash_amount` = (`Cash_amount`)-".$data['point']/$res_point['settings_value']." WHERE `customer_id`=".$data['my_id'])){
                    return 1;
                }
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }else{
        return 0;
    }
}
function shared_tunnel_search($data){
  global $db;
    $shared_with_str = $data['shared_with'];
    $sql="SELECT * FROM `customers_data` WHERE (`display_name`='".$shared_with_str."' OR `tag_id`='".$shared_with_str."' OR `email`='".$shared_with_str."') AND `customer_id`<>'".$data['user_id']."'";
    $query=$db->query($sql);
    if($query->num_rows>0){
        $row=$query->fetch_assoc();
        $value=$row['customer_id'];
        $data['shared_with']=$value;
        return shared_tunnel($data);
    }else{
        return "";
    }
}
function shared_tunnel($data){
    global $db;
    $shared_with = $data['shared_with'];
    if($db->query("SELECT * FROM `shared_tunnel` WHERE `user_id`='".$data['user_id']."' AND `tunnel_id`='".$data['t_id']."' AND `cloud_id`='".$data['c_id']."' AND `shared_with`='".$shared_with."'")->num_rows>0){
        return "Already shared.";
    }
    if($sql=$db->query("INSERT INTO `shared_tunnel` (`user_id`,`tunnel_id`,`cloud_id`,`shared_with`) VALUES(".$data['user_id'].",".$data['t_id'].",".$data['c_id'].",'".$shared_with."')")){
        return "Tunnel shared successful";
    }else{
        return "";
    }
}
function get_acl_destination_base($email, $id){
  global $db;

    $arr = array();
  $sql = $db->query("SELECT * FROM `customers_data` WHERE `email`='".$email."' AND `is_searchable`=1 AND `customer_id`<>".$id);

  if($sql->num_rows>0){

    $sql_get_acl = $db->query("SELECT * FROM `tunnel_acl_relation`
                JOIN `destination` ON `tunnel_acl_relation`.`id` = `destination`.`acl_id`
                JOIN `d_final` ON `tunnel_acl_relation`.`id` = `d_final`.`acl_id`
                JOIN `c_firewall` ON `tunnel_acl_relation`.`id` = `c_firewall`.`acl_id`
                JOIN `c_forwarding` ON `tunnel_acl_relation`.`id` = `c_forwarding`.`acl_id`
                JOIN `c_qos` ON `tunnel_acl_relation`.`id` = `c_qos`.`acl_id`
                JOIN `c_routing` ON `tunnel_acl_relation`.`id` = `c_routing`.`acl_id`
                JOIN `source` ON `tunnel_acl_relation`.`id` = `source`.`acl_id`
                JOIN `s_firewall` ON `tunnel_acl_relation`.`id` = `s_firewall`.`acl_id`
                JOIN `s_aliasing` ON `tunnel_acl_relation`.`id` = `s_aliasing`.`acl_id`
                JOIN `s_qos` ON `tunnel_acl_relation`.`id` = `s_qos`.`acl_id`
                JOIN `s_tos` ON `tunnel_acl_relation`.`id` = `s_tos`.`acl_id`
                WHERE `tunnel_acl_relation`.`is_searchable`=1 AND `tunnel_acl_relation`.`id`
                IN (SELECT `tunnel_acl_relation`.`id` FROM `customers_data` JOIN `tunnels_data` ON `customers_data`.`token`=`tunnels_data`.`user_token`
                JOIN `tunnel_acl_relation` ON `tunnels_data`.`tunnel_id`=`tunnel_acl_relation`.`tunnel_id` JOIN `clouds_data` ON `tunnels_data`.`cloud_id`=`clouds_data`.`cloud_id` WHERE `tunnels_data`.`is_deleted`=0 AND `tunnels_data`.`is_searchable`=1 AND `clouds_data`.`is_searchable`=1 AND `customers_data`.`email`='".$email."') ORDER BY `tunnel_acl_relation`.`id`");
    while($row1 = $sql_get_acl->fetch_assoc()){
        $arr[] = $row1;
    }
      //print_r($arr);die;
    $acl_info = array();
    foreach ($arr as $key => $value) {

        $id = $value['acl_id'];
        $acl_info[$id]=array();
        $acl_info[$id]["tunnel_id"]=$value['tunnel_id'];
        $acl_info[$id]["acl_name"]=$value['acl_name'];
        $acl_info[$id]["acl_description"]=$value['acl_description'];

        unset($value['id']);
        unset($value['acl_id']);
        unset($value['tunnel_id']);
        unset($value['creation_time']);
        unset($value['is_active']);
        unset($value['acl_name']);
        unset($value['acl_description']);
        unset($value['is_searchable']);

        foreach ($value as $k => $val) {
            $key = explode("-", $k);
            $base = $key[0];
            if(!isset($acl_info[$id][$base])){
                $acl_info[$id][$base]=array();
            }
            $label = ucwords(str_replace('_', ' ', $key[1]));
            //if($val>0){
            $acl_info[$id][$base][$key[1]] = array('label'=>readLabel($key), 'value'=>$val);
            //}
        }
    }
    //print_r($acl_info);die;
    return $acl_info;
  }else{
    return 0;
  }
}
function get_customer_acl_destination($id){
    global $db;
    $arr = array();
    $sql = $db->query("SELECT * FROM `customers_data` WHERE `is_searchable`=1 AND `customer_id`=".$id);
        if($sql->num_rows>0){
            $sql_get_acl = $db->query("SELECT * FROM `tunnel_acl_relation`
                JOIN `destination` ON `tunnel_acl_relation`.`id` = `destination`.`acl_id`
                JOIN `d_final` ON `tunnel_acl_relation`.`id` = `d_final`.`acl_id`
                JOIN `c_firewall` ON `tunnel_acl_relation`.`id` = `c_firewall`.`acl_id`
                JOIN `c_forwarding` ON `tunnel_acl_relation`.`id` = `c_forwarding`.`acl_id`
                JOIN `c_qos` ON `tunnel_acl_relation`.`id` = `c_qos`.`acl_id`
                JOIN `c_routing` ON `tunnel_acl_relation`.`id` = `c_routing`.`acl_id`
                JOIN `source` ON `tunnel_acl_relation`.`id` = `source`.`acl_id`
                JOIN `s_firewall` ON `tunnel_acl_relation`.`id` = `s_firewall`.`acl_id`
                JOIN `s_aliasing` ON `tunnel_acl_relation`.`id` = `s_aliasing`.`acl_id`
                JOIN `s_qos` ON `tunnel_acl_relation`.`id` = `s_qos`.`acl_id`
                JOIN `s_tos` ON `tunnel_acl_relation`.`id` = `s_tos`.`acl_id`
                WHERE `tunnel_acl_relation`.`is_searchable`=1 AND `tunnel_acl_relation`.`id`
                IN (SELECT `tunnel_acl_relation`.`id` FROM `customers_data` JOIN `tunnels_data` ON `customers_data`.`token`=`tunnels_data`.`user_token`
                JOIN `tunnel_acl_relation` ON `tunnels_data`.`tunnel_id`=`tunnel_acl_relation`.`tunnel_id` JOIN `clouds_data` ON `tunnels_data`.`cloud_id`=`clouds_data`.`cloud_id` WHERE `tunnels_data`.`is_deleted`=0 AND `tunnels_data`.`is_searchable`=1 AND `clouds_data`.`is_searchable`=1 AND `customers_data`.`customer_id`='".$id."') ORDER BY `tunnel_acl_relation`.`id`");
        while($row1 = $sql_get_acl->fetch_assoc()){
            $arr[] = $row1;
        }
        $acl_info = array();
        foreach ($arr as $key => $value) {
            if($value['tunnel_id']==$value['destination-specific_tunnel']){
                $id = $value['acl_id'];
                $acl_info[$id]=array();
                $acl_info[$id]["tunnel_id"]=$value['tunnel_id'];
                $acl_info[$id]["acl_name"]=$value['acl_name'];
                $acl_info[$id]["acl_description"]=$value['acl_description'];

                unset($value['id']);
                unset($value['acl_id']);
                unset($value['tunnel_id']);
                unset($value['creation_time']);
                unset($value['is_active']);
                unset($value['acl_name']);
                unset($value['acl_description']);
                unset($value['is_searchable']);

                foreach ($value as $k => $val) {
                    $key = explode("-", $k);

                    $base = $key[0];
                    if(!isset($acl_info[$id][$base])){
                        $acl_info[$id][$base]=array();
                    }
                    $acl_info[$id][$base][$key[1]] = array('label'=>readLabel($key), 'value'=>$val);
                }
            }
        }
        //print_r($acl_info);die;
        return $acl_info;
    }else{
        return array();
    }
}
function install_acl($data, $u_id){
  global $db;

  if($db->query("INSERT INTO `user_acl_relation` (`acl_id`,`user_id`,`tunnel_id`) VALUES(".$data['acl_id'].",".$u_id.",".$data['tunnel_id'].")")){
    echo 1;
  }else{
    echo 0;
  }

}
function cash_to_point(){
  global $db;
  $sql_point=$db->query("SELECT `settings_value` FROM `settings` WHERE `settings_name`='cast_to_point'");
  $point = $sql_point->fetch_assoc();
  return $point['settings_value'];
}
function change_profile_picture($file){

  global $db;
  $path="assets/user_img/";
  $formats = array("jpg", "png", "gif", "bmp", "jpeg");
  $name = $file['name'];
    $size = $file['size'];
    $tmp = $file['tmp_name'];
    if (strlen($name)) {
      $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
      if (in_array($ext, $formats)) {
        $imgn = time() . rand() . "." . $ext;
        if (move_uploaded_file($tmp, $path . $imgn)) {
          $res = $imgn;
        }
      } else {
        $res = '0';
      }
    } else {
      $res = '0';
    }
    $original_image = explode('.', $name);
    return array($res, $original_image[0]);
}
function get_available_real_ip(){
    global $db;
    $sql="select `subnet` from `server_subnets` where `used_ips`=0";
    $res=$db->query($sql);
    $row=$res->fetch_assoc();
    return $row['subnet'];
}
function update_cloud($data){
    $cloud_id=$data['cloud_id'];
    $token=$_SESSION['token'];
    //cloud_tunnels($cloud_id,"",$token);

    global $db;
    $sql="select * from `clouds_data` where `cloud_id`=".$cloud_id." and `user_token`='".$token."' and `is_deleted`=0";
    $res=$db->query($sql);
    if($res->num_rows>0){
        while($row=$res->fetch_assoc()){
            $cloud_data=array('cloud_id'=>$row['cloud_id'],'cloud_name'=>$row['cloud_name'],'is_searchable'=>$row['is_searchable']);
        }
    }

    $cloud_id=$cloud_data['cloud_id'];
    $cloud_name=$cloud_data['cloud_name'];
    $is_searchable=$cloud_data['is_searchable'];
    $_SESSION['cloud']=$cloud_id;
    $tunnel = "SELECT `tunnels_data`.*, `remote_server_list`.`server_name` `location`, `real_ip_list`.`is_active` `active` FROM `tunnels_data` left join `real_ip_list` on `tunnels_data`.`real_ip`=`real_ip_list`.`real_ip` left join `remote_server_list` on `tunnels_data`.`location`=`remote_server_list`.`id` WHERE `tunnels_data`.`cloud_id`='".$db->real_escape_string($cloud_id)."' and `tunnels_data`.`user_token`='".$db->real_escape_string($_SESSION['token'])."' and `tunnels_data`.`is_deleted`=0";
    $sql=$db->query($tunnel." order by group_id asc, group_id");
    $data=array();
    while($row=$sql->fetch_assoc()){
        $data[]=$row;
    }
    $tunnels_data=array();
    $tunnel_ids="";
    foreach($data as $tunnel_data){
        $tunnel_ids.=$tunnel_data['tunnel_id'];
        $tunnel_ids.=",";

        $acl_ids=array();
        $installed_acl_ids=array();
        $real_ips=array();
        $installed_real_ips=array();

        $sql="select id from tunnel_acl_relation where tunnel_id='".$tunnel_data['tunnel_id']."'";
        $res=$db->query($sql);
        while($row=$res->fetch_assoc()){
            $acl_ids[]=$row['id'];
        }
        if($tunnel_data['real_ip']!=""){
            $real_ips[]=$tunnel_data['real_ip'];
        }
        unset($tunnel_data['real_ip']);
        if(count($acl_ids)>0){
            $acl_ids_str=implode(",",$acl_ids);
            $sql="SELECT `destination-real_ip` FROM `destination` WHERE `acl_id` IN (".$acl_ids_str.") GROUP BY `destination-real_ip`";
            $res=$db->query($sql);
            while($row=$res->fetch_assoc()){
                if(!in_array($row['destination-real_ip'],$real_ips)){
                    if($row['destination-real_ip']!=""){
                        $real_ips[]=$row['destination-real_ip'];
                    }
                }
            }
        }
        $sql="select acl_id from user_acl_relation where tunnel_id='".$tunnel_data['tunnel_id']."' and status=1";
        $res=$db->query($sql);
        while($row=$res->fetch_assoc()){
            $installed_acl_ids[]=$row['acl_id'];
        }
        if(count($installed_acl_ids)>0){
            $acl_ids_str=implode(",",$installed_acl_ids);
            $sql="SELECT `destination-real_ip` FROM `destination` WHERE `acl_id` IN (".$acl_ids_str.") GROUP BY `destination-real_ip`";
            $res=$db->query($sql);
            while($row=$res->fetch_assoc()){
                if(!in_array($row['destination-real_ip'],$installed_real_ips)){
                    if($row['destination-real_ip']!=""){
                        $installed_real_ips[]=$row['destination-real_ip'];
                    }
                }
            }
        }
        $tunnel_data['real_ip']=$real_ips;
        $tunnel_data['installed_real_ips']=$installed_real_ips;
        $tunnels_data[]=$tunnel_data;
    }
    $tunnel_ids=trim($tunnel_ids,",");

    $sql_contact=$db->query("SELECT `users_data`.`id`, `users_data`.`user_email`, `users_data`.`name` FROM `users_data` INNER JOIN `customer_user_relations` ON `users_data`.`id`=`customer_user_relations`.`user_id` AND `customer_user_relations`.`user_token`='".$db->real_escape_string($_SESSION['token'])."'");

    $html="";
    while ($row_contact=$sql_contact->fetch_assoc()) {
        $html.='<option value="'.$row_contact['user_email'].'">'.$row_contact['user_email'].'</option>';
    }

    $sql_cust=$db->query("SELECT * FROM `customers_data` WHERE `customer_id`<>".$_SESSION['user_id']);
    $html_cust="";
    while ($row_cust=$sql_cust->fetch_assoc()) {
        $html_cust.='<option value="'.$row_cust['customer_id'].'">'.$row_cust['email'].'</option>';
    }
    ?>

        <div class="cloud-tunnels cloud-tunnels-<?php echo($cloud_id); ?>">
            <div class="page-content cloud-content">
                <div class="content" style="padding-top: 0px;">
                    <div class="page-title">
                        <div class="cloud-name cloud-name-<?php echo($cloud_id); ?>">
                            <?php echo($cloud_name); ?>
                            <span class="cloud-cost cloud-cost-<?php echo($cloud_id); ?>"> &nbsp;( Total cost = <?php echo(get_cloud_cost($cloud_id,$tunnel_ids)*cash_to_point()); ?> )</span>
                        </div>
                        <span class="delete_cloud" data-val="<?php echo $cloud_id ?>"><i class="fa fa-trash pull-right cursor" data-toggle="tooltip" data-placement="top" title="Delete this Cloud"></i></span>
                        <?php
                        $switch_checked_val=($is_searchable==1?"checked":"");
                        ?>
                        <div class="cloud_searchable_switch_block">
                            <input id="cmn-toggle-cloud-<?php echo $cloud_id; ?>" class="cmn-toggle cmn-toggle-round cloud_searchable_switch" data-cloud_id="<?php echo $cloud_id; ?>" type="checkbox" <?php echo($switch_checked_val); ?>>
                            <label for="cmn-toggle-cloud-<?php echo $cloud_id; ?>"></label>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    <div data-uid="<?php echo $_SESSION['user_id'] ?>" class="just list Parks">

                        <div class="list_header">
                            <div class="meta" data-toggle="tooltip" data-placement="right" title="ACL"><i class="fa fa-eye"></i></div>
                            <div class="meta" data-toggle="tooltip" data-placement="right" title="Create ACL"><i class="fa fa-cogs"></i></div>

                            <div class="meta" id="SortByName" data-toggle="tooltip" data-placement="right" title="Add tunnels"><a href="javascript:void(0);" data-val="<?php echo $cloud_id; ?>" data-mail="<?php echo $_SESSION['email'] ?>" data-count="0" class="tunnel_add_form_btn"><i class="fa fa-fw fa-plus-circle"></i></a></div>

                            <div class="meta" id="" data-toggle="tooltip" data-placement="right" title="Save all"><a href="javascript:void(0);" data-val="<?php echo $cloud_id; ?>" data-count="0" class="all_tunnel_save_btn"><i class="fa fa-floppy-o"></i></a></div>

                            <div class="meta width-30"><div class="cursor chk_all_tunnel" data-toggle="tooltip" data-placement="bottom" data-val="0" title="Select all tunnels"><i class="fa fa-square-o"></i></div><!--<a href="javascript:void(0);" class="tunnel_vew_by_tnl tunnel_vew_by_tnl_<?php /*echo $cloud_id; */?>" data-cloud="<?php /*echo $cloud_id; */?>" data-dif="client"><i class="fa fa-sort"></i></a>--></div>

                            <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Groups"><i class="fa fa-fw fa-group"></i>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="tunnel_vew_by_grp" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>

                            <div class="meta width-120" data-toggle="tooltip" data-placement="bottom" title="Tunnel name"><i class="fa fa-cog"></i><!--<a href="javascript:void(0);" class="tunnel_vew_by_name" data-cloud="<?php /*echo $cloud_id; */?>" data-dif="asc"><i class="fa fa-sort"></i></a>--></div>
                            <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Bidirection mode"><i class="fa fa-chevron-left"></i><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="tunnel_vew_by_bidirection" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>
                            <div class="meta width-80" data-toggle="tooltip" data-placement="bottom" title="Location"><i class="fa fa-fw fa-globe"></i></div>
                            <div class="meta width-100" data-toggle="tooltip" data-placement="bottom" title="VPN IP"><i class="fa  fa-map-pin"></i></div>
                            <div class="meta width-60" data-toggle="tooltip" data-placement="bottom" title="Points" style="margin-left: 4px;"><i class="fa fa-fw fa-dollar"></i></div>
                            <div class="meta width-140" style="width: 137px!important;" data-toggle="tooltip" data-placement="bottom" title="Real IP">Real IP</div>
                            <div class="meta width-60" data-toggle="tooltip" data-placement="bottom" title="Gateway mode">Gateway<!--<a href="javascript:void(0);" class="tunnel_vew_by_bidirection" data-cloud="<?php /*echo $cloud_id; */?>" data-dif="asc"><i class="fa fa-sort"></i></a>--></div>
                            <div class="meta"></div>
                        </div>
                        <form id="tunnels_form_field" style="display:none;">
                            <input type="button" class="btn btn-sm btn-primary btn_add_tunnel" data-cloud="<?php echo $cloud_id; ?>" value="Submit">
                            <input type="reset" class="btn btn-sm btn-warning" id="tunnel_form_close_btn" value="Cancel">
                        </form>
                        <div class="tunnel_body tunnel_body_<?php echo $cloud_id; ?>">
                            <?php
                            //echo tunnels($tunnels_data);
                            ?>
                        </div>
                        <div id="tunnel_body_pagenation_<?php echo $cloud_id; ?>">

                        </div>
                        <script>
                            console.log("<?php echo($tunnel_ids); ?>");
                            $("#tunnel_body_pagenation_<?php echo $cloud_id; ?>").pagination({
                                dataSource: [<?php echo($tunnel_ids); ?>],
                                pageSize: 5,
                                autoHidePrevious: true,
                                autoHideNext: true,
                                callback: function(data, pagination) {
                                    console.log(data);
                                    console.log(pagination);
                                    // template method of yourself
                                    var html = tunnel_template(data,".tunnel_body_<?php echo $cloud_id; ?>");
                                    /*$(".tunnel_body_<?php //echo $cloud_id; ?>").html(html);*/
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
<?php
}
function cloud_tunnels($id,$name,$token){//id:cloud_id, name:cloud_name
    global $db;
    $cloud_ids=array();
    if($id==0){
        $sql="select * from `clouds_data` where `user_token`='".$token."' and `is_deleted`=0";
        $res=$db->query($sql);
        if($res->num_rows>0){
            while($row=$res->fetch_assoc()){
                $cloud_ids[]=array('cloud_id'=>$row['cloud_id'],'cloud_name'=>$row['cloud_name'],'is_searchable'=>$row['is_searchable']);
            }
        }
    }else{
        $sql="select * from `clouds_data` where `cloud_id`=".$id." and `user_token`='".$token."' and `is_deleted`=0";
        $res=$db->query($sql);
        if($res->num_rows>0){
            while($row=$res->fetch_assoc()){
                $cloud_ids[]=array('cloud_id'=>$row['cloud_id'],'cloud_name'=>$row['cloud_name'],'is_searchable'=>$row['is_searchable']);
            }
        }
    }

    foreach($cloud_ids as $cloud_data){
        $cloud_id=$cloud_data['cloud_id'];
        $cloud_name=$cloud_data['cloud_name'];
        $is_searchable=$cloud_data['is_searchable'];
        $_SESSION['cloud']=$cloud_id;
        $tunnel = "SELECT `tunnels_data`.*, `remote_server_list`.`server_name` `location`, `real_ip_list`.`is_active` `active` FROM `tunnels_data` left join `real_ip_list` on `tunnels_data`.`real_ip`=`real_ip_list`.`real_ip` left join `remote_server_list` on `tunnels_data`.`location`=`remote_server_list`.`id` WHERE `tunnels_data`.`cloud_id`='".$db->real_escape_string($cloud_id)."' and `tunnels_data`.`user_token`='".$db->real_escape_string($_SESSION['token'])."' and `tunnels_data`.`is_deleted`=0";
        $sql=$db->query($tunnel." order by group_id asc, group_id");
        $data=array();
        while($row=$sql->fetch_assoc()){
            $data[]=$row;
        }
        $tunnels_data=array();
        $tunnel_ids="";
        foreach($data as $tunnel_data){
            $tunnel_ids.=$tunnel_data['tunnel_id'];
            $tunnel_ids.=",";

            $acl_ids=array();
            $installed_acl_ids=array();
            $real_ips=array();
            $installed_real_ips=array();

            $sql="select id from tunnel_acl_relation where tunnel_id='".$tunnel_data['tunnel_id']."'";
            $res=$db->query($sql);
            while($row=$res->fetch_assoc()){
                $acl_ids[]=$row['id'];
            }
            if($tunnel_data['real_ip']!=""){
                $real_ips[]=$tunnel_data['real_ip'];
            }
            unset($tunnel_data['real_ip']);
            if(count($acl_ids)>0){
                $acl_ids_str=implode(",",$acl_ids);
                $sql="SELECT `destination-real_ip` FROM `destination` WHERE `acl_id` IN (".$acl_ids_str.") GROUP BY `destination-real_ip`";
                $res=$db->query($sql);
                while($row=$res->fetch_assoc()){
                    if(!in_array($row['destination-real_ip'],$real_ips)){
                        if($row['destination-real_ip']!=""){
                            $real_ips[]=$row['destination-real_ip'];
                        }
                    }
                }
            }
            $sql="select acl_id from user_acl_relation where tunnel_id='".$tunnel_data['tunnel_id']."' and status=1";
            $res=$db->query($sql);
            while($row=$res->fetch_assoc()){
                $installed_acl_ids[]=$row['acl_id'];
            }
            if(count($installed_acl_ids)>0){
                $acl_ids_str=implode(",",$installed_acl_ids);
                $sql="SELECT `destination-real_ip` FROM `destination` WHERE `acl_id` IN (".$acl_ids_str.") GROUP BY `destination-real_ip`";
                $res=$db->query($sql);
                while($row=$res->fetch_assoc()){
                    if(!in_array($row['destination-real_ip'],$installed_real_ips)){
                        if($row['destination-real_ip']!=""){
                            $installed_real_ips[]=$row['destination-real_ip'];
                        }
                    }
                }
            }
            $tunnel_data['real_ip']=$real_ips;
            $tunnel_data['installed_real_ips']=$installed_real_ips;
            $tunnels_data[]=$tunnel_data;
        }
        $tunnel_ids=trim($tunnel_ids,",");

        $sql_contact=$db->query("SELECT `users_data`.`id`, `users_data`.`user_email`, `users_data`.`name` FROM `users_data` INNER JOIN `customer_user_relations` ON `users_data`.`id`=`customer_user_relations`.`user_id` AND `customer_user_relations`.`user_token`='".$db->real_escape_string($_SESSION['token'])."'");

        $html="";
        while ($row_contact=$sql_contact->fetch_assoc()) {
            $html.='<option value="'.$row_contact['user_email'].'">'.$row_contact['user_email'].'</option>';
        }

        $sql_cust=$db->query("SELECT * FROM `customers_data` WHERE `customer_id`<>".$_SESSION['user_id']);
        $html_cust="";
        while ($row_cust=$sql_cust->fetch_assoc()) {
            $html_cust.='<option value="'.$row_cust['customer_id'].'">'.$row_cust['email'].'</option>';
        }
        ?>
        <div class="cloud-row cloud-row-<?php echo($cloud_id); ?>" data-cid="<?php echo($cloud_id); ?>">
            <div class="cloud-tunnels cloud-tunnels-<?php echo($cloud_id); ?>">
                <div class="page-content cloud-content">
                    <div class="content" style="padding-top: 0px;">
                        <div class="page-title">
                            <div class="cloud-name cloud-name-<?php echo($cloud_id); ?>">
                                <?php echo($cloud_name); ?>
                                <span class="cloud-cost cloud-cost-<?php echo($cloud_id); ?>"> &nbsp;( Total cost = <?php echo(get_cloud_cost($cloud_id,$tunnel_ids)); ?> )</span>
                            </div>
                            <span class="delete_cloud" data-val="<?php echo $cloud_id ?>"><i class="fa fa-trash pull-right cursor" data-toggle="tooltip" data-placement="top" title="Delete this Cloud"></i></span>
                            <?php
                            $switch_checked_val=($is_searchable==1?"checked":"");
                            ?>
                            <div class="cloud_searchable_switch_block">
                                <input id="cmn-toggle-cloud-<?php echo $cloud_id; ?>" class="cmn-toggle cmn-toggle-round cloud_searchable_switch" data-cloud_id="<?php echo $cloud_id; ?>" type="checkbox" <?php echo($switch_checked_val); ?>>
                                <label for="cmn-toggle-cloud-<?php echo $cloud_id; ?>"></label>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <div data-uid="<?php echo $_SESSION['user_id'] ?>" class="just list Parks">

                            <div class="list_header">
                                <div class="meta" data-toggle="tooltip" data-placement="right" title="ACL"><i class="fa fa-eye"></i></div>
                                <div class="meta" data-toggle="tooltip" data-placement="right" title="Create ACL"><i class="fa fa-cogs"></i></div>

                                <div class="meta" id="SortByName" data-toggle="tooltip" data-placement="right" title="Add tunnels"><a href="javascript:void(0);" data-val="<?php echo $cloud_id; ?>" data-mail="<?php echo $_SESSION['email'] ?>" data-count="0" class="tunnel_add_form_btn"><i class="fa fa-fw fa-plus-circle"></i></a></div>

                                <div class="meta" id="" data-toggle="tooltip" data-placement="right" title="Save all"><a href="javascript:void(0);" data-val="<?php echo $cloud_id; ?>" data-count="0" class="all_tunnel_save_btn"><i class="fa fa-floppy-o"></i></a></div>

                                <div class="meta width-30"><div class="cursor chk_all_tunnel" data-toggle="tooltip" data-placement="bottom" data-val="0" title="Select all tunnels"><i class="fa fa-square-o"></i></div><!--<a href="javascript:void(0);" class="tunnel_vew_by_tnl tunnel_vew_by_tnl_<?php /*echo $cloud_id; */?>" data-cloud="<?php /*echo $cloud_id; */?>" data-dif="client"><i class="fa fa-sort"></i></a>--></div>

                                <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Groups"><i class="fa fa-fw fa-group"></i>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="tunnel_vew_by_grp" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>

                                <div class="meta width-120" data-toggle="tooltip" data-placement="bottom" title="Tunnel name"><i class="fa fa-cog"></i><!--<a href="javascript:void(0);" class="tunnel_vew_by_name" data-cloud="<?php /*echo $cloud_id; */?>" data-dif="asc"><i class="fa fa-sort"></i></a>--></div>
                                <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Bidirection mode"><i class="fa fa-chevron-left"></i><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="tunnel_vew_by_bidirection" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>
                                <div class="meta width-80" data-toggle="tooltip" data-placement="bottom" title="Location"><i class="fa fa-fw fa-globe"></i></div>
                                <div class="meta width-100" data-toggle="tooltip" data-placement="bottom" title="VPN IP"><i class="fa  fa-map-pin"></i></div>
                                <div class="meta width-60" data-toggle="tooltip" data-placement="bottom" title="Points" style="margin-left: 4px;"><i class="fa fa-fw fa-dollar"></i></div>
                                <div class="meta width-140" style="width: 137px!important;" data-toggle="tooltip" data-placement="bottom" title="Real IP">Real IP</div>
                                <div class="meta width-60" data-toggle="tooltip" data-placement="bottom" title="Gateway mode">Gateway<!--<a href="javascript:void(0);" class="tunnel_vew_by_bidirection" data-cloud="<?php /*echo $cloud_id; */?>" data-dif="asc"><i class="fa fa-sort"></i></a>--></div>
                                <div class="meta"></div>
                            </div>
                            <form id="tunnels_form_field" style="display:none;">
                                <input type="button" class="btn btn-sm btn-primary btn_add_tunnel" data-cloud="<?php echo $cloud_id; ?>" value="Submit">
                                <input type="reset" class="btn btn-sm btn-warning" id="tunnel_form_close_btn" value="Cancel">
                            </form>
                            <div class="tunnel_body tunnel_body_<?php echo $cloud_id; ?>">
                                <?php
                                //echo tunnels($tunnels_data);
                                ?>
                            </div>
                            <div id="tunnel_body_pagenation_<?php echo $cloud_id; ?>">

                            </div>
                            <script>
                                console.log("<?php echo($tunnel_ids); ?>");
                                $("#tunnel_body_pagenation_<?php echo $cloud_id; ?>").pagination({
                                    dataSource: [<?php echo($tunnel_ids); ?>],
                                    pageSize: 5,
                                    autoHidePrevious: true,
                                    autoHideNext: true,
                                    callback: function(data, pagination) {
                                        console.log(data);
                                        console.log(pagination);
                                        // template method of yourself
                                        var html = tunnel_template(data,".tunnel_body_<?php echo $cloud_id; ?>");
                                        /*$(".tunnel_body_<?php //echo $cloud_id; ?>").html(html);*/
                                    }
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    if($id==0 || $id==-1){
        show_shared_tunnels();
    }
}
function get_tunnel_from_id($tunnel_id){
    $tunnels_data=get_tunnels_data($tunnel_id);
    echo tunnels($tunnels_data);
}
function get_tunnels_data($tunnel_id){
    global $db;

    $tunnel = "SELECT `tunnels_data`.*, `remote_server_list`.`server_name` `location`, `real_ip_list`.`is_active` `active` FROM `tunnels_data` left join `real_ip_list` on `tunnels_data`.`real_ip`=`real_ip_list`.`real_ip` left join `remote_server_list` on `tunnels_data`.`location`=`remote_server_list`.`id` WHERE `tunnels_data`.`tunnel_id`='".$tunnel_id."' and `tunnels_data`.`is_deleted`=0";
    $sql=$db->query($tunnel." order by group_id asc, group_id");
    $data=array();
    $tunnel_data=$sql->fetch_assoc();

    $acl_ids=array();
    $installed_acl_ids=array();
    $real_ips=array();
    $installed_real_ips=array();

    $sql="select id from tunnel_acl_relation where tunnel_id='".$tunnel_data['tunnel_id']."'";
    $res=$db->query($sql);
    while($row=$res->fetch_assoc()){
        $acl_ids[]=$row['id'];
    }
    $real_ips[]=array("ip"=>$tunnel_data['real_ip'],"acl_id"=>"");
    unset($tunnel_data['real_ip']);
    if(count($acl_ids)>0){
        $acl_ids_str=implode(",",$acl_ids);
        $sql="SELECT * FROM `destination` WHERE `acl_id` IN (".$acl_ids_str.") GROUP BY `destination-real_ip`";
        $res=$db->query($sql);
        while($row=$res->fetch_assoc()){
            //if(!in_array($row['destination-real_ip'],$real_ips)){
                if($row['destination-real_ip']!=""){
                    $real_ips[]=array("ip"=>$row['destination-real_ip'],"acl_id"=>$row['acl_id']);
                }
            //}
        }
    }
    $sql="select acl_id from user_acl_relation where tunnel_id='".$tunnel_data['tunnel_id']."' and status=1";
    $res=$db->query($sql);
    while($row=$res->fetch_assoc()){
        $installed_acl_ids[]=$row['acl_id'];
    }
    if(count($installed_acl_ids)>0){
        $acl_ids_str=implode(",",$installed_acl_ids);
        $sql="SELECT * FROM `destination` WHERE `acl_id` IN (".$acl_ids_str.") GROUP BY `destination-real_ip`";
        $res=$db->query($sql);
        while($row=$res->fetch_assoc()){
            //if(!in_array($row['destination-real_ip'],$installed_real_ips)){
                if($row['destination-real_ip']!=""){
                    $installed_real_ips[]=array("ip"=>$row['destination-real_ip'],"acl_id"=>$row['acl_id']);
                }
            //}
        }
    }
    $tunnel_data['real_ip']=$real_ips;
    $tunnel_data['installed_real_ips']=$installed_real_ips;
    $tunnels_data=array();
    $tunnels_data[]=$tunnel_data;
    return $tunnels_data;
}
function get_tunnels_from_ids($data){
    //print_r($data);die;
    foreach($data['tunnel_ids'] as $tunnel_id){
        get_tunnel_from_id($tunnel_id);
    }
    exit;
}
function get_sponsor_tunnels_from_id($tunnel_id){
    global $db;

    $tunnel = "SELECT `tunnels_data`.*, `remote_server_list`.`server_name` `location`, `real_ip_list`.`is_active` `active` FROM `tunnels_data` left join `real_ip_list` on `tunnels_data`.`real_ip`=`real_ip_list`.`real_ip` left join `remote_server_list` on `tunnels_data`.`location`=`remote_server_list`.`id` WHERE `tunnels_data`.`tunnel_id`='".$tunnel_id."' and `tunnels_data`.`is_deleted`=0";
    $sql=$db->query($tunnel." order by group_id asc, group_id");
    $data=array();
    $tunnel_data=$sql->fetch_assoc();

    $acl_ids=array();
    $installed_acl_ids=array();
    $real_ips=array();
    $installed_real_ips=array();

    $sql="select id from tunnel_acl_relation where tunnel_id='".$tunnel_data['tunnel_id']."'";
    $res=$db->query($sql);
    while($row=$res->fetch_assoc()){
        $acl_ids[]=$row['id'];
    }
    $real_ips[]=array("ip"=>$tunnel_data['real_ip'],"acl_id"=>"");
    unset($tunnel_data['real_ip']);
    if(count($acl_ids)>0){
        $acl_ids_str=implode(",",$acl_ids);
        $sql="SELECT * FROM `destination` WHERE `acl_id` IN (".$acl_ids_str.") GROUP BY `destination-real_ip`";
        $res=$db->query($sql);
        while($row=$res->fetch_assoc()){
            //if(!in_array($row['destination-real_ip'],$real_ips)){
            if($row['destination-real_ip']!=""){
                $real_ips[]=array("ip"=>$row['destination-real_ip'],"acl_id"=>$row['acl_id']);
            }
            //}
        }
    }
    $sql="select acl_id from user_acl_relation where tunnel_id='".$tunnel_data['tunnel_id']."' and status=1";
    $res=$db->query($sql);
    while($row=$res->fetch_assoc()){
        $installed_acl_ids[]=$row['acl_id'];
    }
    if(count($installed_acl_ids)>0){
        $acl_ids_str=implode(",",$installed_acl_ids);
        $sql="SELECT * FROM `destination` WHERE `acl_id` IN (".$acl_ids_str.") GROUP BY `destination-real_ip`";
        $res=$db->query($sql);
        while($row=$res->fetch_assoc()){
            //if(!in_array($row['destination-real_ip'],$installed_real_ips)){
            if($row['destination-real_ip']!=""){
                $installed_real_ips[]=array("ip"=>$row['destination-real_ip'],"acl_id"=>$row['acl_id']);
            }
            //}
        }
    }
    $tunnel_data['real_ip']=$real_ips;
    $tunnel_data['installed_real_ips']=$installed_real_ips;
    $tunnels_data=array();
    $tunnels_data[]=$tunnel_data;
    echo sponsor_tunnels($tunnels_data);


}
function get_sponsor_tunnels_from_ids($data){
    //print_r($data);die;
    foreach($data['tunnel_ids'] as $tunnel_id){
        get_sponsor_tunnels_from_id($tunnel_id);
    }
    exit;
}
function check_tunnel_sponsored($tunnel_id){
    global $db;
    $sql="SELECT * FROM `shared_tunnel` WHERE `user_id`=".$_SESSION['customer_id']." AND `tunnel_id`=".$tunnel_id;
    $query=$db->query($sql);
    $result=array('status'=>'0','shared_with'=>"");
    if($query->num_rows>0){
        $shared_with=$query->fetch_assoc()['shared_with'];
        $sql="SELECT * FROM `customers_data` WHERE `customer_id`=".$shared_with;
        $customer_query=$db->query($sql);
        if($customer_query->num_rows>0){
            $cust_name=$customer_query->fetch_assoc()['display_name'];
            return array('status'=>'1','shared_with'=>$cust_name);
        }else{
            return $result;
        }
    }else{
        return $result;
    }
}
function remove_sharing($data){
    $res=array('status'=>0);
    global $db;
    $tunnel_id=$data['tunnel_id'];
    $sql="DELETE FROM `shared_tunnel` WHERE `user_id`=".$data['user_id']." AND `tunnel_id`=".$tunnel_id;
    if(isset($data['shared_with'])){
        $sql="DELETE FROM `shared_tunnel` WHERE `shared_with`=".$data['shared_with']." AND `tunnel_id`=".$tunnel_id;
    }
    $query=$db->query($sql);
    if($query){
        $res=array('status'=>1);
    }
    return $res;
}
function update_badge_cnt($data){
    global $db;
    $friends_sql="SELECT id FROM `friends_data` WHERE (friend_id=".$_SESSION['customer_id']." OR customer_id=".$_SESSION['customer_id'].") AND status='accepted'";
    $request_friends_sql="SELECT id FROM `friends_data` WHERE (friend_id=".$_SESSION['customer_id']." OR customer_id=".$_SESSION['customer_id'].") AND status='request'";
    $rejected_friends_sql="SELECT id FROM `friends_data` WHERE (friend_id=".$_SESSION['customer_id']." OR customer_id=".$_SESSION['customer_id'].") AND status='rejected'";
    $friends_cnt=$db->query($friends_sql)->num_rows;
    $request_friends_cnt=$db->query($request_friends_sql)->num_rows;
    $rejected_friends_cnt=$db->query($rejected_friends_sql)->num_rows;
    return array('friends_cnt'=>$friends_cnt,'request_friends_cnt'=>$request_friends_cnt,'rejected_friends_cnt'=>$rejected_friends_cnt);
}
function set_friend($data){
    global $db;
    $customer_id=$data['customer_id'];
    $friend_id=$data['friend_id'];
    $status=$data['status'];
    if($status=="request"){
        $sql="SELECT id FROM `friends_data` WHERE ((`customer_id`=".$customer_id." AND `friend_id`=".$friend_id.") OR (`customer_id`=".$friend_id." AND `friend_id`=".$customer_id."))";
        $res=$db->query($sql);
        if($res->num_rows==0){
            $sql="INSERT INTO `friends_data` (`customer_id`,`friend_id`,`status`) VALUES ('".$customer_id."','".$friend_id."','".$status."')";
            $res=$db->query($sql);
            return $db->insert_id;
        }
    }elseif($status=="accepted" || $status=="rejected"){
        $sql="UPDATE `friends_data` SET `status`='".$status."' WHERE `customer_id`=".$customer_id." AND `friend_id`=".$friend_id;
        $res=$db->query($sql);
        if($res){
            return $res;
        }else{
            return;
        }
    }elseif($status=="deleted"){
        $sql="DELETE FROM `friends_data` WHERE ((`customer_id`=".$customer_id." AND `friend_id`=".$friend_id.") OR (`customer_id`=".$friend_id." AND `friend_id`=".$customer_id."))";
        $res=$db->query($sql);
        if($res){
            return $res;
        }else{
            return "";
        }
    }
}
function get_customers($data){
    global $db;
    $key_code=$data['key_code'];
    $where=" AND (`display_name` LIKE '%".$key_code."%' OR `email` LIKE '%".$key_code."%' OR `tag_id` LIKE '%".$key_code."%')";
        $related_customers=array();
        $sql_related_customers=$db->query("SELECT * FROM `friends_data` WHERE (`customer_id`='".$_SESSION['customer_id']."' OR `friend_id`='".$_SESSION['customer_id']."')");
        if($sql_related_customers->num_rows>0){
            while($row=$sql_related_customers->fetch_assoc()){
                if($row['friend_id']!=$_SESSION['customer_id']){
                    $related_customers[]=$row['friend_id'];
                }else{
                    $related_customers[]=$row['customer_id'];
                }
            }
        }
        $related_customers_str=implode(",",$related_customers);
        $sql="SELECT * FROM `customers_data` WHERE `customer_id`<>".$_SESSION['customer_id']." AND `is_searchable`=1";
        if(count($related_customers)>0){
            $sql="SELECT * FROM `customers_data` WHERE `customer_id` NOT IN (".$related_customers_str.") AND `customer_id`<>".$_SESSION['customer_id']." AND `is_searchable`=1";
        }
    //print_r($sql.$where);die;
        $sql3=$db->query($sql.$where);
        $all_customers=array();
        $i=0;
        while($row=$sql3->fetch_assoc()){
            $i++;
            $last_class="";
            if($i==$sql3->num_rows){
                $last_class="left-friend-list-content-row-last";
            }
            $odd_even_class="left-friend-list-content-row-odd";
            /*if(intval($i/2)*2==$i){
                $odd_even_class="left-friend-list-content-row-even";
            }*/
            $get_customer_acl_destination=get_customer_acl_destination($row['customer_id']);
            $row['shared_acl_cnt']=count($get_customer_acl_destination);
            $all_customers[]=$row;
            //print_r($row);
            ?>
            <div class="left-friend-list-content-row <?php echo($odd_even_class); ?> <?php echo($last_class); ?>" data-customer_id="<?php echo($row['customer_id']); ?>" data-customer_name="<?php echo($row['name']); ?>">
                <div class="profile-info-box" style="float: left;">
                    <div style="float: left">
                        <img class="friend_short_image" src="<?php echo(($row['profile_image']!="")?$row['profile_image']:ROOT_URL.'/assets/img/profiles/demo-user.jpg'); ?>" alt="<?php echo($row['profile_image']); ?>">
                    </div>
                    <div class="friend_info" style="float: left;">
                        <div class="friend_name"><?php echo($row['display_name']); ?></div>
                        <div class="friend_tag_id"><?php echo($row['tag_id']); ?>: <?php echo($row['shared_acl_cnt']); ?></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="friend-action-box">
                            <span class="friend-action" data-friend_id="<?php echo($row['customer_id']); ?>">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </span>
                </div>
                <div class="clearfix"></div>
            </div>
        <?php
        }

}
function get_friends($data){
    global $db;
    //$customer_id=$data['customer_id'];
?>
    <div class="all-customers-box hidden"></div>
    <div class="current-friends-box">
        <?php
        $friend_ids=array();
        $sql_friends=$db->query("SELECT * FROM `friends_data`
                WHERE (`customer_id`='".$_SESSION['customer_id']."' OR `friend_id`='".$_SESSION['customer_id']."') AND `status`='accepted'");
        if($sql_friends->num_rows>0){
            while($row=$sql_friends->fetch_assoc()){
                if($row['friend_id']!=$_SESSION['customer_id']){
                    $friend_ids[]=$row['friend_id'];
                }else{
                    $friend_ids[]=$row['customer_id'];
                }
            }
        }
        if(count($friend_ids)>0){
            $friend_ids_str = implode(",", $friend_ids);
            $all_friends = array();
            $sql_friends = $db->query("SELECT * FROM `customers_data` WHERE `customer_id` IN (".$friend_ids_str.")");
            $i = 0;
            while ($row = $sql_friends->fetch_assoc()){
                $all_friends[] = $row;
                $friend_id = $row['customer_id'];
                $i++;
                $last_class = "";
                if ($i==count($all_friends)){
                    $last_class = "left-friend-list-content-row-last";
                }
                $odd_even_class = "left-friend-list-content-row-odd";
                /*if(intval($i/2)*2==$i){
                    $odd_even_class="left-friend-list-content-row-even";
                }*/
                $get_customer_acl_destination = get_customer_acl_destination($friend_id);
                $row['shared_acl_cnt'] = count($get_customer_acl_destination);
                ?>
                <div class="left-friend-list-content-row <?php echo ($odd_even_class); ?> <?php echo ($last_class); ?> custom_popup_context_item" data-friend_id="<?php echo ($friend_id); ?>" data-friend_name="<?php echo ($row['name']); ?>">
                    <div class="profile-info-box" style="float: left;">
                        <div style="float: left">
                            <img class="friend_short_image"
                                 src="<?php echo (($row['profile_image']!="")?$row['profile_image']:ROOT_URL.'/assets/img/profiles/demo-user.jpg'); ?>"
                                 alt="<?php echo ($row['profile_image']); ?>">
                        </div>
                        <div class="friend_info" style="float: left;">
                            <div class="friend_name"><?php echo ($row['display_name']); ?></div>
                            <div class="friend_tag_id"><?php echo ($row['tag_id']); ?>: <?php echo ($row['shared_acl_cnt']); ?></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="friend-action-box">
                        <span class="friend-action delete-action" data-friend_id="<?php echo ($friend_id); ?>">
                            <i class="fa fa-trash-o" aria-hidden="true"></i>
                        </span>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <?php
            }
        }
        ?>
    </div>
<?php
}
function get_request_friends($data){
        global $db;
        $sql_request_friends=$db->query("SELECT * FROM `friends_data`
            LEFT JOIN `customers_data` ON `friends_data`.`customer_id`=`customers_data`.`customer_id`
            WHERE `friends_data`.`friend_id`='".$_SESSION['customer_id']."' AND `friends_data`.`status`='request'");
        $all_friends=array();
        $i=0;
        while($row=$sql_request_friends->fetch_assoc()){
            $i++;
            $last_class="";
            if($i==$sql_request_friends->num_rows){
                $last_class="left-friend-list-content-row-last";
            }
            $odd_even_class="left-friend-list-content-row-odd";
            /*if(intval($i/2)*2==$i){
                $odd_even_class="left-friend-list-content-row-even";
            }*/
            $get_customer_acl_destination=get_customer_acl_destination($row['customer_id']);
            $row['shared_acl_cnt']=count($get_customer_acl_destination);
            $all_friends[]=$row;
            //print_r($row);
            ?>

            <div class="left-friend-list-content-row <?php echo($odd_even_class); ?> <?php echo($last_class); ?>" data-customer_id="<?php echo($row['customer_id']); ?>" data-customer_name="<?php echo($row['name']); ?>">
                <div class="profile-info-box" style="float: left;">
                    <div style="float: left">
                        <img class="friend_short_image" src="<?php echo(($row['profile_image']!="")?$row['profile_image']:ROOT_URL.'/assets/img/profiles/demo-user.jpg'); ?>" alt="<?php echo($row['profile_image']); ?>">
                    </div>
                    <div class="friend_info" style="float: left;">
                        <div class="friend_name"><?php echo($row['display_name']); ?></div>
                        <div class="friend_tag_id"><?php echo($row['tag_id']); ?>: <?php echo($row['shared_acl_cnt']); ?></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="friend-action-box">
                        <span class="friend-action accept-action" data-friend_id="<?php echo($row['customer_id']); ?>">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </span>
                        <span class="friend-action reject-action" data-friend_id="<?php echo($row['customer_id']); ?>">
                            <i class="fa fa-ban" aria-hidden="true"></i>
                        </span>
                </div>
                <div class="clearfix"></div>
            </div>
        <?php
        }
        ?>

        <?php
        $sql_request_friends=$db->query("SELECT * FROM `friends_data`
            LEFT JOIN `customers_data` ON `friends_data`.`friend_id`=`customers_data`.`customer_id`
            WHERE `friends_data`.`customer_id`='".$_SESSION['customer_id']."' AND `friends_data`.`status`='request'");
        $all_friends=array();
        $i=0;
        while($row=$sql_request_friends->fetch_assoc()){
            $i++;
            $last_class="";
            if($i==$sql_request_friends->num_rows){
                $last_class="left-friend-list-content-row-last";
            }
            $odd_even_class="left-friend-list-content-row-odd";
            /*if(intval($i/2)*2==$i){
                $odd_even_class="left-friend-list-content-row-even";
            }*/
            $get_customer_acl_destination=get_customer_acl_destination($row['customer_id']);
            $row['shared_acl_cnt']=count($get_customer_acl_destination);
            $all_friends[]=$row;
            //print_r($row);
            ?>

            <div class="left-friend-list-content-row <?php echo($odd_even_class); ?> <?php echo($last_class); ?>" data-customer_id="<?php echo($row['customer_id']); ?>" data-customer_name="<?php echo($row['name']); ?>">
                <div class="profile-info-box" style="float: left;">
                    <div style="float: left">
                        <img class="friend_short_image" src="<?php echo(($row['profile_image']!="")?$row['profile_image']:ROOT_URL.'/assets/img/profiles/demo-user.jpg'); ?>" alt="<?php echo($row['profile_image']); ?>">
                    </div>
                    <div class="friend_info" style="float: left;">
                        <div class="friend_name"><?php echo($row['display_name']); ?></div>
                        <div class="friend_tag_id"><?php echo($row['tag_id']); ?>: <?php echo($row['shared_acl_cnt']); ?></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="friend-action-box">

                </div>
                <div class="clearfix"></div>
            </div>
        <?php
        }
}
function get_rejected_friends($data){
    global $db;
        $sql_rejected_friends=$db->query("SELECT * FROM `friends_data`
                LEFT JOIN `customers_data` ON `friends_data`.`customer_id`=`customers_data`.`customer_id`
                WHERE `friends_data`.`friend_id`='".$_SESSION['customer_id']."' AND `friends_data`.`status`='rejected'");
        $all_friends=array();
        $i=0;
        while($row=$sql_rejected_friends->fetch_assoc()){
            $i++;
            $last_class="";
            if($i==$sql_rejected_friends->num_rows){
                $last_class="left-friend-list-content-row-last";
            }
            $odd_even_class="left-friend-list-content-row-odd";
            /*if(intval($i/2)*2==$i){
                $odd_even_class="left-friend-list-content-row-even";
            }*/
            $get_customer_acl_destination=get_customer_acl_destination($row['customer_id']);
            $row['shared_acl_cnt']=count($get_customer_acl_destination);
            $all_friends[]=$row;
            //print_r($row);
            ?>

            <div class="left-friend-list-content-row <?php echo($odd_even_class); ?> <?php echo($last_class); ?>" data-customer_id="<?php echo($row['customer_id']); ?>" data-customer_name="<?php echo($row['name']); ?>">
                <div class="profile-info-box" style="float: left;">
                    <div style="float: left">
                        <img class="friend_short_image" src="<?php echo(($row['profile_image']!="")?$row['profile_image']:ROOT_URL.'/assets/img/profiles/demo-user.jpg'); ?>" alt="<?php echo($row['profile_image']); ?>">
                    </div>
                    <div class="friend_info" style="float: left;">
                        <div class="friend_name"><?php echo($row['display_name']); ?></div>
                        <div class="friend_tag_id"><?php echo($row['tag_id']); ?>: <?php echo($row['shared_acl_cnt']); ?></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="friend-action-box">
                        <span class="friend-action delete-action" data-friend_id="<?php echo($row['customer_id']); ?>">
                            <i class="fa fa-trash-o" aria-hidden="true"></i>
                        </span>
                </div>
                <div class="clearfix"></div>
            </div>
        <?php
        }
        ?>

        <?php
        $sql_rejected_friends=$db->query("SELECT * FROM `friends_data`
                LEFT JOIN `customers_data` ON `friends_data`.`friend_id`=`customers_data`.`customer_id`
                WHERE `friends_data`.`customer_id`='".$_SESSION['customer_id']."' AND `friends_data`.`status`='rejected'");
        $all_friends=array();
        $i=0;
        while($row=$sql_rejected_friends->fetch_assoc()){
            $i++;
            $last_class="";
            if($i==$sql_rejected_friends->num_rows){
                $last_class="left-friend-list-content-row-last";
            }
            $odd_even_class="left-friend-list-content-row-odd";
            /*if(intval($i/2)*2==$i){
                $odd_even_class="left-friend-list-content-row-even";
            }*/
            $get_customer_acl_destination=get_customer_acl_destination($row['customer_id']);
            $row['shared_acl_cnt']=count($get_customer_acl_destination);
            $all_friends[]=$row;
            //print_r($row);
            ?>

            <div class="left-friend-list-content-row <?php echo($odd_even_class); ?> <?php echo($last_class); ?>" data-customer_id="<?php echo($row['customer_id']); ?>" data-customer_name="<?php echo($row['name']); ?>">
                <div class="profile-info-box" style="float: left;">
                    <div style="float: left">
                        <img class="friend_short_image" src="<?php echo(($row['profile_image']!="")?$row['profile_image']:ROOT_URL.'/assets/img/profiles/demo-user.jpg'); ?>" alt="<?php echo($row['profile_image']); ?>">
                    </div>
                    <div class="friend_info" style="float: left;">
                        <div class="friend_name"><?php echo($row['display_name']); ?></div>
                        <div class="friend_tag_id"><?php echo($row['tag_id']); ?>: <?php echo($row['shared_acl_cnt']); ?></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="friend-action-box">

                </div>
                <div class="clearfix"></div>
            </div>
        <?php
        }
}
function get_friends_for_dialog($data){
    global $db;
    //$customer_id=$data['customer_id'];
    ?>
    <div class="current-friends-box">
        <?php
        $friend_ids=array();
        $sql_friends=$db->query("SELECT * FROM `friends_data`
                WHERE (`customer_id`='".$_SESSION['customer_id']."' OR `friend_id`='".$_SESSION['customer_id']."') AND `status`='accepted'");
        if($sql_friends->num_rows>0){
            while($row=$sql_friends->fetch_assoc()){
                if($row['friend_id']!=$_SESSION['customer_id']){
                    $friend_ids[]=$row['friend_id'];
                }else{
                    $friend_ids[]=$row['customer_id'];
                }
            }
        }
        if(count($friend_ids)>0){
            $friend_ids_str = implode(",", $friend_ids);
            $all_friends = array();
            $sql_friends = $db->query("SELECT * FROM `customers_data` WHERE `customer_id` IN (".$friend_ids_str.")");
            $i = 0;
            while ($row = $sql_friends->fetch_assoc()){
                $all_friends[] = $row;
                $friend_id = $row['customer_id'];
                $i++;
                $last_class = "";
                if ($i==count($all_friends)){
                    $last_class = "left-friend-list-content-row-last";
                }
                $odd_even_class = "left-friend-list-content-row-odd";
                /*if(intval($i/2)*2==$i){
                    $odd_even_class="left-friend-list-content-row-even";
                }*/
                $get_customer_acl_destination = get_customer_acl_destination($friend_id);
                $row['shared_acl_cnt'] = count($get_customer_acl_destination);
                ?>
                <div class="left-friend-list-content-row <?php echo ($odd_even_class); ?> <?php echo ($last_class); ?>" data-friend_id="<?php echo ($friend_id); ?>" data-customer_name="<?php echo ($row['name']); ?>">
                    <div class="customer-profile-info-box">
                        <div style="float: left">
                            <img class="friend_short_image"
                                 src="<?php echo (($row['profile_image']!="")?$row['profile_image']:ROOT_URL.'/assets/img/profiles/demo-user.jpg'); ?>"
                                 alt="<?php echo ($row['profile_image']); ?>">
                        </div>
                        <div class="friend_info" style="float: left;">
                            <div class="friend_name"><?php echo ($row['display_name']); ?></div>
                            <div class="friend_tag_id"><?php echo ($row['tag_id']); ?>: <?php echo ($row['shared_acl_cnt']); ?></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            <?php
            }
        }
        ?>
    </div>
<?php
}
function show_shared_tunnels(){
    global $db;
    $sql_tunnel= $db->query("SELECT * FROM `shared_tunnel` WHERE `shared_with`=".$db->real_escape_string($_SESSION['customer_id']));
    $tunnels="";
    if($sql_tunnel->num_rows>0){

        while($row_tunnel=$sql_tunnel->fetch_assoc()){
            $tunnels.=$row_tunnel['tunnel_id'].",";
        }
        $tunnels=rtrim($tunnels, ",");
    }
    if($tunnels==""){
        return false;
    }

    $tunnel = "SELECT `tunnels_data`.*, `remote_server_list`.`server_name` `location`, `real_ip_list`.`is_active` `active` FROM `tunnels_data` left join `real_ip_list` on `tunnels_data`.`real_ip`=`real_ip_list`.`real_ip` left join `remote_server_list` on `tunnels_data`.`location`=`remote_server_list`.`id` WHERE `tunnels_data`.`tunnel_id` IN (".$tunnels.") and `tunnels_data`.`is_deleted`=0";
    $sql=$db->query($tunnel." order by group_id asc, group_id");
    $data=array();
    while($row=$sql->fetch_assoc()){
        $data[]=$row;
    }

    $cloud_data=array();
    $cloud_sql="SELECT * FROM `clouds_data` WHERE `email`='".$_SESSION['customer_email']."' AND `is_shared`=1";
    $query=$db->query($cloud_sql);
    if($query->num_rows==0){
        $cloud_insert_sql="INSERT INTO `clouds_data` (`cloud_name`,`email`,`is_shared`) VALUES ('shared','".$_SESSION['customer_email']."','1')";
        $cloud_insert_query=$db->query($cloud_insert_sql);
        $cloud_insert_id=$db->insert_id;
        $cloud_data=array('cloud_id'=>$cloud_insert_id,'cloud_name'=>"shared",'is_searchable'=>1);
    }else{
        $cloud_data=$query->fetch_assoc();
    }

    $cloud_id=$cloud_data['cloud_id'];
    $cloud_name=$cloud_data['cloud_name'];
    $is_searchable=$cloud_data['is_searchable'];

    $tunnels_data=array();
    $tunnel_ids="";

    foreach($data as $tunnel_data){
        $tunnel_ids.=$tunnel_data['tunnel_id'];
        $tunnel_ids.=",";

        $acl_ids=array();
        $installed_acl_ids=array();
        $real_ips=array();
        $installed_real_ips=array();

        $sql="select id from tunnel_acl_relation where tunnel_id='".$tunnel_data['tunnel_id']."'";
        $res=$db->query($sql);
        while($row=$res->fetch_assoc()){
            $acl_ids[]=$row['id'];
        }
        if($tunnel_data['real_ip']!=""){
            $real_ips[]=$tunnel_data['real_ip'];
        }
        unset($tunnel_data['real_ip']);
        if(count($acl_ids)>0){
            $acl_ids_str=implode(",",$acl_ids);
            $sql="SELECT `destination-real_ip` FROM `destination` WHERE `acl_id` IN (".$acl_ids_str.") GROUP BY `destination-real_ip`";
            $res=$db->query($sql);
            while($row=$res->fetch_assoc()){
                if(!in_array($row['destination-real_ip'],$real_ips)){
                    if($row['destination-real_ip']!=""){
                        $real_ips[]=$row['destination-real_ip'];
                    }
                }
            }
        }
        $sql="select acl_id from user_acl_relation where tunnel_id='".$tunnel_data['tunnel_id']."' and status=1";
        $res=$db->query($sql);
        while($row=$res->fetch_assoc()){
            $installed_acl_ids[]=$row['acl_id'];
        }
        if(count($installed_acl_ids)>0){
            $acl_ids_str=implode(",",$installed_acl_ids);
            $sql="SELECT `destination-real_ip` FROM `destination` WHERE `acl_id` IN (".$acl_ids_str.") GROUP BY `destination-real_ip`";
            $res=$db->query($sql);
            while($row=$res->fetch_assoc()){
                if(!in_array($row['destination-real_ip'],$installed_real_ips)){
                    if($row['destination-real_ip']!=""){
                        $installed_real_ips[]=$row['destination-real_ip'];
                    }
                }
            }
        }
        $tunnel_data['real_ip']=$real_ips;
        $tunnel_data['installed_real_ips']=$installed_real_ips;
        $tunnels_data[]=$tunnel_data;
    }
    $tunnel_ids=trim($tunnel_ids,",");

    ?>
    <div class="cloud-row cloud-row-<?php echo($cloud_id); ?>" data-cid="<?php echo($cloud_id); ?>">
        <div class="cloud-tunnels cloud-tunnels-<?php echo($cloud_id); ?>">
            <div class="page-content cloud-content">
                <div class="content" style="padding-top: 0px;">
                    <div class="page-title">
                        <div class="cloud-name cloud-name-<?php echo($cloud_id); ?>">
                            <?php echo($cloud_name); ?>
                            <span class="cloud-cost cloud-cost-<?php echo($cloud_id); ?>"> &nbsp;( Total cost =0 )</span>
                            <!--                            <span class="cloud-cost cloud-cost-<?php /*echo($cloud_id); */?>"> &nbsp;( Total cost = <?php /*echo(get_cloud_cost($cloud_id,$tunnel_ids)*cash_to_point()); */?> )</span>
                            -->                        </div>
                        <span class="delete_cloud" data-val="<?php echo $cloud_id ?>"><i class="fa fa-trash pull-right cursor" data-toggle="tooltip" data-placement="top" title="Delete this Cloud"></i></span>
                        <?php
                        $switch_checked_val=($is_searchable==1?"checked":"");
                        ?>
                        <div class="cloud_searchable_switch_block">
                            <input id="cmn-toggle-cloud-<?php echo $cloud_id; ?>" class="cmn-toggle cmn-toggle-round cloud_searchable_switch" data-cloud_id="<?php echo $cloud_id; ?>" type="checkbox" <?php echo($switch_checked_val); ?>>
                            <label for="cmn-toggle-cloud-<?php echo $cloud_id; ?>"></label>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    <div data-uid="<?php echo $_SESSION['user_id'] ?>" class="just list Parks">

                        <div class="list_header">
                            <div class="meta" data-toggle="tooltip" data-placement="right" title="ACL"><i class="fa fa-eye"></i></div>
                            <div class="meta" data-toggle="tooltip" data-placement="right" title="Create ACL"><i class="fa fa-cogs"></i></div>

                            <div class="meta" id="SortByName" data-toggle="tooltip" data-placement="right" title="Add tunnels"><a href="javascript:void(0);" data-val="<?php echo $cloud_id; ?>" data-mail="<?php echo $_SESSION['email'] ?>" data-count="0" class="tunnel_add_form_btn"><i class="fa fa-fw fa-plus-circle"></i></a></div>

                            <div class="meta" id="" data-toggle="tooltip" data-placement="right" title="Save all"><a href="javascript:void(0);" data-val="<?php echo $cloud_id; ?>" data-count="0" class="all_tunnel_save_btn"><i class="fa fa-floppy-o"></i></a></div>

                            <div class="meta width-30"><div class="cursor chk_all_tunnel" data-toggle="tooltip" data-placement="bottom" data-val="0" title="Select all tunnels"><i class="fa fa-square-o"></i></div><!--<a href="javascript:void(0);" class="tunnel_vew_by_tnl tunnel_vew_by_tnl_<?php /*echo $cloud_id; */?>" data-cloud="<?php /*echo $cloud_id; */?>" data-dif="client"><i class="fa fa-sort"></i></a>--></div>

                            <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Groups"><i class="fa fa-fw fa-group"></i>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="tunnel_vew_by_grp" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>

                            <div class="meta width-120" data-toggle="tooltip" data-placement="bottom" title="Tunnel name"><i class="fa fa-cog"></i><!--<a href="javascript:void(0);" class="tunnel_vew_by_name" data-cloud="<?php /*echo $cloud_id; */?>" data-dif="asc"><i class="fa fa-sort"></i></a>--></div>
                            <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Bidirection mode"><i class="fa fa-chevron-left"></i><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="tunnel_vew_by_bidirection" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>
                            <div class="meta width-80" data-toggle="tooltip" data-placement="bottom" title="Location"><i class="fa fa-fw fa-globe"></i></div>
                            <div class="meta width-100" data-toggle="tooltip" data-placement="bottom" title="VPN IP"><i class="fa  fa-map-pin"></i></div>
                            <div class="meta width-60" data-toggle="tooltip" data-placement="bottom" title="Points" style="margin-left: 4px;"><i class="fa fa-fw fa-dollar"></i></div>
                            <div class="meta width-140" style="width: 137px!important;" data-toggle="tooltip" data-placement="bottom" title="Real IP">Real IP</div>
                            <div class="meta width-60" data-toggle="tooltip" data-placement="bottom" title="Gateway mode">Gateway<!--<a href="javascript:void(0);" class="tunnel_vew_by_bidirection" data-cloud="<?php /*echo $cloud_id; */?>" data-dif="asc"><i class="fa fa-sort"></i></a>--></div>
                            <div class="meta"></div>
                        </div>
                        <form id="tunnels_form_field" style="display:none;">
                            <input type="button" class="btn btn-sm btn-primary btn_add_tunnel" data-cloud="<?php echo $cloud_id; ?>" value="Submit">
                            <input type="reset" class="btn btn-sm btn-warning" id="tunnel_form_close_btn" value="Cancel">
                        </form>
                        <div class="tunnel_body tunnel_body_<?php echo $cloud_id; ?>">
                            <?php
                            //echo tunnels($tunnels_data);
                            ?>
                        </div>
                        <div id="tunnel_body_pagenation_<?php echo $cloud_id; ?>">

                        </div>
                        <script>
                            console.log("<?php echo($tunnel_ids); ?>");
                            $("#tunnel_body_pagenation_<?php echo $cloud_id; ?>").pagination({
                                dataSource: [<?php echo($tunnel_ids); ?>],
                                pageSize: 5,
                                autoHidePrevious: true,
                                autoHideNext: true,
                                callback: function(data, pagination) {
                                    console.log(data);
                                    console.log(pagination);
                                    // template method of yourself
                                    var html = sponsor_tunnel_template(data,".tunnel_body_<?php echo $cloud_id; ?>");
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php

}
function destroy_account($token){
    global $db;
    $customer_sql="select `customer_id` from `customers_data` where `token`='".$token."'";
    $customer_query=$db->query($customer_sql);
    if($customer_query->num_rows>0){
        $customer_data=$customer_query->fetch_assoc();
        $customer_id=$customer_data['customer_id'];
        $clouds_sql="select `cloud_id` from `clouds_data` where `user_token`='".$token."'";
        $clouds_query=$db->query($clouds_sql);
        $db->query("DELETE FROM `customers_data` WHERE `token`='".$token."'");
        $db->query("DELETE FROM `friends_data` WHERE `customer_id`='".$customer_id."' OR `friend_id`='".$customer_id."'");
        $db->query("DELETE FROM `customers_data` WHERE `token`='".$token."'");
        if($clouds_query->num_rows>0){
            while($cloud_data=$clouds_query->fetch_assoc()){
                $cloud_id=$cloud_data['cloud_id'];
                $tunnels_sql="select `tunnel_id`,`real_ip` from `tunnels_data` where `cloud_id`='".$cloud_id."'";
                $tunnels_query=$db->query($tunnels_sql);
                if($tunnels_query->num_rows>0){
                    while($tunnel_data=$tunnels_query->fetch_assoc()){
                        $tunnel_id=$tunnel_data['tunnel_id'];
                        $acls_sql="select `id` from `tunnel_acl_relation` where `tunnel_id`=".$tunnel_id;
                        $acls_query=$db->query($acls_sql);
                        if($acls_query->num_rows>0){ //clean all acls
                            while($acl_data=$acls_query->fetch_assoc()){
                                $acl_id=$acl_data['id'];
                                $destination_real_ip_sql="select `destination-real_ip` from `destination` where `acl_id`=".$acl_id;
                                $destination_real_ip_query=$db->query($destination_real_ip_sql);
                                if($destination_real_ip_query->num_rows>0){
                                    $destination_data=$destination_real_ip_query->fetch_assoc();
                                    $destination_real_ip=$destination_data['destination-real_ip'];
                                    if($destination_real_ip!="" && $destination_real_ip!="0"){
                                        unset_real_ip($destination_real_ip,$acl_id); //clean all real ips
                                    }
                                }
                                $db->query("DELETE FROM `tunnel_acl_relation` WHERE `id`=".$acl_id);
                                $db->query("DELETE FROM `destination` WHERE `acl_id`=".$acl_id);
                                $db->query("DELETE FROM `d_final` WHERE `acl_id`=".$acl_id);
                                $db->query("DELETE FROM `c_firewall` WHERE `acl_id`=".$acl_id);
                                $db->query("DELETE FROM `c_forwarding` WHERE `acl_id`=".$acl_id);
                                $db->query("DELETE FROM `c_qos` WHERE `acl_id`=".$acl_id);
                                $db->query("DELETE FROM `c_routing` WHERE `acl_id`=".$acl_id);
                                $db->query("DELETE FROM `source` WHERE `acl_id`=".$acl_id);
                                $db->query("DELETE FROM `s_aliasing` WHERE `acl_id`=".$acl_id);
                                $db->query("DELETE FROM `s_firewall` WHERE `acl_id`=".$acl_id);
                                $db->query("DELETE FROM `s_qos` WHERE `acl_id`=".$acl_id);
                                $db->query("DELETE FROM `s_tos` WHERE `acl_id`=".$acl_id);
                                $db->query("DELETE FROM `user_acl_relation` WHERE `acl_id`=".$acl_id);
                            }
                        }
                        $tunnel_real_ip=$tunnel_data["real_ip"];
                        if($tunnel_real_ip!="" && $tunnel_real_ip!="0"){
                            unset_real_ip($tunnel_real_ip,$acl_id); //clean all real ips
                        }
                        $db->query("DELETE FROM `tunnels_data` WHERE `tunnel_id`=".$tunnel_id);
                        $db->query("DELETE FROM `shared_tunnel` WHERE `tunnel_id`=".$tunnel_id);
                    }
                }
                $db->query("DELETE FROM `shared_tunnel` WHERE `cloud_id`=".$cloud_id);
            }
        }
        $db->query("DELETE FROM `clouds_data` WHERE `user_token`='".$token."'");
        $db->query("DELETE FROM `shared_tunnel` WHERE `user_id`='".$customer_id."' OR `shared_with`='".$customer_id."'");
        return array("status"=>1, "msg"=>"success");
    }else{
        return array("status"=>0, "msg"=>"error");
    }
}
function unset_real_ip($real_ip,$acl_id){
    global $db;
    $destination_real_ip_sql="select `acl_id` from `destination` where `destination-real_ip`='".$real_ip."'";
    $destination_real_ip_query=$db->query($destination_real_ip_sql);
    $n1=$destination_real_ip_query->num_rows;
    $tunnel_real_ip_sql="select `tunnel_id` from `tunnels_data` where `real_ip`='".$real_ip."'";
    $tunnel_real_ip_query=$db->query($tunnel_real_ip_sql);
    $n2=$tunnel_real_ip_query->num_rows;
    if(($n1==1 && $n2==0) || ($n1==0 && $n2==1)){
        if($db->query("update `real_ip_list` set `in_use`='0' where `real_ip`='".$real_ip."'"))
            return 1;
        else
            return 0;
    }else{
        return 0;
    }
}
function get_cost_data_from_tunnel_id($tunnel_id){
    global $db;
    $cloud_id=0;
    $sql="select `cloud_id` from `tunnels_data` where `tunnel_id`=".$tunnel_id;
    $query=$db->query($sql);
    if($query->num_rows>0){
        $row=$query->fetch_assoc();
        $cloud_id=$row['cloud_id'];
    }
    $cloud_cost=get_cloud_cost($cloud_id,"");
    $tunnel_cost=get_tunnel_cost($tunnel_id);
    return array("cloud_id"=>$cloud_id,"cloud_cost"=>$cloud_cost,"tunnel_id"=>$tunnel_id,"tunnel_cost"=>$tunnel_cost);
}
function get_remote_server_info($data){
    $res=remote($data['tunnel_id'], "get_remote_server_info", $data, "a", $data["token"]);
    if($res==1){
        return array("toclient"=>$_SESSION['token'], "status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'get_remote_server_info', 'value'=>$data['tunnel_id']);
    }
}
function set_remote_server_info($data){
    $res=remote($data['tunnel_id'], "set_remote_server_info", $data, "a", $data["token"]);
    if($res==1){
        return array("toclient"=>$_SESSION['token'], "status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'set_remote_server_info', 'value'=>$data['tunnel_id']);
    }
}
function save_diagram_data($data){
    global $db;
    $diagram_data=$data['diagram_data'];
    $diagram_data_arr=json_decode($diagram_data,true);
    //print_r($diagram_data_arr);
    $clear_sql="TRUNCATE TABLE `diagram_nodes`";
    $db->query($clear_sql);
    foreach($diagram_data_arr['nodeDataArray'] as $node){
        $insert_sql="INSERT INTO `diagram_nodes` (`id`,`loc`,`text`,`color`) VALUES (".$node['id'].",'".$node['loc']."','".$node['text']."','".$node['color']."')";
        $db->query($insert_sql);
    }
    $clear_sql="TRUNCATE TABLE `diagram_links`";
    $db->query($clear_sql);
    foreach($diagram_data_arr['linkDataArray'] as $link){
        $from_node=isset($link['from']) ? $link['from']:0;
        $to_node=isset($link['to']) ? $link['to']:0;
        $text=isset($link['text']) ? $link['text']:"";
        $curviness=isset($link['curviness']) ? $link['curviness']:0;
        $color=isset($link['color']) ? $link['color']:"#000000";
        $points=isset($link['points']) ? implode(",",$link['points']) : "";
        $insert_sql="INSERT INTO `diagram_links` (`from_node`,`to_node`,`text`,`curviness`,`color`,`points`) VALUES (".$from_node.",".$to_node.",'".$text."','".$curviness."','".$color."','".$points."')";
        $db->query($insert_sql);
    }
    update_remote_server_data($diagram_data_arr);
    return array('status'=>1);
}
function update_remote_server_data($diagram_data_arr){
    global $db;
    $remote_server_ids=array();
    foreach($diagram_data_arr['nodeDataArray'] as $node){
        $remote_server_ids[]=$node['id'];
    }
    $cur_remote_server_query=$db->query("SELECT * FROM `remote_server_list`");
    while($row=$cur_remote_server_query->fetch_assoc()){
        if(!in_array($row['id'],$remote_server_ids)){
            if($row['id']!=0){
                //$db->query("DELETE FROM `remote_server_list` WHERE `id`=".$row['id']);
                $data=array('id'=>$row['id']);
                remote_server_delete($data);
            }
        }
    }
}