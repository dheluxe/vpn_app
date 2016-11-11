<?php

include_once(dirname(dirname(__FILE__))."/includes/config.php");
include_once("api_function1.php");
//////////////////////////////////////////////////////////////////////////////////////////
function job_queue_info(){
  global $db;
  $arr=array();
  $sql=$db->query("SELECT * FROM `job_queue` WHERE `is_complete_action`<>2 GROUP BY `tunnel_id`");
  if($sql->num_rows>0){
    while($res=$sql->fetch_assoc()){
      $arr[]=$res['tunnel_id'];
    }
  }
  return $arr;
}

//server user name
function server_username(){
    $id=time().rand();
    $b=strlen($id);
    $c=5;
    $d=$c-$b;
    $a="";
    for($i=0; $i<$d; $i++){
        $j=0;
        $a.=$j;

    }
    return 'VPN'.$a.$id.'S';
}

//server password
function server_password(){
    $array=array("1"=>"1", "2"=>"2", "3"=>"3", "4"=>"4", "5"=>"5", "6"=>"6", "7"=>"7", "8"=>"8", "9"=>"9", "0"=>"0", "a"=>"a", "b"=>"b", "c"=>"c", "d"=>"d", "e"=>"e", "f"=>"f", "g"=>"g", "h"=>"h", "i"=>"i", "j"=>"j", "k"=>"k", "l"=>"l", "m"=>"m", "n"=>"n", "o"=>"o", "p"=>"p", "q"=>"q", "r"=>"r", "s"=>"s", "t"=>"t", "u"=>"u", "v"=>"v", "w"=>"w", "x"=>"x", "y"=>"y", "z"=>"z", "1"=>"1", "2"=>"2", "3"=>"3", "4"=>"4", "5"=>"5", "6"=>"6", "7"=>"7", "8"=>"8", "9"=>"9", "0"=>"0", "A"=>"A", "B"=>"B", "C"=>"C", "D"=>"D", "E"=>"E", "F"=>"F", "G"=>"G", "H"=>"H", "I"=>"I", "J"=>"J", "K"=>"K", "L"=>"L", "M"=>"M", "N"=>"N", "O"=>"O", "P"=>"P", "Q"=>"Q", "R"=>"R", "S"=>"S", "T"=>"T", "U"=>"U", "V"=>"V", "W"=>"W", "X"=>"X", "Y"=>"Y", "Z"=>"Z", "@"=>"@", "#"=>"#", "&"=>"&", "%"=>"%", "1"=>"1", "2"=>"2", "3"=>"3", "4"=>"4", "5"=>"5", "6"=>"6", "7"=>"7", "8"=>"8", "9"=>"9", "0"=>"0");
    $var="";
    for($i=0; $i<6; $i++){
        $val=array_rand($array);
        $var.=$val;
    }
    return $var;
}
//to manage all job queue insertion
function remote($id, $action, $data, $group, $token){
    global $db;
    $sql="INSERT INTO `job_queue` (`tunnel_id`, `action`, `group`, `new_data`, `token`) VALUES ('".$id."', '".$action."', '".$group."', '".serialize($data)."', '".$token."')";

    //return array("status" => 0, 'data' => $token);

    if($db->query($sql)){
        //return array("status" => 0, 'data' => $sql);
        return true;
    }
    else{
      echo mysql_error();
    }

}

//login section
function dologin($data){
   global $db;
   //echo "SELECT COUNT(*) AS `total`, `login_type`, `email`, `password`, `customer_id` FROM `customers_data` WHERE `email`='" . $db->real_escape_string($data['email']) . "' AND `is_active`='1' AND `is_verfied`=1";
   $sql = $db->query("SELECT COUNT(*) AS `total`, `login_type`, `name`, `email`, `token`, `password`, `customer_id` FROM `customers_data` WHERE `email`='" . $db->real_escape_string($data['email']) . "' AND `is_active`='1' AND `is_verfied`=1");
   $row = $sql->fetch_assoc();

   if ($row['total'] == 1) {

      $pass = $data['password'];
      $pass = substr($pass, 0, 2) . $pass . substr($pass, -2, 2);
      if($row['login_type'] == 'normal'){
         if ($row['password'] == md5($pass)) {
            $_SESSION['vpn_user'] = md5($row['customer_id']);
            $_SESSION['user_id'] = $row['customer_id'];
            $_SESSION['uname']=explode(" ", $row['name']);
            $_SESSION['email'] = $row['email'];
            $_SESSION['token'] = $row['token'];
            $_SESSION['user_type'] = 'customer';
            /* $path="../app/contacts.php";
            header('location:'.$path);*/
            $return_values = array('user_id'=>$row['customer_id'], 'email'=>$row['email'], 'token'=>$row['token']);
            return array("status" => 1, 'data' => 'Login Successfull', 'type'=>'login', 'message_type'=>'reply', 'value'=>$return_values);
         }
         else {
            return array("status" => 0, 'data' => 'Wrong username or password', 'type'=>'login', 'message_type'=>'reply', 'value'=>array());
         }
      }
      else if($row['login_type'] == 'google') {
            $_SESSION['vpn_user'] = md5($row['customer_id']);
            $_SESSION['pre_user_id'] = $row['customer_id'];
            $_SESSION['user_id'] = $row['customer_id'];
            $_SESSION['uname']=explode(" ", $row['name']);
            $_SESSION['token'] = $row['token'];
            $_SESSION['user_type'] = 'customer';
             $path="contacts.php";
             header('location:'.$path); //seems strange. check how gauth works!
          return null;
      }
  }
  return array("status" => 0, 'data' => 'User not exist', 'type'=>'login', 'message_type'=>'reply', 'value'=>array());
}

//registration section
function doregister($data)
{
    global $db;
    $def_cash_qry = $db->query("SELECT `settings_value` FROM `settings` WHERE `settings_name`='default_cash'");
    $def_cash = $def_cash_qry->fetch_assoc();
    $sql = $db->query("SELECT COUNT(*) AS `total` FROM `customers_data` WHERE `email`='" . $db->real_escape_string($data['email']) . "'");
    $row = $sql->fetch_assoc();
    if ($row['total'] == 0) {
        $pass = $db->real_escape_string($data['password']);
        $pass = substr($pass, 0, 2) . $pass . substr($pass, -2, 2);
        $token=md5($data['email'].time());
        $tag_id=create_tag_id();

        if ($db->query("INSERT INTO `customers_data` (`email`, `password`, `name`, `display_name`, `tag_id`, `token`, `is_active`, `is_verfied`, `Cash_amount`) VALUES ('".$db->real_escape_string(trim($data['email']))."', '".md5($pass)."','".$data['name']."', '".$data['display_name']."', '".$tag_id."', '".$token."', 1, 1, ".$def_cash['settings_value'].")")) {
           $current_id=$db->insert_id;
           if(!empty($current_id)) {
               $sql="INSERT INTO `clouds_data` (`cloud_name`,`email`,`is_shared`) VALUES ('shared','".$data['email']."','1')";
               $db->query($sql);
              /* $subject ='Confirm Your Email Address';
               $message ='<html>
                   <head>
                       <title>"'.$subject.'"</title>
                   </head>
                   <body>
                   Thank you for registration. You are just one step behind to get the experience of latest VPN technology. Please click on the following
                   <a href="http://dev.comenzarit.com/demovpn/activate.php?id='.$current_id.'">click to confirm you email address and activate your account.</a>
                   </body>
               </html>';
               $headers = "MIME-Version: 1.0" . "\r\n";
               $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
               $headers .= 'From:DemoVPN <demovpn@comenzarit.com>' . "\r\n";
               mail($_POST['email'],$subject,$message,$headers);*/
            }
            return array("status" => 1, 'data' => 'Successfully created a new user', 'type'=>'signup', 'message_type'=>'reply', 'value'=>array());
        } else {
            return array("status" => 0, 'data' => 'Sorry, unknown error occurred, try again later', 'type'=>'signup', 'message_type'=>'reply', 'value'=>array());
        }
    } else {
        return array("status" => 0, 'data' => 'This email is already registered', 'type'=>'signup', 'message_type'=>'reply', 'value'=>array());
    }
}
function create_tag_id(){
    $numbers = '0123456789';
    $rand_num=generateRandomString($numbers, 4);
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $rand_str=generateRandomString($characters, 3);
    return $rand_num.$rand_str;
}
function generateRandomString($characters, $length = 10) {
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

//contact delete section
  function dodel_cus($data){
    global $db;
      $sql = $db->query("DELETE FROM `users_data` WHERE `id`='".$data."'");
        if ($sql) {
          return array("status" => 1, 'data' => 'Successfully Deleted');
        } else {
          return array("status" => 0, 'data' => 'Ops some error occured! try again later');
        }
    }

//profile update section
    function doupdate_profile($data){
     global $db;
     if($db->query("update `customers_data` set `name`='".$db->real_escape_string($data['name'])."', display_name='".$db->real_escape_string($data['display_name'])."', phone=".$db->real_escape_string($data['phone']).", remail='".$db->real_escape_string($data['reemail'])."' WHERE `customer_id`=".$db->real_escape_string($_SESSION['user_id'])."")){

       $arr=array("id"=>$_SESSION['user_id'], "name"=>$data['name'], "phone"=>$data['phone'], "remail"=>$data['reemail']);
       //json example array(status, message, property type, all data)
       return array("status" => 1, 'data' => 'Successfully updated', "type"=>"doupdate_profile", "value"=>$arr);
     }
   }

//change password section
    function changePassword($data){
      global $db;
      $pass = $db->real_escape_string($data['opassword']);
      $pass = substr($pass, 0, 2) . $pass . substr($pass, -2, 2);
      $npass = $db->real_escape_string($data['password']);
      $npass = substr($npass, 0, 2) . $npass . substr($npass, -2, 2);
      $sql=$db->query("SELECT * from `customers_data` where `password`='".md5($pass)."' AND `customer_id`=".$db->real_escape_string($_SESSION['user_id'])."");
      if($sql->num_rows>0){
        $db->query("update `customers_data` set `password`='".md5($npass)."' where `customer_id`=".$db->real_escape_string($_SESSION['user_id'])."");
        return array("status" => 1, 'data' => 'Successfully updated');
      }else{
        return array("status" => 0, 'data' => 'Password not match with old password');
      }
    }

//add contact manually section
  function doaddcontact(){
    global $db;
    if($db->query("INSERT INTO `users_data` SET `user_email`='".$db->real_escape_string(trim($_POST['contact_mail']))."', `name`='".$db->real_escape_string(trim($_POST['contact_name']))."'")){
        $id = $db->insert_id;
    } else {
        $sql = $db->query("SELECT `id` FROM `users_data` WHERE `user_email`='".$db->real_escape_string(trim($_POST['contact_mail']))."'");
        $row = $sql->fetch_assoc();
        $id = $row['id'];
    }
      $sql="INSERT INTO `customer_user_relations` SET `customer_id`='" . $db->real_escape_string($_SESSION['user_id']) . "', `user_id`='" . $id . "'";
      print_r($sql);
    if($db->query($sql))
    {
        return array("status" => 1, 'data' => 'Successfully added');
    } else {
        return array("status" => 0, 'data' => 'Contact already exists');
    }
  }

  function addVoucher(){
    global $db;

    $sql=$db->query("SELECT * FROM `voucher` WHERE `voucher_no`='".$_POST['voucher']."' AND `is_active`=1");
    $row = $sql->fetch_assoc();

    $sql1=$db->query("SELECT * from `customers_data` where `token`='".$db->real_escape_string($_SESSION['token'])."'");
    $row1 = $sql1->fetch_assoc();

     $voucher_id=$row['id'];
     $voucher_amt=$row['amount'];
     $cash_amt=$row1['Cash_amount'];

     $sql3=$db->query("UPDATE `customers_data` SET `Cash_amount`=$cash_amt+$voucher_amt WHERE `token`='".$db->real_escape_string($_SESSION['token'])."'");
     if($sql3){
        $db->query("UPDATE `voucher` SET `is_active`=0 where `id`=".$voucher_id);
        return array("status" => 1, 'data' => 'Successfully Applied This Voucher');
      } else {
        return array("status" => 0, 'data' => 'Invalid Voucher');
      }
}

  function update_plan($data){
    global $db;
    $sql3=$db->query("UPDATE `customers_data` SET `plan_id`='".$data."' WHERE `customer_id`=".$db->real_escape_string($_SESSION['user_id'])."");
    if($sql3){
      $arr=array("id"=>$_SESSION['user_id'], "plan_id"=>$data);
      return array("status" => 1, 'data' => 'Successfully Modify Your Plan', "type"=>"update_plan", "value"=>$arr);
    }else{
      return array("status" => 0, 'data' => 'Ops some error occured! try again later');
    }
  }

//status change php section
function status($stat, $tunnel_id){
    $html="";
    /*  if($stat >= 0 && $stat < 2) {
        $html .= '<input type="hidden" class="edit_status_s" name="status" value=' . $stat . '>';
        $html .= '<div class="status st_ch_status_' . $stat . ' tunnel_stat_' . $tunnel_id . '" type="data" data-toggle="tooltip" title="Active" data-cast="' . $_SESSION['user_id'] . '" data-val="' . $stat . '" data-id="' . $tunnel_id . '"><i class="fa fa-fw fa-circle"></i></div>';
    }*/
    if($stat==1){
        $html.='<input type="hidden" class="edit_status_s" name="status" value=1>';
        $html.='<div class="status tunnel_stat_'.$tunnel_id.'" type="data" data-toggle="tooltip" title="Active" data-cast="'.$_SESSION['user_id'].'" data-val="1" data-id="'.$tunnel_id.'"><i class="fa fa-fw fa-circle" style="color:#1D9E74"></i></div>';

    }else if($stat==0){
        $html.='<input type="hidden" class="edit_status_s" name="status" value=0>';
        $html.='<div class="status tunnel_stat_'.$tunnel_id.'" type="data" data-toggle="tooltip" title="Inactive" data-cast="'.$_SESSION['user_id'].'" data-val="0" data-id="'.$tunnel_id.'"><i class="fa fa-fw fa-circle"  style="color:#DA3838"></i></div>';
    }
    return $html;
}

function task_server_status($tunnel_id) {
    global $db;
    $sql = $db->query("SELECT rs.`remote_ip`, MIN(IFNULL(js.`status`, 0)) as status FROM `remote_server_list` as rs LEFT JOIN `job_queue` as jq ON rs.`remote_group`=jq.`group` LEFT JOIN `job_status` AS js ON js.`job_id` = jq.`job_id` AND js.`server_id`=rs.`id`  WHERE jq.`is_complete_action`=0 AND jq.`tunnel_id`=".$tunnel_id."  GROUP BY rs.`remote_ip`");
    $result = array();
    while($row=$sql->fetch_assoc())
    {
        $result[] = $row;
    }
    return $result;
}

//gateway change php section
function gateway($stat, $tunnel_id, $type){
    $html="";
    if($type!="client"){
      /*  if($stat >= 0 && $stat < 2) {
            $html .= '<input type="hidden" class="edit_gateway_s" name="gateway" value=' . $stat . '>';
            $html .= '<div class="gateway_stat_' . $stat . ' gateway tunnel_gate_' . $tunnel_id . '"  data-pos="0" type="data" data-toggle="tooltip" title="No" data-cast="' . $_SESSION['user_id'] . '" data-val="' . $stat .  '" data-id="' . $tunnel_id . '"><i class="fa fa-times"></i></div>';
        }*/
          if($stat==0){
              $html.='<input type="hidden" class="edit_gateway_s" name="gateway" value=0>';
              $html.='<div class="gateway tunnel_gate_'.$tunnel_id.'"  data-pos="0" type="data" data-toggle="tooltip" title="No" data-cast="'.$_SESSION['user_id'].'" data-val="0" data-id="'.$tunnel_id.'"><i class="fa fa-times" style="color:#DA3838"></i></div>';
            /*$html.='<div class="lock_btn" data-i="unlock"><i class="fa fa-unlock"></i></div>';
            $html.='<div class="lock_btn" data-i="lock"><i class="fa fa-lock"></i></div>';*/
          }else if($stat==1){
              $html.='<input type="hidden" class="edit_gateway_s" name="gateway" value=1>';
              $html.='<div class="gateway tunnel_gate_'.$tunnel_id.'"  data-pos="0" type="data" data-toggle="tooltip" title="Yes" data-cast="'.$_SESSION['user_id'].'" data-val="1" data-id="'.$tunnel_id.'"><i class="fa fa-check" style="color:#1D9E74"></i></div>';
            /*$html.='<div class="lock_btn" data-i="unlock"><i class="fa fa-unlock"></i></div>';
            $html.='<div class="lock_btn" data-i="lock"><i class="fa fa-lock"></i></div>';*/
        }
    }
    return $html;
}

//bidirection change php section
function biderection ($stat, $tunnel_id){
    $html="";
   if($stat==0){
        $html.='<input type="hidden" id="edit_biderection_s" name="biderection" value=0>';
        $html.='<div class="biderection tunnel_bi_'.$tunnel_id.'"  data-pos="0" type="data" data-toggle="tooltip" title="Mode 1" data-ctrl="0" data-cast="'.$_SESSION['user_id'].'" data-val="0" data-id="'.$tunnel_id.'" data-url="change_biderection">';
        $html.='<i class="fa fa-chevron-left"></i><i class="fa fa-chevron-right"></i></div>';
    }
    if($stat==1){
        $html.='<input type="hidden" id="edit_biderection_s" name="biderection" value=1>';
        $html.='<div class="biderection tunnel_bi_'.$tunnel_id.'"  data-pos="0" type="data" data-toggle="tooltip" title="Mode 2" data-ctrl="0" data-cast="'.$_SESSION['user_id'].'" data-val="1" data-id="'.$tunnel_id.'" data-url="change_biderection">';
        $html.='<i class="fa fa-chevron-left" style="color:#1D9E74"></i><i class="fa fa-chevron-right" style="color:#1D9E74"></i></div>';

    }
    if($stat==2){
        $html.='<input type="hidden" id="edit_biderection_s" name="biderection" value=2>';
        $html.='<div class="biderection tunnel_bi_'.$tunnel_id.'"  data-pos="0" type="data" data-toggle="tooltip" title="Mode 3" data-ctrl="0" data-cast="'.$_SESSION['user_id'].'" data-val="2" data-id="'.$tunnel_id.'" data-url="change_biderection">';
        $html.='<i class="fa fa-chevron-left" style="color:#1D9E74"></i><i class="fa fa-chevron-right"></i></div>';
    }
    if($stat==3){
        $html.='<input type="hidden" id="edit_biderection_s" name="biderection" value=3>';
        $html.='<div class="biderection tunnel_bi_'.$tunnel_id.'"  data-pos="0" type="data" data-toggle="tooltip" title="Mode 4" data-ctrl="0" data-cast="'.$_SESSION['user_id'].'" data-val="2" data-id="'.$tunnel_id.'" data-url="change_biderection">';
        $html.='<i class="fa fa-chevron-left"></i><i class="fa fa-chevron-right" style="color:#1D9E74"></i></div>';
    }
    return $html;
}

//Tunnels show by the tunnel function
function tunnels($tunnel, $is_shared=false){
    //print_r($tunnel);die;
  $group_arr=array('<span style="color: #ea4335;"><strong>A</strong></span>', '<span style="color: #839D1C;"><strong>B</strong></span>', '<span style="color: #00A998;"><strong>C</strong></span>', '<span style="color: #F6AE00;"><strong>D</strong></span>', '<span style="color: #4285F4;"><strong>E</strong></span>', '<span style="color: #330033;"><strong>F</strong></span>', '<span style="color: #FF404E;"><strong>G</strong></span>', '<span style="color: #FFFF00;"><strong>H</strong></span>', '<span style="color: #FF3300;"><strong>I</strong></span>', '<span style="color: #CC6600;"><strong>J</strong></span>', '<span style="color: #9999CC;"><strong>K</strong></span>', '<span style="color: #0000CC;"><strong>L</strong></span>', '<span style="color: #FF0000;"><strong>M</strong></span>', '<span style="color: #003366;"><strong>N</strong></span>', '<span style="color: #003333;"><strong>0</strong></span>', '<span style="color: #FF00CC;"><strong>P</strong></span>', '<span style="color: #FF0066;"><strong>Q</strong></span>', '<span style="color: #CC0000;"><strong>R</strong></span>', '<span style="color: #CC6600;"><strong>S</strong></span>', '<span style="color: #666666;"><strong>T</strong></span>', '<span style="color: #330066;"><strong>U</strong></span>', '<span style="color: #CC99CC;"><strong>V</strong></span>', '<span style="color: #FFCC66;"><strong>W</strong></span>', '<span style="color: #FF3399;"><strong>X</strong></span>', '<span style="color: #99CCFF;"><strong>Y</strong></span>', '<span style="color: #0099FF;"><strong>Z</strong></span>');
   $html="";
   foreach ($tunnel as $data) {

    $html.='<div class="p_div">';
        $html.='<div id="p_div_'.$data['tunnel_id'].'">';
           $dev_class = 'dev-disconnect';
           $dev_message = 'Disconnected';
            $icon='<i class="fa fa-share-square-o" aria-hidden="true"></i>';
           if($data['dev_status'] == 1){
               $dev_class = 'dev-connected';
               $dev_message = $data['DeV'];
               //$dev_message = 'Connected';
               $icon='<i class="fa fa-times" aria-hidden="true"></i>';
           }
           elseif($data['dev_status'] == 0){
               $dev_class = 'dev-connecting';
               $dev_message = 'Initiating';
               $icon='<i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>';
           }
           elseif($data['dev_status'] == -1){
               $dev_class = 'dev-disconnected';
               $dev_message = 'Disconnected';
               $icon='<i class="fa fa-share-square-o" aria-hidden="true"></i>';
           }

       $html .= '<a class="btn holbol dev_status dev_status_'.$data['tunnel_id'].' '.$dev_class.' " data-tid="' . $data['tunnel_id'] . '" data-val="' . $data['plan_id'] . '" data-toggle="tooltip" data- data-placement="bottom" style="margin-left: 0px; background-color:transparent; color: black; width:24px!important; margin-right:0px!important; margin-left: 0px!important;">'.$icon.'</a>';

       $html .= '<a class="holbol dev-status-label dev-status-label_'.$data['tunnel_id'].'" data-id="' . $data['tunnel_id'] . '" data-val="' . $data['plan_id'] . '" data-toggle="tooltip" data-placement="bottom" style="text-align: center; margin-left: 0px; background-color:transparent; color: black; border-left:none;width: 150px!important;">'.$dev_message.'</a>';

       //if($data['plan_id']!=1){
                 $html .= '<a class="btn holbol acc_type cursor acc_type_' . $data['tunnel_id'] . '" data-id="' . $data['tunnel_id'] . '" data-val="' . $data['plan_id'] . '" data-toggle="tooltip" data-placement="bottom" ' . ($data['plan_id'] == 1 ? "style='margin-left: 0px; background-color:#b9c3c8; '" : "style='margin-left: 0px; background-color:transparent;  color: black; opacity:0.25'") . '>Premium</a>';
            //}
            //if($data['route']==1){
                $html.='<a data-val="'.$data['route'].'" class="btn holbol route_change cursor tunnel_route_'.$data['tunnel_id'].'" type="data" data-pos="0" data-id="'.$data['tunnel_id'].'" '.($data['route']==1?"style='background-color:#b9c3c8'":"style='background-color:transparent;  color: black; opacity:0.25'").'>Route</a>';
            //}

            //if($data['internet']==1){
                $html.='<a data-val="'.$data['internet'].'" class="btn holbol internet_change cursor tunnel_internet_'.$data['tunnel_id'].'" type="data" data-pos="0" data-id="'.$data['tunnel_id'].'" '.($data['internet']==1?"style='background-color:#b9c3c8'":"style='background-color:transparent; color: black; opacity:0.25'").'>Internet</a>';
            //}
            $opacity = '';
            if($is_shared == false){
                $opacity = 'opacity:0.25; color: black; background-color: transparent;';
            }
            $btn_text="Sponsore";
            $check_sponsored_data=check_tunnel_sponsored($data['tunnel_id']);
            $data['sponsor']=$check_sponsored_data['status'];
            if($check_sponsored_data['status']=='1'){
                $btn_text="Sponsoring";
                $opacity = 'background-color: #b9c3c8;;';
            }
            $html.='<a data-val="" class="btn holbol sponsore sponsored_'.$data['tunnel_id'].'" type="data" data-pos="0" data-tid="'.$data['tunnel_id'].'"  data-cloud="'.$data['cloud_id'].'" data-u="'.$_SESSION['user_id'].'" style="background-color:#1D9E74;'.$opacity.'">'.$btn_text.'</a>';

            $html.='<a class="btn holbol change_tunnel change_tunnel_'.$data['tunnel_id'].'" data-id="'.$data['tunnel_id'].'" data-type="'.($data['tunnel_type']!="client"?"server":"client").'" href="javascript:void(0)" '.($data['tunnel_type']!="client"?"style='background-color:#b9c3c8'":"style='background-color:transparent;  color: black; opacity:0.25'").'>Server';

            $html.='</a>';

        $html.='</div>';

        if($data['tunnel_type']=="client"){
            if($data['status']!=0){
              $html.='<div class="list_body bg_yellow tunnel_body tunnel_body_'.$data['tunnel_id'].'">';
            }else if($data['status']==0){
                $html.='<div class="list_body bg_yellow tunnel_body tunnel_body_'.$data['tunnel_id'].'" style="background-color:#cecece">';
            }
        }else{
            if($data['status']!=0){
              $html.='<div class="list_body bg_green tunnel_body tunnel_body_'.$data['tunnel_id'].'">';
            }else if($data['status']==0){
              $html.='<div class="list_body bg_green tunnel_body tunnel_body_'.$data['tunnel_id'].'" style="background-color:#cecece">';
            }
        }

         $html.='<div class="meta">';
            $html.='<a href="javascript:void(0)" class="showACL" data-toggle="tooltip" data-placement="right" title="ACL view" data-cloud="'.$data['cloud_id'].'" data-type="'.$data['tunnel_type'].'" data-id="'.$data['tunnel_id'].'"><i class="fa fa-eye"></i></a>';
        $html.='</div>';

       $html.='<div class="meta" data-toggle="tooltip" title="Add acl">';
       $html.='<a href="javascript:void(0)" class="btn_add_acl btn_add_acl_'.$data['tunnel_id'].'" data-toggle="tooltip" data-placement="right" title="Create ACL" data-id="'.$data['tunnel_id'].'"><i class="fa fa-fw fa-wrench"></i></a>';
       $html.='</div>';

         //$html.='<div class="meta" data-toggle="tooltip" data-placement="right" title="'.($data['tunnel_type']!="client"?"Downgrade to client":"Upgrade to server").'"><a href="javascript:void(0)" class="change_tunnel change_tunnel_'.$data['tunnel_id'].'" data-type="'.$data['tunnel_type'].'" data-id="'.$data['tunnel_id'].'">'.($data['tunnel_type']!="client"?"<i class='fa fa-long-arrow-down'></i>":"<i class='fa fa-long-arrow-up'></i>").'</a></div>';

         $html.='<div class="meta" data-toggle="tooltip" title="Add clone"><a href="javascript:void(0)" class="add_clone" data-type="'.$data['tunnel_type'].'" data-id="'.$data['tunnel_id'].'"><i class="fa fa-fw fa-plus"></i></a></div>';

         $html.='<div class="meta" data-toggle="tooltip" title="Save this"><a href="javascript:void(0)" class="save_this_client" data-type="'.$data['tunnel_type'].'" data-id="'.$data['tunnel_id'].'"><i class="fa fa-floppy-o"></i></a></div>';

         $html.='<div class="meta cursor tunnel_chk tunnel_'.$data['tunnel_id'].' tunnel_grp_chk_'.$data['group_id'].'" data-val="0" data-id="'.$data['tunnel_id'].'" data-toggle="tooltip" title="Select tunnel"><i class="fa fa-fw fa-square-o"></i></div>';

         $html.='<div class="meta cursor tunnel_grp" data-toggle="tooltip" data-gid="'.$data['group_id'].'" title="'.$data['group_id'].'"><div class="group tunnel_grp_'.$data['tunnel_id'].'" type="data" data-cast="'.$_SESSION['user_id'].'" data-val="'.$data['group_id'].'" data-id="'.$data['tunnel_id'].'" data-pos="0">'.(array_key_exists($data['group_id'], $group_arr)?$group_arr[$data['group_id']]:"").'</div></div>';

         //$html.='<div class="meta width-140 tunnel_email_'.$data['tunnel_id'].'" data-toggle="tooltip" data-placement="bottom" title="'.$data['email'].'">'.$data['email'].'</div>';
         $html.='<div class="meta width-120 tunnel_display_'.$data['tunnel_id'].'" data-toggle="tooltip" data-placement="bottom" title="'.$data['display_name'].'"><a href="javascript:void(0);" class="display display_'.$data['tunnel_id'].' tunnel_editable" data-type="text" data-pk="'.$data['tunnel_id'].'" data-title="Enter display name">'.($data['display_name']!=""?$data['display_name']:"Tunnel ".$data['tunnel_id']).'</a></div>';

         $html.='<div class="meta cursor">'.biderection($data['bidirectional_mode'], $data['tunnel_id']).'</div>';
         $html.='<div class="meta width-80 tunnel_location_'.$data['tunnel_id'].'" data-toggle="tooltip" title=""><a href="javascript:void(0);" class="change_location location_'.$data['tunnel_id'].' tunnel_editable" data-type="select" data-source="request.php?request=get_server_name" data-pk="'.$data['tunnel_id'].'">'.($data['location']!=0?$data['location']:"Auto").'</a></div>';
         //new

       if($data['tunnel_type']=="client"){
           $html.='<div class="meta width-80 subnet_'.$data['tunnel_id'].'" data-toggle="tooltip" title="">Auto</div>';
       }else{
           $html.='<div class="meta width-80 subnet_'.$data['tunnel_id'].'" data-toggle="tooltip" title="">'.$data['cloud_ip'].'</div>';
       }

         $tunnel_cost = get_tunnel_cost($data['tunnel_id']);

         $html.='<div class="meta plan_cost_'.$data['tunnel_id'].'" data-toggle="tooltip" title="Tunnel points '.$tunnel_cost.'" style="width:60px;">'.$tunnel_cost.'</div>';

         $html.='<span class="not_client_'.$data['tunnel_id'].'">';
         if($data['tunnel_type']!="client"){
             $html.='<div class="real_ip_meta width-140" data-toggle="tooltip">';
             if( (count($data['real_ip'])>0) || (count($data['installed_real_ips'])>0)){
                 $html.='<div class="real_ip_select_box_'.$data['tunnel_id'].' custom_select_box" data-val="'.($data['active']!=null?$data['active']:-1).'" data-id="'.$data['tunnel_id'].'">';

                 if($data['real_ip'][0]['ip']==""){
                     $html.='<div class="custom_option active_option">';
                     $html.='<div class="display_value" data-value="">Not assigned</div>';
                     $html.='<div class="assign_action_btn" data-id="'.$data['tunnel_id'].'">';
                     $html.='<i class="fa fa-fw fa-plus"></i>';
                     $html.='</div>';
                     $html.='</div>';
                 }else{
                     $html.='<div class="custom_option active_option">';
                     $html.='<div class="display_value" data-value="'.$data['real_ip'][0]['ip'].'">'.$data['real_ip'][0]['ip'].'</div>';
                     $html.='<div class="action_btn" data-id="'.$data['tunnel_id'].'">';
                     $html.='<i class="fa fa-fw fa-times"></i>';
                     $html.='</div>';
                     $html.='</div>';
                 }
                 $html.='<div class="custom_option inactive_option hidden">';
                     $html.='<div class="display_value" data-value="">None</div>';
                 $html.='</div>';

                 $i=0;
                 foreach($data['real_ip'] as $real_ip){
                     if($i>0){
                         $html.='<div class="custom_option inactive_option inactive_option_'.$real_ip['acl_id'].' hidden" data-aid="'.$real_ip['acl_id'].'">';
                         $html.='<div class="display_value" data-value="'.$real_ip['ip'].'">'.$real_ip['ip'].'</div>';
                         $html.='<div class="action_btn" data-tid="'.$data['tunnel_id'].'">';
                         $html.='<i class="fa fa-fw fa-times"></i>';
                         $html.='</div>';
                         $html.='</div>';
                     }
                     $i++;
                 }
                 foreach($data['installed_real_ips'] as $real_ip){
                     $html.='<div class="custom_option inactive_option inactive_option_'.$real_ip['acl_id'].' hidden" data-aid="'.$real_ip['acl_id'].'">';
                     $html.='<div class="display_value" data-value="'.$real_ip['ip'].'">'.$real_ip['ip'].'</div>';
                     $html.='<div class="action_btn">';
                     $html.='<i class="fa fa-fw fa-times"></i>';
                     $html.='</div>';
                     $html.='</div>';
                 }
                 $html.='</div>';
             }else{ //if real ip is not assigned
                 $html.='<div class="real_ip_select_box_'.$data['tunnel_id'].' custom_select_box" data-val="'.($data['active']!=null?$data['active']:-1).'" data-id="'.$data['tunnel_id'].'">';
                 $html.='<div class="custom_option active_option">';
                 $html.='<div class="display_value" data-value="">Not assigned</div>';
                 $html.='<div class="assign_action_btn" data-id="'.$data['tunnel_id'].'">';
                 $html.='<i class="fa fa-fw fa-plus"></i>';
                 $html.='</div>';
                 $html.='</div>';

                 $html.='<div class="custom_option inactive_option hidden">';
                 $html.='<div class="display_value" data-value="">None</div>';
                 $html.='</div>';

                 $html.='</div>';
             }
            $html.='</div>';
            $html.='<div class="meta cursor width-60">'.gateway($data['gateway_mode'], $data['tunnel_id'], $data['tunnel_type']).'</div>';
         } else {
            $html.='<div class="meta width-140" data-toggle="tooltip" data-placement="right" title="'.($data['tunnel_type']!="client"?"":"To activate this field upgrade to server").'"><a href="javascript:void(0)" class="change_tunnel change_tunnel_'.$data['tunnel_id'].'" data-type="'.$data['tunnel_type'].'" data-id="'.$data['tunnel_id'].'">'.($data['tunnel_type']!="client"?"<i class='fa fa-long-arrow-down'></i>":"<i class='fa fa-long-arrow-up'></i>").'</a></div>';

            $html.='<div class="meta width-60" data-toggle="tooltip" data-placement="right" title="'.($data['tunnel_type']!="client"?"":"To activate this field upgrade to server").'"><a href="javascript:void(0)" class="change_tunnel change_tunnel_'.$data['tunnel_id'].'" data-type="'.$data['tunnel_type'].'" data-id="'.$data['tunnel_id'].'">'.($data['tunnel_type']!="client"?"<i class='fa fa-long-arrow-down'></i>":"<i class='fa fa-long-arrow-up'></i>").'</a></div>';
         }


       $html.='<div class="tunnel_searchable_switch_block">';
       $html.='<input id="cmn-toggle-tunnel-'.$data['tunnel_id'].'" class="cmn-toggle cmn-toggle-round tunnel_searchable_switch" data-tunnel_id="'.$data['tunnel_id'].'" type="checkbox" '.($data["is_searchable"]==1?"checked":"").'>';
       $html.='<label for="cmn-toggle-tunnel-'.$data['tunnel_id'].'"></label>';
       $html.='</div>';


         $html.='<div class="meta cursor float-right">'.status($data['status'], $data['tunnel_id']).'</div>';
         $html.='</span><div class="meta float-right" data-toggle="tooltip" title="Delete this tunnel" ><a href="javascript:void(0);" data-id="'.$data['tunnel_id'].'" class="delete_tunnel delete_tunnel_'.$data['tunnel_id'].'" data-type="'.$data['tunnel_type'].'"><i class="fa fa-fw fa-trash" style="color:#DA3838"></i></a></div>';

      $html.='</div>';
      $html.='</div>';

       //tunnel acl

       $html.='<div class="tunnel_acl_div_'.$data['tunnel_id'].' tunnel_acl_div" data-id="'.$data['tunnel_id'].'" style="display:none;">';
        $html.='<label style="border-bottom: 1px solid #000;direction: ltr;font-size: 20px;margin-left: 10px;">Source base<span class="source_no_data_p_'.$data['tunnel_id'].'" style="color: #ea4335; font-size: 15px;"></span>&nbsp;&nbsp;<input type="button" class="btn btn-xs btn-primary acl_destination_search_btn" value="Search ACL" data-tid="'.$data['tunnel_id'].'" style="margin-bottom: 3px;"/></label>';
        $html.='<div class="source_acl_content_'.$data['tunnel_id'].'"></div>';
        $html.='<label  style="border-bottom: 1px solid #000;direction: ltr;font-size: 20px;margin-left: 20px;margin-top: 5px;">Destination base<span class="destination_no_data_p_'.$data['tunnel_id'].'" style="color: #ea4335; font-size: 15px;"></span></label>';
        $deststate = ($data['tunnel_type']!="client"?"":"disabled");
        $html.='<div class="destination_acl_content destination_acl_content_'.$data['tunnel_id'].' ' .$deststate. '"></div>';
      $html.='</div>';
   }
   return $html;
}

function sponsor_tunnels($tunnel, $is_shared=true){
    //print_r($tunnel);die;
    $group_arr=array('<span style="color: #ea4335;"><strong>A</strong></span>', '<span style="color: #839D1C;"><strong>B</strong></span>', '<span style="color: #00A998;"><strong>C</strong></span>', '<span style="color: #F6AE00;"><strong>D</strong></span>', '<span style="color: #4285F4;"><strong>E</strong></span>', '<span style="color: #330033;"><strong>F</strong></span>', '<span style="color: #FF404E;"><strong>G</strong></span>', '<span style="color: #FFFF00;"><strong>H</strong></span>', '<span style="color: #FF3300;"><strong>I</strong></span>', '<span style="color: #CC6600;"><strong>J</strong></span>', '<span style="color: #9999CC;"><strong>K</strong></span>', '<span style="color: #0000CC;"><strong>L</strong></span>', '<span style="color: #FF0000;"><strong>M</strong></span>', '<span style="color: #003366;"><strong>N</strong></span>', '<span style="color: #003333;"><strong>0</strong></span>', '<span style="color: #FF00CC;"><strong>P</strong></span>', '<span style="color: #FF0066;"><strong>Q</strong></span>', '<span style="color: #CC0000;"><strong>R</strong></span>', '<span style="color: #CC6600;"><strong>S</strong></span>', '<span style="color: #666666;"><strong>T</strong></span>', '<span style="color: #330066;"><strong>U</strong></span>', '<span style="color: #CC99CC;"><strong>V</strong></span>', '<span style="color: #FFCC66;"><strong>W</strong></span>', '<span style="color: #FF3399;"><strong>X</strong></span>', '<span style="color: #99CCFF;"><strong>Y</strong></span>', '<span style="color: #0099FF;"><strong>Z</strong></span>');
    $html="";
    foreach ($tunnel as $data) {

        $html.='<div class="p_div">';
        $html.='<div id="p_div_'.$data['tunnel_id'].'">';
        $dev_class = 'dev-disconnect';
        $dev_message = 'Disconnected';
        $icon='<i class="fa fa-share-square-o" aria-hidden="true"></i>';
        if($data['dev_status'] == 1){
            $dev_class = 'dev-connected';
            $dev_message = $data['DeV'];
            //$dev_message = 'Connected';
            $icon='<i class="fa fa-times" aria-hidden="true"></i>';
        }
        elseif($data['dev_status'] == 0){
            $dev_class = 'dev-connecting';
            $dev_message = 'Initiating';
            $icon='<i class="fa fa-refresh fa-spin fa-1x fa-fw"></i>';
        }
        elseif($data['dev_status'] == -1){
            $dev_class = 'dev-disconnected';
            $dev_message = 'Disconnected';
            $icon='<i class="fa fa-share-square-o" aria-hidden="true"></i>';
        }

        $html .= '<a class="btn holbol dev_status ' . $dev_class . '" data-tid="' . $data['tunnel_id'] . '" data-val="' . $data['plan_id'] . '" data-toggle="tooltip" data- data-placement="bottom" style="margin-left: 0px; background-color:transparent; color: black; width:24px!important; margin-right:0px!important; margin-left: 0px!important;">'.$icon.'</a>';

        $html .= '<a class="holbol dev-status-label dev-status-label_'.$data['tunnel_id'].'" data-id="' . $data['tunnel_id'] . '" data-val="' . $data['plan_id'] . '" data-toggle="tooltip" data-placement="bottom" style="text-align: center; margin-left: 0px; background-color:transparent; color: black; border-left:none;width: 150px!important;">'.$dev_message.'</a>';


        //if($data['plan_id']!=1){
        $html .= '<a class="btn holbol acc_type cursor acc_type_' . $data['tunnel_id'] . '" data-id="' . $data['tunnel_id'] . '" data-val="' . $data['plan_id'] . '" data-toggle="tooltip" data-placement="bottom" ' . ($data['plan_id'] == 1 ? "style='margin-left: 0px; background-color:#b9c3c8; '" : "style='margin-left: 0px; background-color:transparent;  color: black; opacity:0.25'") . '>Premium</a>';
        //}
        //if($data['route']==1){
        $html.='<a data-val="'.$data['route'].'" class="btn holbol route_change cursor tunnel_route_'.$data['tunnel_id'].'" type="data" data-pos="0" data-id="'.$data['tunnel_id'].'" '.($data['route']==1?"style='background-color:#b9c3c8'":"style='background-color:transparent;  color: black; opacity:0.25'").'>Route</a>';
        //}

        //if($data['internet']==1){
        $html.='<a data-val="'.$data['internet'].'" class="btn holbol internet_change cursor tunnel_internet_'.$data['tunnel_id'].'" type="data" data-pos="0" data-id="'.$data['tunnel_id'].'" '.($data['internet']==1?"style='background-color:#b9c3c8'":"style='background-color:transparent; color: black; opacity:0.25'").'>Internet</a>';
        //}
        $opacity = '';
        $sponsore_class="sponsored";
        $opacity = 'opacity:1; background-color: #b9c3c8;';
        $data['sponsor']=1;
        if($is_shared == false){
            $opacity = 'opacity:0.25; color: black; background-color: transparent;';
            $sponsore_class="sponsore";
        }
        $html.='<a data-val="" class="btn holbol '.$sponsore_class.' sponsored_'.$data['tunnel_id'].'" type="data" data-pos="0" data-tid="'.$data['tunnel_id'].'"  data-cloud="'.$data['cloud_id'].'" data-u="'.$_SESSION['user_id'].'" style="'.$opacity.'">Sponsored</a>';

        $html.='<a class="btn holbol change_tunnel change_tunnel_'.$data['tunnel_id'].'" data-id="'.$data['tunnel_id'].'" data-type="'.($data['tunnel_type']!="client"?"server":"client").'" href="javascript:void(0)" '.($data['tunnel_type']!="client"?"style='background-color:#b9c3c8'":"style='background-color:transparent;  color: black; opacity:0.25'").'>Server</a>';

        $html.='</div>';

        if($data['tunnel_type']=="client"){
            if($data['status']!=0){
                $html.='<div class="list_body bg_yellow tunnel_body tunnel_body_'.$data['tunnel_id'].'">';
            }else if($data['status']==0){
                $html.='<div class="list_body bg_yellow tunnel_body tunnel_body_'.$data['tunnel_id'].'" style="background-color:#cecece">';
            }
        }else{
            if($data['status']!=0){
                $html.='<div class="list_body bg_green tunnel_body tunnel_body_'.$data['tunnel_id'].'">';
            }else if($data['status']==0){
                $html.='<div class="list_body bg_green tunnel_body tunnel_body_'.$data['tunnel_id'].'" style="background-color:#cecece">';
            }
        }

        $html.='<div class="meta">';
        $html.='<a href="javascript:void(0)" class="showACL" data-toggle="tooltip" data-placement="right" title="ACL view" data-cloud="'.$data['cloud_id'].'" data-type="'.$data['tunnel_type'].'" data-id="'.$data['tunnel_id'].'"><i class="fa fa-eye"></i></a>';
        $html.='</div>';

        $html.='<div class="meta" data-toggle="tooltip" title="Add acl">';
        $html.='<a href="javascript:void(0)" class="btn_add_acl btn_add_acl_'.$data['tunnel_id'].'" data-toggle="tooltip" data-placement="right" title="Create ACL" data-id="'.$data['tunnel_id'].'"><i class="fa fa-fw fa-wrench"></i></a>';
        $html.='</div>';

        //$html.='<div class="meta" data-toggle="tooltip" data-placement="right" title="'.($data['tunnel_type']!="client"?"Downgrade to client":"Upgrade to server").'"><a href="javascript:void(0)" class="change_tunnel change_tunnel_'.$data['tunnel_id'].'" data-type="'.$data['tunnel_type'].'" data-id="'.$data['tunnel_id'].'">'.($data['tunnel_type']!="client"?"<i class='fa fa-long-arrow-down'></i>":"<i class='fa fa-long-arrow-up'></i>").'</a></div>';

        $html.='<div class="meta" data-toggle="tooltip" title="Add clone"><a href="javascript:void(0)" class="add_clone" data-type="'.$data['tunnel_type'].'" data-id="'.$data['tunnel_id'].'"><i class="fa fa-fw fa-plus"></i></a></div>';

        $html.='<div class="meta" data-toggle="tooltip" title="Save this"><a href="javascript:void(0)" class="save_this_client" data-type="'.$data['tunnel_type'].'" data-id="'.$data['tunnel_id'].'"><i class="fa fa-floppy-o"></i></a></div>';

        $html.='<div class="meta cursor tunnel_chk tunnel_'.$data['tunnel_id'].' tunnel_grp_chk_'.$data['group_id'].'" data-val="0" data-id="'.$data['tunnel_id'].'" data-toggle="tooltip" title="Select tunnel"><i class="fa fa-fw fa-square-o"></i></div>';

        $html.='<div class="meta cursor tunnel_grp" data-toggle="tooltip" data-gid="'.$data['group_id'].'" title="'.$data['group_id'].'"><div class="group tunnel_grp_'.$data['tunnel_id'].'" type="data" data-cast="'.$_SESSION['user_id'].'" data-val="'.$data['group_id'].'" data-id="'.$data['tunnel_id'].'" data-pos="0">'.(array_key_exists($data['group_id'], $group_arr)?$group_arr[$data['group_id']]:"").'</div></div>';

        //$html.='<div class="meta width-140 tunnel_email_'.$data['tunnel_id'].'" data-toggle="tooltip" data-placement="bottom" title="'.$data['email'].'">'.$data['email'].'</div>';
        $html.='<div class="meta width-120 tunnel_display_'.$data['tunnel_id'].'" data-toggle="tooltip" data-placement="bottom" title="'.$data['display_name'].'"><a href="javascript:void(0);" class="display display_'.$data['tunnel_id'].' tunnel_editable" data-type="text" data-pk="'.$data['tunnel_id'].'" data-title="Enter display name">'.($data['display_name']!=""?$data['display_name']:"Tunnel ".$data['tunnel_id']).'</a></div>';

        $html.='<div class="meta cursor">'.biderection($data['bidirectional_mode'], $data['tunnel_id']).'</div>';
        $html.='<div class="meta width-80 tunnel_location_'.$data['tunnel_id'].'" data-toggle="tooltip" title=""><a href="javascript:void(0);" class="change_location location_'.$data['tunnel_id'].' tunnel_editable" data-type="select" data-source="request.php?request=get_server_name" data-pk="'.$data['tunnel_id'].'">'.($data['location']!=0?$data['location']:"Auto").'</a></div>';
        //new

        if($data['tunnel_type']=="client"){
            $html.='<div class="meta width-80 subnet_'.$data['tunnel_id'].'" data-toggle="tooltip" title="">Auto</div>';
        }else{
            $html.='<div class="meta width-80 subnet_'.$data['tunnel_id'].'" data-toggle="tooltip" title="">'.$data['cloud_ip'].'</div>';
        }

        $tunnel_cost =get_tunnel_cost($data['tunnel_id']);

        $html.='<div class="meta plan_cost_'.$data['tunnel_id'].'" data-toggle="tooltip" title="Tunnel points '.$tunnel_cost.'" style="width:60px;">'.$tunnel_cost.'</div>';

        $html.='<span class="not_client_'.$data['tunnel_id'].'">';
        if($data['tunnel_type']!="client"){
            $html.='<div class="real_ip_meta width-140" data-toggle="tooltip">';
            if( (count($data['real_ip'])>0) || (count($data['installed_real_ips'])>0)){
                $html.='<div class="real_ip_select_box_'.$data['tunnel_id'].' custom_select_box" data-val="'.($data['active']!=null?$data['active']:-1).'" data-id="'.$data['tunnel_id'].'">';

                if($data['real_ip'][0]['ip']==""){
                    $html.='<div class="custom_option active_option">';
                    $html.='<div class="display_value" data-value="">Not assigned</div>';
                    $html.='<div class="assign_action_btn" data-id="'.$data['tunnel_id'].'">';
                    $html.='<i class="fa fa-fw fa-plus"></i>';
                    $html.='</div>';
                    $html.='</div>';
                }else{
                    $html.='<div class="custom_option active_option">';
                    $html.='<div class="display_value" data-value="'.$data['real_ip'][0]['ip'].'">'.$data['real_ip'][0]['ip'].'</div>';
                    $html.='<div class="action_btn" data-id="'.$data['tunnel_id'].'">';
                    $html.='<i class="fa fa-fw fa-times"></i>';
                    $html.='</div>';
                    $html.='</div>';
                }
                $html.='<div class="custom_option inactive_option hidden">';
                $html.='<div class="display_value" data-value="">None</div>';
                $html.='</div>';

                $i=0;
                foreach($data['real_ip'] as $real_ip){
                    if($i>0){
                        $html.='<div class="custom_option inactive_option inactive_option_'.$real_ip['acl_id'].' hidden" data-aid="'.$real_ip['acl_id'].'">';
                        $html.='<div class="display_value" data-value="'.$real_ip['ip'].'">'.$real_ip['ip'].'</div>';
                        $html.='<div class="action_btn" data-tid="'.$data['tunnel_id'].'">';
                        $html.='<i class="fa fa-fw fa-times"></i>';
                        $html.='</div>';
                        $html.='</div>';
                    }
                    $i++;
                }
                foreach($data['installed_real_ips'] as $real_ip){
                    $html.='<div class="custom_option inactive_option inactive_option_'.$real_ip['acl_id'].' hidden" data-aid="'.$real_ip['acl_id'].'">';
                    $html.='<div class="display_value" data-value="'.$real_ip['ip'].'">'.$real_ip['ip'].'</div>';
                    $html.='<div class="action_btn">';
                    $html.='<i class="fa fa-fw fa-times"></i>';
                    $html.='</div>';
                    $html.='</div>';
                }
                $html.='</div>';
            }else{ //if real ip is not assigned
                $html.='<div class="real_ip_select_box_'.$data['tunnel_id'].' custom_select_box" data-val="'.($data['active']!=null?$data['active']:-1).'" data-id="'.$data['tunnel_id'].'">';
                $html.='<div class="custom_option active_option">';
                $html.='<div class="display_value" data-value="">Not assigned</div>';
                $html.='<div class="assign_action_btn" data-id="'.$data['tunnel_id'].'">';
                $html.='<i class="fa fa-fw fa-plus"></i>';
                $html.='</div>';
                $html.='</div>';

                $html.='<div class="custom_option inactive_option hidden">';
                $html.='<div class="display_value" data-value="">None</div>';
                $html.='</div>';

                $html.='</div>';
            }
            $html.='</div>';
            $html.='<div class="meta cursor width-60">'.gateway($data['gateway_mode'], $data['tunnel_id'], $data['tunnel_type']).'</div>';
        } else {
            $html.='<div class="meta width-140" data-toggle="tooltip" data-placement="right" title="'.($data['tunnel_type']!="client"?"":"To activate this field upgrade to server").'"><a href="javascript:void(0)" class="change_tunnel change_tunnel_'.$data['tunnel_id'].'" data-type="'.$data['tunnel_type'].'" data-id="'.$data['tunnel_id'].'">'.($data['tunnel_type']!="client"?"<i class='fa fa-long-arrow-down'></i>":"<i class='fa fa-long-arrow-up'></i>").'</a></div>';

            $html.='<div class="meta width-60" data-toggle="tooltip" data-placement="right" title="'.($data['tunnel_type']!="client"?"":"To activate this field upgrade to server").'"><a href="javascript:void(0)" class="change_tunnel change_tunnel_'.$data['tunnel_id'].'" data-type="'.$data['tunnel_type'].'" data-id="'.$data['tunnel_id'].'">'.($data['tunnel_type']!="client"?"<i class='fa fa-long-arrow-down'></i>":"<i class='fa fa-long-arrow-up'></i>").'</a></div>';
        }


        $html.='<div class="tunnel_searchable_switch_block">';
        $html.='<input id="cmn-toggle-tunnel-'.$data['tunnel_id'].'" class="cmn-toggle cmn-toggle-round tunnel_searchable_switch" data-tunnel_id="'.$data['tunnel_id'].'" type="checkbox" '.($data["is_searchable"]==1?"checked":"").'>';
        $html.='<label for="cmn-toggle-tunnel-'.$data['tunnel_id'].'"></label>';
        $html.='</div>';


        $html.='<div class="meta cursor float-right">'.status($data['status'], $data['tunnel_id']).'</div>';
        $html.='</span><div class="meta float-right" data-toggle="tooltip" title="Delete this tunnel" ><a href="javascript:void(0);" data-id="'.$data['tunnel_id'].'" class="delete_tunnel delete_tunnel_'.$data['tunnel_id'].'" data-type="'.$data['tunnel_type'].'"><i class="fa fa-fw fa-trash" style="color:#DA3838"></i></a></div>';

        $html.='</div>';
        $html.='</div>';

        //tunnel acl

        $html.='<div class="tunnel_acl_div_'.$data['tunnel_id'].' tunnel_acl_div" data-id="'.$data['tunnel_id'].'" style="display:none;">';
        $html.='<label style="border-bottom: 1px solid #000;direction: ltr;font-size: 20px;margin-left: 10px;">Source base<span class="source_no_data_p_'.$data['tunnel_id'].'" style="color: #ea4335; font-size: 15px;"></span>&nbsp;&nbsp;<input type="button" class="btn btn-xs btn-primary acl_destination_search_btn" value="Search ACL" data-tid="'.$data['tunnel_id'].'" style="margin-bottom: 3px;"/></label>';
        $html.='<div class="source_acl_content_'.$data['tunnel_id'].'"></div>';
        $html.='<label  style="border-bottom: 1px solid #000;direction: ltr;font-size: 20px;margin-left: 20px;margin-top: 5px;">Destination base<span class="destination_no_data_p_'.$data['tunnel_id'].'" style="color: #ea4335; font-size: 15px;"></span></label>';
        $deststate = ($data['tunnel_type']!="client"?"":"disabled");
        $html.='<div class="destination_acl_content destination_acl_content_'.$data['tunnel_id'].' ' .$deststate. '"></div>';
        $html.='</div>';
    }
    return $html;
}
//add cloud section
function add_cloud($data){
   global $db;
   $sql = $db->query("INSERT INTO `clouds_data` (`cloud_name`, `user_token`, `email`) VALUES ('".$db->real_escape_string($data['cloud_name'])."', '".$db->real_escape_string($data['token'])."', '".$db->real_escape_string($data['cloud_email'])."')");
       $last_id=$db->insert_id;
       if($sql){
           return array("status" => 1, 'data' => 'A new cloud added successfully.', "type"=>"add_cloud", "value"=>$last_id);
       }
       else{
           return array("status" => 1, 'data' => 'Error encounted.', "type"=>"add_cloud");
       }
}

//delete tunnel section
function delete_tunnel($data){  //for websocket
   global $db;
   $sql = $db->query("SELECT * FROM `tunnels_data` WHERE `tunnel_id`=".$data['id']);
   $row = $sql->fetch_assoc();
   if($sql->num_rows>0){
      $arr=array("data"=>$data);
      $res=remote($data['id'], "delete_tunnel", $arr, "a", $data["token"]);
      if($res==1){
        if($data['type']=="server"){
          $db->query("UPDATE `server_subnets` SET `used_ips`=0 WHERE `subnet`='".$row['cloud_ip']."'");
          $db->query("UPDATE `real_ip_list` SET `in_use`=0 WHERE `real_ip`='".$row['real_ip']."'");
        }
        return array("status" => 1, 'message_type'=>'reply', 'type'=>'delete_tunnel', 'data' => 'Your request under process, please wait...');
      }
   }
}

function gateway_change($data){
    global $db;
    $arr=array("id"=>$data['id'], "value"=>$data['val']);
    $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='gateway_change' AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
    if($sql->num_rows==0){
        $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$data['id'].", '".$data['token']."', 'gateway_change', '".serialize($arr)."')");
    }else{
        $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='gateway_change' AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
    }
    return array("status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', "type"=>"gateway_change", "value"=>$arr);
}

function status_change($data){
    global $db;
    $arr=array("id"=>$data['id'], "value"=>$data['val']);
    $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='status_change' AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
    if($sql->num_rows==0){
        $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$data['id'].", '".$data['token']."', 'status_change', '".serialize($arr)."')");
    }else{
        $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='status_change' AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
    }
    return array("status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', "type"=>"status_change", "value"=>$arr);
}

function bidirection_change($data){
  global $db;
  //$_SESSION['users_data'][$id]["bidirection_change"]=array("id"=>$id, "value"=>$val);
  $arr=array("id"=>$data['id'], "value"=>$data['val']);
  $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='bidirection_change' AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
  if($sql->num_rows==0){
    $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$data['id'].", '".$data['token']."', 'bidirection_change', '".serialize($arr)."')");
  }else{
    $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='bidirection_change' AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
  }
  //return $_SESSION['users_data'];
  //if($sql=$db->query("UPDATE `tunnels_data` SET `bidirectional_mode`=".$val." WHERE `tunnel_id`=".$id)){
      // $arr=array("id"=>$id, "value"=>$val);
      // $res=remote($_SESSION['user_id'], $id, "bidirection_change", $arr, "b");
      // if($res==1){
        return array("status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', "type"=>"bidirection_change", "value"=>$arr);
      // }
  //}
}

function internet_change($data){
  global $db;
  $arr=array("id"=>$data['id'], "value"=>$data['val']);
  $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='internet_change' AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
  if($sql->num_rows==0){
    $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$data['id'].", '".$data['token']."', 'internet_change', '".serialize($arr)."')");
  }else{
    $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='internet_change' AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
  }
      $arr=array("id"=>$data['id'], "value"=>$data['val']);
      return array("status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', "type"=>"internet_change", "value"=>$arr);
}

function route_change($data){
    /*$id, $val, $token*/
    global $db;
    $arr=array("id"=>$data['id'], "value"=>$data['val']);
    $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='route_change' AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");

    if($sql->num_rows==0){
        $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$data['id'].", '".$data['token']."', 'route_change', '".serialize($arr)."')");
    }else{
        $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='route_change' AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
    }
    $arr=array("id"=>$data['id'], "value"=>$data['val']);
    return array("status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', "type"=>"route_change", "value"=>$arr);
}

function plan_change($data){
   /* $id, $val, $token*/
  global $db;
  $arr=array("id"=>$data['id'], "value"=>$data['val']);
  $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='plan_change' AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
  if($sql->num_rows==0){
    $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$data['id'].", '".$data['token']."', 'plan_change', '".serialize($arr)."')");
  }else{
    $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='plan_change' AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
  }
  return array("status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', "type"=>"plan_change", "value"=>$arr);
}

//group change section
function group_change($id, $val, $token){
  global $db;
  //$_SESSION['users_data'][$id]["group_change"]=array("id"=>$id, "value"=>$val);
  $arr=array("id"=>$id, "value"=>$val);
  $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='group_change' AND `tunnel_id`=".$id." AND `token`='".$token."'");
  if($sql->num_rows==0){
    $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$id.", '".$token."', 'group_change', '".serialize($arr)."')");
  }else{
    $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='group_change' AND `tunnel_id`=".$id." AND `token`='".$token."'");
  }
  //if($sql=$db->query("UPDATE `tunnels_data` SET `group_id`=".$val." WHERE `tunnel_id`=".$id)){
      $arr=array("id"=>$id, "value"=>$val);
      // $res=remote($_SESSION['user_id'], $id, "group_change", $arr, "b");
      // if($res==1){
        return array("status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', "type"=>"group_change", "value"=>$arr);
      // }
  //}
}

//add tunnel section
function add_tunnel($data){
   global $db;
   $tunnel=array();
   $real_ip="";
   $subnet_ip="";
   $customer_id=0;
   $sql = $db->query("SELECT `customer_id` FROM `customers_data` WHERE `Cash_amount`>0 and `token` = '".$data['token']."'");
   if($sql->num_rows!=0){
       $row_customer=$sql->fetch_assoc();
       $customer_id=$row_customer['customer_id'];
       $m=server_username();
       $n=server_password();
       $subnet_res=$db->query("SELECT `subnet` from `server_subnets` where `used_ips`='0'");
       $subnet_num = $subnet_res->num_rows;
       if($subnet_num==0){
           return array("status" => 0, 'data' => 'Unexpected error occured.');
       }else{
           $subnet_row=$subnet_res->fetch_assoc();
           $subnet_ip = $subnet_row['subnet'];
           $db->query("UPDATE `server_subnets` SET `used_ips`='1'  WHERE `subnet`='".$subnet_ip."'");
            $tunnel[0]['uname']=$m;
            $tunnel[0]['upass']=$n;
            $tunnel[0]['cloud_id']=$data['cloud_id'];
            $tunnel[0]['email']=$data['mail_id'];
            $tunnel[0]['display_name']="";
            $tunnel[0]['bidirectional_mode']=0;
            $tunnel[0]['gateway_mode']=0;
            $tunnel[0]['cloud_ip']=$subnet_ip;
            $tunnel[0]['tunnel_type']="server";
            $tunnel[0]['group_id']=0;
            $tunnel[0]['username']=$m;
            $tunnel[0]['password']=$n;
            $tunnel[0]['token']=$data['token'];
            $tunnel[0]['location']=0;
       }
       //return array("status" => 0, 'data' => 'Unexpected error occured.');
       $res=remote("", "add_new_tunnel", $tunnel, "a", $data['token']);
       //return $res;
       if($res==1){
       }
   }else{
       return array("status" => 2, 'data' => 'You have no balace to do any operations, please recharge ur account.');
   }
   if($res==1){
      return array("status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'add_tunnel', "uid"=>$customer_id, 'value'=>$tunnel);
   }
}

function add_server_clone($data){
  global $db;
  $tunnel=array();
  $sql2=$db->query("SELECT `subnet` FROM `server_subnets` WHERE `used_ips`=0");

  if($sql2->num_rows>0){
      $row2=$sql2->fetch_assoc();
      $subnet_clone=$row2['subnet'];
      $uname=server_username();
      $upass=server_password();
      $sql1=$db->query("SELECT * FROM `tunnels_data` WHERE `tunnel_id`=".$data['id']);
      $row_clone=$sql1->fetch_assoc();
        $row_clone['uname']=$uname;
        $row_clone['upass']=$upass;
        $row_clone['cloud_ip']=$subnet_clone;
        $row_clone['token']=$data['token'];
        $row_clone['clone_id']=$data['id'];

        $tunnel['tunnel']=$row_clone;
        $tunnel['acl_info']=get_own_acl_info($data['id']);

        $res=remote("", "add_server_clone", $tunnel, "a", $data['token']);
        if($res==1){
          $db->query("UPDATE `server_subnets` SET `used_ips`='1'  WHERE `subnet`='".$subnet_clone."'");
          return array("status"=>1, "data"=>"Your request under process, please wait...", 'message_type'=>'reply', "type"=>"add_server_clone", "uid"=>$row_clone['customer_id'], "value"=>$tunnel);
        }
  }else{
    return array("status"=>0, "data"=>"Unexpected error occured, try again");
  }
}
//does nothing?
function add_client_clone($data){
  global $db;
  $tunnel=array();
    if(!isset($row2)) $row2 = array();
  $subnet_clone=$row2['subnet'];
  $sql1=$db->query("SELECT * FROM `tunnels_data` WHERE `tunnel_id`=".$data['id']);
  $row_clone=$sql1->fetch_assoc();

    $tunnel['tunnel']=$row_clone;
    $tunnel['acl_info']=get_own_acl_info($data['id']);
    $res=remote("", "add_client_clone", $tunnel, "a", $data['token']);
    if($res==1){
      return array("status"=>1, "data"=>"Your request under process, please wait...", 'message_type'=>'reply', "type"=>"add_client_clone", "uid"=>$row_clone['customer_id'], "value"=>$tunnel);
    }
  //}
}

function getTunnel($data){
    global $db;
    $tunnel = "SELECT `tunnels_data`.*, (case when (`tunnels_data`.`real_ip` = '') THEN 'Request real ip' ELSE `tunnels_data`.`real_ip`  END) real_ip, `remote_server_list`.`server_name` `location`, `real_ip_list`.`is_active` `active` FROM `tunnels_data` left join `real_ip_list` on `tunnels_data`.`real_ip`=`real_ip_list`.`real_ip` left join `remote_server_list` on `tunnels_data`.`location`=`remote_server_list`.`id` WHERE  `tunnels_data`.`cloud_id`='".$db->real_escape_string($data['cloud'])."' and `tunnels_data`.`user_token`='".$db->real_escape_string($_SESSION['token'])."' and `tunnels_data`.`is_deleted`=0";

    if($data['type']=="tunnel"){
        if($data['dif']=="server"){

            $sql=$db->query($tunnel." order by `tunnels_data`.`tunnel_type`");
        }else {
            $sql=$db->query($tunnel." order by `tunnels_data`.`tunnel_type` DESC");
        }
    }if($data['type']=="group"){
        if($data['dif']=="asc"){
            $sql=$db->query($tunnel." order by group_id");
        }else {
            $sql=$db->query($tunnel." order by group_id DESC");
        }
    } else if($data['type']=="name"){
        if($data['dif']=="asc"){
            $sql=$db->query($tunnel." order by display_name");
        }else{
            $sql=$db->query($tunnel." order by display_name DESC");
        }
    } else if($data['type']=="bidirection"){
        if($data['dif']=="asc"){
            $sql=$db->query($tunnel." order by bidirectional_mode");
        }else{
            $sql=$db->query($tunnel." order by bidirectional_mode DESC");
        }
    } else if($data['type']=="gateway"){
        if($data['dif']=="asc"){
            $sql=$db->query($tunnel." order by gateway_mode");
        }else{
            $sql=$db->query($tunnel." order by gateway_mode DESC");
        }
    }else if($data['type']=="internet"){
        if($data['dif']=="asc"){
            $sql=$db->query($tunnel." order by internet");
        }else{
            $sql=$db->query($tunnel." order by internet DESC");
        }
    }else if($data['type']=="route"){
        if($data['dif']=="asc"){
            $sql=$db->query($tunnel." order by route");
        }else{
            $sql=$db->query($tunnel." order by route DESC");
        }
    }
    $t_data=array();
    while($row=$sql->fetch_assoc()){
        $row['cost']=packages($row['tunnel_type'], $row['plan_id'], $row['tunnel_id']);
        $t_data[]=$row;
    }
    if(isset($data['rect'])) {
        $acltunnel = "SELECT `tunnels_data`.*, (case when (`tunnels_data`.`real_ip` = '') THEN 'Request real ip' ELSE `tunnels_data`.`real_ip`  END) real_ip, `remote_server_list`.`server_name` `location`, `real_ip_list`.`is_active` `active` FROM `tunnels_data` left join `real_ip_list` on `tunnels_data`.`real_ip`=`real_ip_list`.`real_ip` left join `remote_server_list` on `tunnels_data`.`location`=`remote_server_list`.`id` left join `user_acl_relation` on `user_acl_relation`.`tunnel_id` = `tunnels_data`.`tunnel_id` WHERE `tunnels_data`.`is_deleted`=0 and `user_acl_relation`.`acl_id`=". $data['rect'] . " order by `tunnels_data`.`tunnel_type`";
        $sql2=$db->query($acltunnel);
        while($row=$sql2->fetch_assoc()){
            $row['cost']=packages($row['tunnel_type'], $row['plan_id'], $row['tunnel_id']);
            $t_data[]=$row;
        }
      //  $t_data[]='sadasdasdasdas';
    }
    return $t_data;
}

function getAllTunnel($data){
    global $db;
    $this_cloud=array();
    $other_clouds=array();
    $clouds_sql="SELECT * FROM `clouds_data` WHERE `user_token`='".$_SESSION['token']."' AND `is_deleted`=0";
    $clouds_query=$db->query($clouds_sql);
    if($clouds_query->num_rows>0){
        while($row=$clouds_query->fetch_assoc()){
            if($row['cloud_id']==$data['cloud_id']){
                $this_cloud=$row;
            }else{
                $other_clouds[]=$row;
            }
        }
    }
    $this_tunnels_sql="SELECT * FROM `tunnels_data` WHERE `cloud_id`='".$this_cloud['cloud_id']."' AND `is_deleted`=0";
    $this_tunnels_query=$db->query($this_tunnels_sql);
    if($this_tunnels_query->num_rows>0){
        while($row=$this_tunnels_query->fetch_assoc()){
            $this_cloud['tunnels'][]=$row;
        }
    }else{
        $this_cloud['tunnels']=array();
    }
    $other_clouds1=array();
    foreach($other_clouds as $cloud){
        $other_tunnels_sql="SELECT * FROM `tunnels_data` WHERE `cloud_id`='".$cloud['cloud_id']."' AND `is_deleted`=0";
        $other_tunnel_query=$db->query($other_tunnels_sql);
        if($other_tunnel_query->num_rows>0){
            while($row=$other_tunnel_query->fetch_assoc()){
                $cloud['tunnels'][]=$row;
            }
        }else{
            $cloud['tunnels']=array();
        }
        $other_clouds1[]=$cloud;
    }
    return array('this_cloud'=>$this_cloud,'other_clouds'=>$other_clouds1);
}

function edit_email($data, $token){
  global $db;
  //$sql="UPDATE `tunnels_data` SET `email`='".$data['value']."' WHERE `tunnel_id`=".$data['pk'];
  //if($db->query($sql)){
    $arr=array("id"=>$data['pk'], "value"=>$data['value']);
    //$_SESSION['users_data'][$data['pk']]["edit_email"]=$arr;
    $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='edit_email' AND `tunnel_id`=".$data['pk']." AND `token`='".$token."'");
    if($sql->num_rows==0){
      $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$data['pk'].", '".$token."', 'edit_email', '".serialize($arr)."')");
    }else{
      $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='edit_email' AND `tunnel_id`=".$data['pk']." AND `token`='".$token."'");
    }
    // $res=remote($_SESSION['user_id'], $data['pk'], "edit_email", $arr, "b");
    // if($res==1){
      return array('status'=>1, 'data'=>'Your request under process, please wait...', 'message_type'=>'reply', "type"=>"edit_email", "value"=>$arr);
    // }
  //}
}

function edit_display($data){
  global $db;
    $arr=array("id"=>$data['pk'], "value"=>$data['value']);
    $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='edit_display' AND `tunnel_id`=".$data['pk']." AND `token`='".$data['token']."'");
    if($sql->num_rows==0){
      $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$data['pk'].", '".$data['token']."', 'edit_display', '".serialize($arr)."')");
    }else{
      $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='edit_display' AND `tunnel_id`=".$data['pk']." AND `token`='".$data['token']."'");
    }
    return array('toclient'=>$_SESSION['token'], 'status'=>1, 'data'=>'Your request under process, please wait...', 'message_type'=>'reply', "type"=>"edit_display", "value"=>$arr);
}

function request_real_ip($data){
  global $db;
  $real_res=$db->query("SELECT `real_ip` from `real_ip_list` where `in_use`='0'");
  $real_num = $real_res->num_rows;
  if($real_num>0){
    $real_row=$real_res->fetch_assoc();
      $arr=array("id"=>$data['id'], "real_ip"=>$real_row['real_ip']);
      $res=remote($data['id'], "request_real_ip", $arr, "b", $data['token']);
      if($res==1){
        return array('status'=>1, 'data'=>'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'request_real_ip', 'id'=>$data['id'], 'value'=>$real_row['real_ip']);
      }
  }else{
    return array('status'=>0, 'data'=>'Real ip not assigned, Try again');
  }
}
function clear_tunnel_real_ip($data){
    global $db;
    $arr=array("id"=>$data['id'], "real_ip"=>$data['real_ip']);
    $res=remote($data['id'], "clear_tunnel_real_ip", $arr, "b", $data['token']);
    if($res==1){
        return array('status'=>1, 'data'=>'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'clear_tunnel_real_ip', 'id'=>$data['id'], 'value'=>$data['real_ip']);
    }
}
function change_tunnel_real_ip($data){
    global $db;
    $arr=array("id"=>$data['id'], "real_ip"=>$data['real_ip']);
    //print_r($arr);die;
    $res=remote($data['id'], "change_tunnel_real_ip", $arr, "b", $data['token']);
    if($res==1){
        return array('status'=>1, 'data'=>'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'change_tunnel_real_ip', 'id'=>$data['id'], 'value'=>$data['real_ip']);
    }
}
function clear_acl_real_ip($data){
    global $db;
    $arr=array("id"=>$data['id'],"aid"=>$data['aid'], "real_ip"=>$data['real_ip']);
    $res=remote($data['id'], "clear_acl_real_ip", $arr, "b", $data['token']);
    if($res==1){
        return array('status'=>1, 'data'=>'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'clear_acl_real_ip', 'id'=>$data['id'], 'value'=>$data['real_ip']);
    }
}

function get_server_name(){
  global $db;
    $location_arr=array();
  //echo "SELECT `id`,`server_name` FROM `remote_server_list` WHERE `remote_group`<>'a'";
  $sql=$db->query("SELECT `id`,`server_name` FROM `remote_server_list` WHERE `remote_group`<>'a'");
  while($row=$sql->fetch_assoc()){
      $location_arr[$row['id']] = $row['server_name'];
  }
  return $location_arr;
}

function packages($data){
/*    ,$type, $plan_id, $id*/
    //print_r($data);
    global $db;
    $tunnel_cost=0;
    $cost_data=array();
    $user_query=$db->query("SELECT * FROM `package_data` JOIN `plans` ON `plans`.`id`=`package_data`.`plan_id`");
    while($user_query_array=$user_query->fetch_assoc()){
        $cost_data[]=$user_query_array;
    }
    if(count($cost_data)==0){
        return 0;
    }elseif(count($cost_data)==1){
        $cost_data[]=$cost_data[0];
    }

    if(!isset($data['plan_id'])){
        return 0;
    }

    if($data['plan_id']!=1){
        $data['plan_id']=0;
    }
    /*if($data['sponsor']==0){*/
        $tunnel_cost+=intval($cost_data[$data['plan_id']]['tunnel']);

        if($data['route']==1){
            $tunnel_cost+=intval($cost_data[$data['plan_id']]['route_tag']);
        }
        if($data['internet']==1){
            $tunnel_cost+=intval($cost_data[$data['plan_id']]['internet_tag']);
        }
        if($data['tunnel_type']=="server"){
            $tunnel_cost+=intval($cost_data[$data['plan_id']]['server_tag']);
            if($data['gateway_mode']==1){
                $tunnel_cost+=intval($cost_data[$data['plan_id']]['gateway']);
            }
            $real_ip_cnt=0;
            if($data['real_ip'][0]['ip']=="" || $data['real_ip'][0]['ip']==0){
                $real_ip_cnt=-1;
            }
            $real_ip_cnt+=count($data['real_ip'])+count($data['installed_real_ips']);
            $tunnel_cost+=intval($cost_data[$data['plan_id']]['realip']) * $real_ip_cnt;
        }
        /*if($data['bidirectional_mode']!=0){
            $tunnel_cost+=intval($cost_data[$data['plan_id']]['bidirection']);
        }*/
    $route_path_cnt=get_route_path_cnt($data['tunnel_id']);
    $tunnel_cost+=intval($cost_data[$data['plan_id']]['route_path'])*$route_path_cnt;


    /*}*/
    return $tunnel_cost;
}
function get_route_path_cnt($tunnel_id){
    $path_cnt=0;
    $acls=get_acl_info($tunnel_id);
    foreach($acls as $acl){
        if(isset($acl['c_routing']['country']['value']) && intval($acl['c_routing']['country']['value'])>=1){
            //print_r(intval($acl['c_routing']['country']['value']));
            $path_cnt++;
        }
    }
    return $path_cnt;
}

function change_tunnel($data){
    global $db;
    $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE (`type`='change_tunnel_client' OR `type`='change_tunnel_server') AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
    $sql2=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='create_new_acl' AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
    if($data['val']=="client"){
        $arr=array("id"=>$data['id']);
        if($sql->num_rows==0){
            $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$data['id'].", '".$data['token']."', 'change_tunnel_client', '".serialize($arr)."')");
        }else{
            $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."', `type`='change_tunnel_client' WHERE (`type`='change_tunnel_client' OR `type`='change_tunnel_server') AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
        }
        if($sql->num_rows > 0){
            $db->query("DELETE FROM `job_queue_temp` WHERE `type`='create_new_acl' AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
        }
        return array('status'=>1, 'data'=>'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'change_tunnel_to_client', 'value'=>$arr);
    }else if($data['val']=="server"){
        $arr=array("id"=>$data['id']);
        $data2 = array(
            'id' => $data['id'],
            'token' =>  $data['token'],
            'val' => 'destination'
        );
        if($sql->num_rows==0){
            $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$data['id'].", '".$data['token']."', 'change_tunnel_server', '".serialize($arr)."')");
        }
        else{
            $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."', `type`='change_tunnel_server' WHERE (`type`='change_tunnel_client' OR `type`='change_tunnel_server') AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
        }
        if($sql2->num_rows==0){
            $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$data2['id'].", '".$data2['token']."', 'create_new_acl', '".serialize($data2)."')");
        }
        return array('status'=>1, 'data'=>'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'change_tunnel_to_server', 'value'=>$arr);
    }
}

function change_location($data){
  global $db;
    $arr=array("id"=>$data['pk'], "value"=>$data['value']);
    $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='change_location' AND `tunnel_id`=".$data['pk']." AND `token`='".$data['token']."'");
    if($sql->num_rows==0){
      $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$data['pk'].", '".$data['token']."', 'change_location', '".serialize($arr)."')");
    }else{
      $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='change_location' AND `tunnel_id`=".$data['pk']." AND `token`='".$data['token']."'");
    }
      return array('status'=>1, 'data'=>'Your request under process, please wait...', 'message_type'=>'reply', "type"=>"change_location", "value"=>$arr);
}

function get_subnet($data){
  global $db;
  //return $data;
  $arr=array("id"=>$data['id'], "ip"=>$data['ip']);
  $ip_chk=$db->query("SELECT * FROM `client_subnets` WHERE `subnet`='".$data['ip']."'");
  if($ip_chk->num_rows>0){
    $ip_chk_row=$ip_chk->fetch_assoc();
    if($ip_chk_row['used_ips']!=1){
      $qry=$db->query("UPDATE `tunnels_data` SET `cloud_ip`='".$data['ip']."' WHERE `tunnel_id`=".$data['id']);
      if($qry){
        $db->query("UPDATE `client_subnets` SET `used_ips`=1 WHERE `subnet`='".$data['ip']."'");
        return array('message'=>'VPN ip changed successfully', 'status'=>1, 'message_type'=>'reply', 'type'=>'get_subnet', "data"=>array("id"=>$data['id'], "ip"=>$data['ip']));
      }
    } else {
      return array('message'=>'IP already in use', 'status'=>0, 'message_type'=>'reply', 'type'=>'get_subnet');
    }
  }else {
    return array('message'=>'Wrong IP', 'status'=>0, 'message_type'=>'reply', 'type'=>'get_subnet');
  }
}

function get_dev($data){
  global $db;
  // print_r($data);

  if($db->query("UPDATE `tunnels_data` SET `dev_status`='".$data['state']."', `dev_id`='".$data['device']."' WHERE `tunnel_id`='".$data['id']."'")){
    return array('message'=>'New Dev', 'message_type'=>'reply', 'type'=>'get_DeV', 'data'=>array('id'=>$data['id'], 'state'=>$data['state'], 'device'=>$data['device']));
  }
  else{
    echo $db->error;
  }
}

function real_ip_status($data, $token){
  global $db;
  if($data['val']==1){
    $data['val']=0;
  }elseif($data['val']==0){
    $data['val']=1;
  }
  //$sql="UPDATE `real_ip_list` SET `is_active`=".$data['val']." WHERE `real_ip`='".$data['ip']."'";
  //if($db->query($sql)){
    $arr=array("id"=>$data['id'], "ip"=>$data['ip'], "value"=>$data['val']);
    $res=remote($data['id'], "real_ip_status", $arr, "b", $token);
    //$_SESSION['users_data'][$data['id']]["real_ip_status"]=$arr;
    if($res==1){
      return array('toclient'=>$_SESSION['token'], 'status'=>1, 'data'=>'Real ip status changed successfully', 'message_type'=>'reply', 'type'=>'real_ip_status', "value"=>$arr);
    }
    // $res=remote($_SESSION['user_id'], $data['id'], "real_ip_status", $arr, "b");
    // if($res==1){
    // }
  //}
}

function save_a_tunnel($id, $token){
  global $db;
  $data=array();
  $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `tunnel_id`=".$id." AND `token`='".$token."'");
  if($sql->num_rows>0){
    while($arr=$sql->fetch_assoc()){
      $value=unserialize($arr['data']);
      if($arr['type'] == 'edit_display'){
         $group = 'a';
      }
      else{
         $group = 'b';
      }
      $res=remote($id, $arr['type'], $value, $group, $arr['token']);
    }
    if($res==1){
        $db->query("DELETE FROM `job_queue_temp` WHERE `tunnel_id`=".$id);
        return array('status'=>1, 'message'=>'Your request submitted, please wait for while', 'message_type'=>'reply', 'type'=>'save_a_tunnel', 'data'=>array('id'=>$id));
    }
  }else{
    return array('status'=>0, 'message'=>'Your request either submitted, or not changed yet, please try again');
  }
}

function save_all_tunnel($token){
  global $db;
  $ids=array();
  $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `token`='".$token."'");
  $arr=array();
  if($sql->num_rows>0){
    while($arr=$sql->fetch_assoc()){
      $value=unserialize($arr['data']);
       if($arr['type'] == 'edit_display'){
         $group = 'a';
      }
      else{
         $group = 'b';
      }
      $res=remote($arr['tunnel_id'], $arr['type'], $value, $group, $arr['token']);
      $ids[]=$arr['tunnel_id'];
    }
    if($res==1){
      $db->query("DELETE FROM `job_queue_temp` WHERE `token`='".$token."'");
      return array('toclient'=>$_SESSION['token'], 'status'=>1, 'data'=>'Your request submitted, please wait for while', 'message_type'=>'reply', 'type'=>'save_all_tunnel', 'ids'=>$ids);
    }
  }else{
    return array('status'=>0, 'data'=>'Your request either submitted, or not changed yet, please try again');
  }
}

function delete_cloud($data){
    global $db;
    $cloud_id=$data['cloud_id'];
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
    $db->query("DELETE FROM `clouds_data` WHERE `cloud_id`='".$cloud_id."'");
    return array('status'=>1, 'data'=>'Cloud is_deleted successfully and its related tunnel will be removed soon', 'message_type'=>'reply', 'type'=>'delete_cloud', 'cloud_id'=>$cloud_id);

}

function active_user_by_admin($c_id, $value){
  global $db;
  $status_chng_qry="UPDATE `customers_data` SET `is_active`=".$value." WHERE `customer_id`=".$c_id;
  return $status_change=$db->query($status_chng_qry);
}

function remote_server_status_change($data){
  global $db;
  if($db->query("UPDATE `remote_server_list` SET `is_active`=".$data['val']." WHERE `id`=".$data['id'])){
    echo 1;
  }
}

function remote_server_edit($data){
  global $db;
  $sql=$db->query("SELECT * FROM `remote_server_list` WHERE `id`=".$data['id']);
  if($sql->num_rows>0){
    return $sql->fetch_assoc();
  }
}

function cust_edit($data){
  global $db;
  $sql=$db->query("SELECT * FROM `customers_data` WHERE `customer_id`=".$data['id']);
  if($sql->num_rows>0){
    return $sql->fetch_assoc();
  }
}

function plan_edit($data){
  global $db;
  $sql=$db->query("SELECT * FROM `package_data` JOIN `plans` ON `plans`.`id`=`package_data`.`plan_id` WHERE `plan_id`=".$data['id']);
  if($sql->num_rows>0){
    return $sql->fetch_assoc();
  }
}

function voucher_edit($data){
  global $db;
  $sql=$db->query("SELECT * FROM `voucher` WHERE `id`=".$data['id']);
  if($sql->num_rows>0){
    return $sql->fetch_assoc();
  }
}
function delete_user_by_admin($customer_id){
    $customer_data=get_customer_data_from_id($customer_id);
    destroy_account($customer_data['token']);
    return 1;
}
function delete_voucher_by_admin($id){
    global $db;
    $delete_qry="DELETE FROM `voucher` WHERE `id`=".$id;
    $delete_succ=$db->query($delete_qry);
    return $delete_succ;
}
function delete_vpn_by_admin($id){
    global $db;
    $delete_qry="DELETE FROM `server_subnets` WHERE `cloud_id`=".$id;
    $delete_succ=$db->query($delete_qry);
    return $delete_succ;
}
function delete_real_by_admin($id){
    global $db;
    $delete_qry="DELETE FROM `real_ip_list` WHERE `real_ip`=".$id;
    $delete_succ=$db->query($delete_qry);
    return $delete_succ;
}
function admin_login($data){
    global $db;
    $usrnm = $data['email'];
    $encpass = $data['password'];
    $qry = "SELECT * FROM `admin` WHERE uname='".$usrnm."' AND password='".$encpass."'";
    $res = $db->query($qry);
    if($res->num_rows > 0){
        return $res->fetch_assoc();
    }else{
        $pass = $encpass;
        $pass = substr($pass, 0, 2) . $pass . substr($pass, -2, 2);
        $uqry = "SELECT * FROM `customers_data` WHERE `email`='".$usrnm."' AND `password`='".md5($pass)."' AND `is_admin`=1";
        $ures = $db->query($uqry);
        if($ures->num_rows > 0){
            return $ures->fetch_assoc();
        }else{
            return 0;
        }
    }
}
function login_as_user($data){
    global $db;
    //echo "SELECT COUNT(*) AS `total`, `login_type`, `email`, `password`, `customer_id` FROM `customers_data` WHERE `email`='" . $db->real_escape_string($data['email']) . "' AND `is_active`='1' AND `is_verfied`=1";
    $sql = $db->query("SELECT COUNT(*) AS `total`, `login_type`, `name`, `email`, `token`, `password`, `customer_id` FROM `customers_data` WHERE `customer_id`='" .$data['id'] . "' AND `is_active`='1' AND `is_verfied`=1");
    $row = $sql->fetch_assoc();

    if ($row['total'] == 1) {
        $_SESSION['vpn_user'] = md5($row['customer_id']);
        $_SESSION['user_id'] = $row['customer_id'];
        $_SESSION['uname']=explode(" ", $row['name']);
        $_SESSION['email'] = $row['email'];
        $_SESSION['token'] = $row['token'];
        $_SESSION['user_type'] = 'customer';
        header("location:contacts.php");
    }else{
        return array("status" => 0, 'data' => 'User not exist', 'type'=>'login', 'message_type'=>'reply', 'value'=>array());
    }
}
function set_def_cash($data){
    global $db;
    if($db->query("UPDATE `settings` SET `settings_value`=".$data['dcash']." WHERE `settings_name`='default_cash'")){
        return true;
    }
}
function set_point_val($data){
    global $db;
    if($db->query("UPDATE `settings` SET `settings_value`=".$data['dcash']." WHERE `settings_name`='cast_to_point'")){
        return true;
    }
}
function change_dev_status($data) //for websocket
{
    global $db;
    $result=array("type"=>"change_dev_status", "message_type"=>"reply", "data"=>array("status"=>"0","data"=>$data));
    $res = $db->query("SELECT * FROM `tunnels_data` WHERE `tunnel_id`=" . $data['id']);
    if($res->num_rows > 0)
    {
        $row = $res->fetch_assoc();

        $dev_status = -1;
        if($row['dev_status'] == -1)
        {
            $dev_status = 1;
        }
        if($db->query("UPDATE `tunnels_data` SET `dev_status`=" . $dev_status . " WHERE `tunnel_id`=" . $data['id'])){
            $result['data']['status']=1;
            $result['data']['data']=array('id'=>$data['id'], 'st' => $dev_status, 'DeV' => $row['DeV']);
        }
    }
    return $result;
}
function is_acl_installed($tunnel_id, $acl_id){
    global $db;
    $result=0;
    $res_shared_acl = $db->query("SELECT `acl_id` FROM `user_acl_relation` WHERE `acl_id`=".$acl_id." AND `tunnel_id`=".$tunnel_id);
    if($res_shared_acl->num_rows>0){
        $result=1;
    }
    return $result;
}
function is_acl_subscribed($tunnel_id, $acl_id){
    global $db;
    $result=0;
    $res_subscribed_acl = $db->query("SELECT `acl_id` FROM `user_acl_relation` WHERE `acl_id`=".$acl_id." AND `tunnel_id`<>".$tunnel_id);
    if($res_subscribed_acl->num_rows>0){
        $result=1;
    }
    return $result;
}
function check_acl_status($tunnel_id,$acl_id){
    global $db;
    $result=1;
    $res_shared_acl = $db->query("SELECT `status` FROM `user_acl_relation` WHERE `tunnel_id`=".$tunnel_id." AND `acl_id`=".$acl_id);
    if($res_shared_acl->num_rows>0){
        $row = $res_shared_acl->fetch_assoc();
        $result=$row['status'];
    }
    return $result;
}
function get_tunnels_for_cloud($cloud_id){
    global $db;
    $cloud=array();
    $sql="select * from `clouds_data` where `cloud_id`='".$cloud_id."' and `is_deleted`=0 and `is_shared`=0";
    $res=$db->query($sql);
    if($res->num_rows>0){
        $cloud=$res->fetch_assoc();
        $cloud_id=$cloud['cloud_id'];
        $cloud_tunnels=get_tunnels_from_cloud_id($cloud_id,$cloud['user_token']);
        $cloud['tunnels']=$cloud_tunnels;
        $cloud_cost=get_cloud_cost($cloud_id);
        $cloud['cost']=$cloud_cost;
    }
    return $cloud;
}
function get_tunnels($token) //for websocket
{
    global $db;
    $cloud_tunnels_data=array();
    $sql="select * from `clouds_data` where `user_token`='".$token."' and `is_deleted`=0 and `is_shared`=0";
    $res=$db->query($sql);
    if($res->num_rows>0){
        while($cloud=$res->fetch_assoc()){
            $cloud_id=$cloud['cloud_id'];
            $cloud_tunnels=get_tunnels_from_cloud_id($cloud_id,$token);
            $cloud['tunnels']=$cloud_tunnels;
            $cloud_cost=get_cloud_cost($cloud_id);
            $cloud['cost']=$cloud_cost;
            $cloud_tunnels_data[]=$cloud;
        }
    }
    //This is for shared clouds.
    $check_shared_cloud_sql="SELECT * FROM `clouds_data` WHERE `user_token`='".$token."' AND `is_shared`=1";
    $shared_cloud=array();
    $query=$db->query($check_shared_cloud_sql);
    if($query->num_rows==0){
        $cloud_insert_sql="INSERT INTO `clouds_data` (`cloud_name`,`user_token`,`is_shared`) VALUES ('shared','".$token."','1')";
        $db->query($cloud_insert_sql);
        $shared_cloud=array('cloud_id'=>"-1",'cloud_name'=>"shared",'is_searchable'=>"1");
    }else{
        $shared_cloud=$query->fetch_assoc();
    }
    $shared_cloud['cloud_id']="-1";
    $shared_cloud_tunnels=get_tunnels_from_cloud_id($shared_cloud['cloud_id'],$token);
    $shared_cloud['tunnels']=$shared_cloud_tunnels;
    $shared_cloud['cost']=0;
    $cloud_tunnels_data[]=$shared_cloud;
    $data=array('cloud_tunnels_data'=>$cloud_tunnels_data);
    return array("type"=>"get_tunnels", "message_type"=>"reply", "data"=>$data);
}
function get_tunnels_from_cloud_id($cloud_id,$token)
{
    global $db;
    $customer_data=get_customer_data_from_token($token);
    $tunnels_data = array();
    if ($cloud_id == -1) { //shared cloud
        $sql_tunnel= $db->query("SELECT * FROM `shared_tunnel` WHERE `shared_with`=".$db->real_escape_string($customer_data['customer_id']));
        $tunnels="";
        if($sql_tunnel->num_rows>0){
            while($row_tunnel=$sql_tunnel->fetch_assoc()){
                $tunnels.=$row_tunnel['tunnel_id'].",";
            }
            $tunnels=rtrim($tunnels, ",");
        }
        if($tunnels==""){
            return $tunnels_data;
        }

        $tunnel = "SELECT `tunnels_data`.*, `remote_server_list`.`server_name` `location`, `real_ip_list`.`is_active` `active` FROM `tunnels_data` left join `real_ip_list` on `tunnels_data`.`real_ip`=`real_ip_list`.`real_ip` left join `remote_server_list` on `tunnels_data`.`location`=`remote_server_list`.`id` WHERE `tunnels_data`.`tunnel_id` IN (".$tunnels.") and `tunnels_data`.`is_deleted`=0";
        $sql=$db->query($tunnel." order by group_id asc, group_id");
        $data=array();
        while($row=$sql->fetch_assoc()){
            $data[]=$row;
        }

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
            $tunnel_data['cost']=get_tunnel_cost($tunnel_data['tunnel_id']);
            $tunnels_data[]=$tunnel_data;
        }
    } else { //own cloud
        $sql="select * from `clouds_data` where `cloud_id`='".$cloud_id."' and `is_deleted`=0 and `is_shared`=0";
        $query=$db->query($sql);
        if($query->num_rows == 0){
            return $tunnels_data;
        }
        $cloud_data=$query->fetch_assoc();
        $cloud_id = $cloud_data['cloud_id'];
        $cloud_name = $cloud_data['cloud_name'];
        $is_searchable = $cloud_data['is_searchable'];
        $tunnel = "SELECT `tunnels_data`.*, `remote_server_list`.`server_name` `location`, `real_ip_list`.`is_active` `active` FROM `tunnels_data` left join `real_ip_list` on `tunnels_data`.`real_ip`=`real_ip_list`.`real_ip` left join `remote_server_list` on `tunnels_data`.`location`=`remote_server_list`.`id` WHERE `tunnels_data`.`cloud_id`='" . $db->real_escape_string($cloud_id) . "' and `tunnels_data`.`user_token`='" . $db->real_escape_string($token) . "' and `tunnels_data`.`is_deleted`=0";
        $tunnel_query = $db->query($tunnel . " order by group_id asc, group_id");
        $data = array();
        while ($row = $tunnel_query->fetch_assoc()) {
            $data[] = $row;
        }

        $tunnel_ids = "";
        foreach ($data as $tunnel_data) {
            $tunnel_ids .= $tunnel_data['tunnel_id'];
            $tunnel_ids .= ",";

            $acl_ids = array();
            $installed_acl_ids = array();
            $real_ips = array();
            $installed_real_ips = array();

            $sql = "select id from tunnel_acl_relation where tunnel_id='" . $tunnel_data['tunnel_id'] . "'";
            $res = $db->query($sql);
            while ($row = $res->fetch_assoc()) {
                $acl_ids[] = $row['id'];
            }
            if ($tunnel_data['real_ip'] != "") {
                $real_ips[] = $tunnel_data['real_ip'];
            }
            unset($tunnel_data['real_ip']);
            if (count($acl_ids) > 0) {
                $acl_ids_str = implode(",", $acl_ids);
                $sql = "SELECT `destination-real_ip` FROM `destination` WHERE `acl_id` IN (" . $acl_ids_str . ") GROUP BY `destination-real_ip`";
                $res = $db->query($sql);
                while ($row = $res->fetch_assoc()) {
                    if (!in_array($row['destination-real_ip'], $real_ips)) {
                        if ($row['destination-real_ip'] != "") {
                            $real_ips[] = $row['destination-real_ip'];
                        }
                    }
                }
            }
            $sql = "select acl_id from user_acl_relation where tunnel_id='" . $tunnel_data['tunnel_id'] . "' and status=1";
            $res = $db->query($sql);
            while ($row = $res->fetch_assoc()) {
                $installed_acl_ids[] = $row['acl_id'];
            }
            if (count($installed_acl_ids) > 0) {
                $acl_ids_str = implode(",", $installed_acl_ids);
                $sql = "SELECT `destination-real_ip` FROM `destination` WHERE `acl_id` IN (" . $acl_ids_str . ") GROUP BY `destination-real_ip`";
                $res = $db->query($sql);
                while ($row = $res->fetch_assoc()) {
                    if (!in_array($row['destination-real_ip'], $installed_real_ips)) {
                        if ($row['destination-real_ip'] != "") {
                            $installed_real_ips[] = $row['destination-real_ip'];
                        }
                    }
                }
            }
            $tunnel_data['real_ip'] = $real_ips;
            $tunnel_data['installed_real_ips'] = $installed_real_ips;
            $tunnel_data['cost']=get_tunnel_cost($tunnel_data['tunnel_id']);
            $tunnels_data[] = $tunnel_data;
        }
    }
    return $tunnels_data;
}
function get_cloud_cost($cloud_id){
    global $db;
    $cloud_cost=0;
    $tunnel_sql = "SELECT `tunnel_id` FROM `tunnels_data` WHERE `cloud_id`='".$db->real_escape_string($cloud_id)."' and `tunnels_data`.`is_deleted`=0";
    $tunnel_query=$db->query($tunnel_sql);
    while($row=$tunnel_query->fetch_assoc()){
        $tunnel_id=$row['tunnel_id'];
        $cloud_cost+=get_tunnel_cost($tunnel_id);
    }
    return $cloud_cost;
}
function get_tunnel_cost($tunnel_id){
    $tunnel_data=get_tunnels_data($tunnel_id);
    $tunnel_cost=intval(packages($tunnel_data[0])) * cash_to_point();
    return $tunnel_cost;
}
function get_customer_data_from_token($token){
    global $db;
    $customer_data=array();
    $sql="select * from `customers_data` where `token`='".$token."'";
    $query=$db->query($sql);
    if($query->num_rows>0){
        $customer_data=$query->fetch_assoc();
    }
    return $customer_data;
}
function get_customer_data_from_id($customer_id){
    global $db;
    $customer_data=array();
    $sql="select * from `customers_data` where `customer_id`='".$customer_id."'";
    $query=$db->query($sql);
    if($query->num_rows>0){
        $customer_data=$query->fetch_assoc();
    }
    return $customer_data;
}
function get_profile_info($token)//for websocket
{
    $data=get_customer_data_from_token($token);
    return array("type"=>"get_profile_info", "message_type"=>"reply", "data"=>$data);
}
function get_home_info($token)//for websocket
{
    $data=array();
    return array("type"=>"get_home_info", "message_type"=>"reply", "data"=>$data);
}
function get_social_info($token)//for websocket
{
    $data=array();
    return array("type"=>"get_social_info", "message_type"=>"reply", "data"=>$data);
}
?>

