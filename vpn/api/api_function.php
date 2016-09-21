<?php
$root_url=$_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']."/vpn_site/vpn";
$root_url=$_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']."/vpn";
define("ROOT_URL",$root_url );


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
   foreach ($tunnel as  $data) {

    $html.='<div class="p_div">';
        $html.='<div id="p_div_'.$data['tunnel_id'].'">';
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
         $html.='<div class="meta width-80 tunnel_location_'.$data['tunnel_id'].'" data-toggle="tooltip" title=""><a href="javascript:void(0);" class="change_location location_'.$data['tunnel_id'].' tunnel_editable" data-type="select" data-source="request.php?request=get_server_name" data-pk="'.$data['tunnel_id'].'">'.($data['location']!=null?$data['location']:"Select Location").'</a></div>';
         //new
         $dev_class = 'dev-disconnect';
        $dev_message = 'Disconnected';
           if($data['dev_status'] == 1){
               $dev_class = 'dev-connected';
               $dev_message = $data['DeV'];
           }
           elseif($data['dev_status'] == 0){
               $dev_class = 'dev-connecting';
               $dev_message = 'Initiating';
           }
           elseif($data['dev_status'] == -1){
               $dev_class = 'dev-disconnected';
               $dev_message = 'Disconnected';
           }
         $html.='<div class="meta width-270 text-align-center" style="width:78px;"><div class="dev_status  '.$dev_class.'"  data-tid="'.$data['tunnel_id'].'" id="DeV_'.$data['tunnel_id'].'" data-toggle="tooltip" data-placement="bottom" title="" style="width:100%;">'.$dev_message.'</div></div>';

       if($data['tunnel_type']=="client"){
           $html.='<div class="meta width-80 subnet_'.$data['tunnel_id'].'" data-toggle="tooltip" title="">Auto</div>';
       }else{
           $html.='<div class="meta width-80 subnet_'.$data['tunnel_id'].'" data-toggle="tooltip" title="">'.$data['cloud_ip'].'</div>';
       }

         $tunnel_cost = packages($data['tunnel_type'], $data['plan_id'], $data['tunnel_id']);
         $html.='<div class="meta plan_cost_'.$data['tunnel_id'].'" data-toggle="tooltip" title="Tunnel points '.$tunnel_cost*cash_to_point().'">'.$tunnel_cost*cash_to_point().'</div>';

         $html.='<span class="not_client_'.$data['tunnel_id'].'">';
         if($data['tunnel_type']!="client"){
             if( (count($data['real_ip'])>0) || (count($data['installed_real_ips'])>0)){
                 $html.='<div class="real_ip_meta width-140" data-toggle="tooltip">';
                 $html.='<select class="select_real_ip real_ip_'.$data['tunnel_id'].'" style="width:120px!important; height:22px!important;min-height:0px;" data-val="'.($data['active']!=null?$data['active']:-1).'" data-id="'.$data['tunnel_id'].'">'.(count($data['real_ip'])!=0?count($data['real_ip']):"Not assigned");
                 foreach($data['real_ip'] as $real_ip){
                     $html.='<option value="'.$real_ip.'">'.$real_ip.'</option>';
                 }
                 foreach($data['installed_real_ips'] as $real_ip){
                     $html.='<option value="'.$real_ip.'" disabled>'.$real_ip.'</option>';
                 }
                 $html.='</select>';
             }else{
                 $html.='<div class="meta width-140" data-toggle="tooltip">';
                 $html.='<a href="javascript:void(0);" class="real_ip real_ip_'.$data['tunnel_id'].'" style="color:#1B1E24" data-val="-1" data-id="'.$data['tunnel_id'].'">Not assigned</a>';
             }
            $html.='</div>';
            $html.='<div class="meta cursor">'.gateway($data['gateway_mode'], $data['tunnel_id'], $data['tunnel_type']).'</div>';
         } else {
            $html.='<div class="meta width-140" data-toggle="tooltip" data-placement="right" title="'.($data['tunnel_type']!="client"?"":"To activate this field upgrade to server").'"><a href="javascript:void(0)" class="change_tunnel change_tunnel_'.$data['tunnel_id'].'" data-type="'.$data['tunnel_type'].'" data-id="'.$data['tunnel_id'].'">'.($data['tunnel_type']!="client"?"<i class='fa fa-long-arrow-down'></i>":"<i class='fa fa-long-arrow-up'></i>").'</a></div>';

            $html.='<div class="meta" data-toggle="tooltip" data-placement="right" title="'.($data['tunnel_type']!="client"?"":"To activate this field upgrade to server").'"><a href="javascript:void(0)" class="change_tunnel change_tunnel_'.$data['tunnel_id'].'" data-type="'.$data['tunnel_type'].'" data-id="'.$data['tunnel_id'].'">'.($data['tunnel_type']!="client"?"<i class='fa fa-long-arrow-down'></i>":"<i class='fa fa-long-arrow-up'></i>").'</a></div>';
         }
         $html.='<div class="meta cursor float-right">'.status($data['status'], $data['tunnel_id']).'</div>';
         $html.='</span><div class="meta float-right" data-toggle="tooltip" title="Delete this tunnel" ><a href="javascript:void(0);" data-id="'.$data['tunnel_id'].'" class="delete_tunnel delete_tunnel_'.$data['tunnel_id'].'" data-type="'.$data['tunnel_type'].'"><i class="fa fa-fw fa-trash" style="color:#DA3838"></i></a></div>';

      $html.='</div>';
       $html.='<div class="tunnel_searchable_switch_block">';
       $html.='<input id="cmn-toggle-tunnel-'.$data['tunnel_id'].'" class="cmn-toggle cmn-toggle-round tunnel_searchable_switch" data-tunnel_id="'.$data['tunnel_id'].'" type="checkbox" '.($data["is_searchable"]==1?"checked":"").'>';
       $html.='<label for="cmn-toggle-tunnel-'.$data['tunnel_id'].'"></label>';
       $html.='</div>';
      $html.='</div>';

       //tunnel acl

       $html.='<div class="tunnel_acl_div_'.$data['tunnel_id'].' tunnel_acl_div" data-id="'.$data['tunnel_id'].'" style="display:none;">';
        $html.='<label style="border-bottom: 1px solid #000;direction: ltr;font-size: 20px;margin-left: 20px;">Source base<span class="source_no_data_p_'.$data['tunnel_id'].'" style="color: #ea4335; font-size: 15px;"></span>&nbsp;&nbsp;<input type="button" class="btn btn-xs btn-primary acl_destination_search_btn" value="Search ACL" data-tid="'.$data['tunnel_id'].'" style="margin-bottom: 3px;"/></label>';
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
    foreach ($tunnel as  $data) {

        $html.='<div class="p_div">';
        $html.='<div id="p_div_'.$data['tunnel_id'].'">';
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
        if($is_shared == false){
            $opacity = 'opacity:0.25; color: black; background-color: transparent;';
            $sponsore_class="sponsore";
        }
        $html.='<a data-val="" class="btn holbol '.$sponsore_class.' sponsored_'.$data['tunnel_id'].'" type="data" data-pos="0" data-tid="'.$data['tunnel_id'].'"  data-cloud="'.$data['cloud_id'].'" data-u="'.$_SESSION['user_id'].'" style="background-color:#1D9E74;'.$opacity.'">Sponsored</a>';

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
        $html.='<div class="meta width-80 tunnel_location_'.$data['tunnel_id'].'" data-toggle="tooltip" title=""><a href="javascript:void(0);" class="change_location location_'.$data['tunnel_id'].' tunnel_editable" data-type="select" data-source="request.php?request=get_server_name" data-pk="'.$data['tunnel_id'].'">'.($data['location']!=null?$data['location']:"Select Location").'</a></div>';
        //new
        $dev_class = 'dev-disconnect';
        $dev_message = 'Disconnected';
        if($data['dev_status'] == 1){
            $dev_class = 'dev-connected';
            $dev_message = $data['DeV'];
        }
        elseif($data['dev_status'] == 0){
            $dev_class = 'dev-connecting';
            $dev_message = 'Initiating';
        }
        elseif($data['dev_status'] == -1){
            $dev_class = 'dev-disconnected';
            $dev_message = 'Disconnected';
        }
        $html.='<div class="meta width-270 text-align-center" style="width:78px;"><div class="dev_status  '.$dev_class.'"  data-tid="'.$data['tunnel_id'].'" id="DeV_'.$data['tunnel_id'].'" data-toggle="tooltip" data-placement="bottom" title="" style="width:100%;">'.$dev_message.'</div></div>';

        if($data['tunnel_type']=="client"){
            $html.='<div class="meta width-80 subnet_'.$data['tunnel_id'].'" data-toggle="tooltip" title="">Auto</div>';
        }else{
            $html.='<div class="meta width-80 subnet_'.$data['tunnel_id'].'" data-toggle="tooltip" title="">'.$data['cloud_ip'].'</div>';
        }

        $tunnel_cost = packages($data['tunnel_type'], $data['plan_id'], $data['tunnel_id']);
        $html.='<div class="meta plan_cost_'.$data['tunnel_id'].'" data-toggle="tooltip" title="Tunnel points '.$tunnel_cost*cash_to_point().'">'.$tunnel_cost*cash_to_point().'</div>';

        $html.='<span class="not_client_'.$data['tunnel_id'].'">';
        if($data['tunnel_type']!="client"){
            if( (count($data['real_ip'])>0) || (count($data['installed_real_ips'])>0)){
                $html.='<div class="real_ip_meta width-140" data-toggle="tooltip">';
                $html.='<select class="select_real_ip real_ip_'.$data['tunnel_id'].'" style="width:120px!important; height:22px!important;min-height:0px;" data-val="'.($data['active']!=null?$data['active']:-1).'" data-id="'.$data['tunnel_id'].'">'.(count($data['real_ip'])!=0?count($data['real_ip']):"Not assigned");
                foreach($data['real_ip'] as $real_ip){
                    $html.='<option value="'.$real_ip.'">'.$real_ip.'</option>';
                }
                foreach($data['installed_real_ips'] as $real_ip){
                    $html.='<option value="'.$real_ip.'" disabled>'.$real_ip.'</option>';
                }
                $html.='</select>';
            }else{
                $html.='<div class="meta width-140" data-toggle="tooltip">';
                $html.='<a href="javascript:void(0);" class="real_ip real_ip_'.$data['tunnel_id'].'" style="color:#1B1E24" data-val="-1" data-id="'.$data['tunnel_id'].'">Not assigned</a>';
            }
            $html.='</div>';
            $html.='<div class="meta cursor">'.gateway($data['gateway_mode'], $data['tunnel_id'], $data['tunnel_type']).'</div>';
        } else {
            $html.='<div class="meta width-140" data-toggle="tooltip" data-placement="right" title="'.($data['tunnel_type']!="client"?"":"To activate this field upgrade to server").'"><a href="javascript:void(0)" class="change_tunnel change_tunnel_'.$data['tunnel_id'].'" data-type="'.$data['tunnel_type'].'" data-id="'.$data['tunnel_id'].'">'.($data['tunnel_type']!="client"?"<i class='fa fa-long-arrow-down'></i>":"<i class='fa fa-long-arrow-up'></i>").'</a></div>';

            $html.='<div class="meta" data-toggle="tooltip" data-placement="right" title="'.($data['tunnel_type']!="client"?"":"To activate this field upgrade to server").'"><a href="javascript:void(0)" class="change_tunnel change_tunnel_'.$data['tunnel_id'].'" data-type="'.$data['tunnel_type'].'" data-id="'.$data['tunnel_id'].'">'.($data['tunnel_type']!="client"?"<i class='fa fa-long-arrow-down'></i>":"<i class='fa fa-long-arrow-up'></i>").'</a></div>';
        }
        $html.='<div class="meta cursor float-right">'.status($data['status'], $data['tunnel_id']).'</div>';
        $html.='</span>';

        $html.='</div>';
        $html.='<div class="tunnel_searchable_switch_block">';
        $html.='<input id="cmn-toggle-tunnel-'.$data['tunnel_id'].'" class="cmn-toggle cmn-toggle-round tunnel_searchable_switch" data-tunnel_id="'.$data['tunnel_id'].'" type="checkbox" '.($data["is_searchable"]==1?"checked":"").'>';
        $html.='<label for="cmn-toggle-tunnel-'.$data['tunnel_id'].'"></label>';
        $html.='</div>';
        $html.='</div>';

        //tunnel acl

        $html.='<div class="tunnel_acl_div_'.$data['tunnel_id'].' tunnel_acl_div" data-id="'.$data['tunnel_id'].'" style="display:none;">';
        $html.='<label style="border-bottom: 1px solid #000;direction: ltr;font-size: 20px;margin-left: 20px;">Source base<span class="source_no_data_p_'.$data['tunnel_id'].'" style="color: #ea4335; font-size: 15px;"></span>&nbsp;&nbsp;<input type="button" class="btn btn-xs btn-primary acl_destination_search_btn" value="Search ACL" data-tid="'.$data['tunnel_id'].'" style="margin-bottom: 3px;"/></label>';
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
   $sql = $db->query("INSERT INTO `clouds_data` (`cloud_name`, `user_token`, `email`) VALUES ('".$db->real_escape_string($_POST['cloud_name'])."', '".$db->real_escape_string($_SESSION['token'])."', '".$db->real_escape_string($_POST['cloud_email'])."')");
       $last_id=$db->insert_id;
       $data=array("id"=>$last_id, "clouds_data"=>$_POST['cloud_name'], "customer_id"=>$_SESSION['user_id'], "email"=>$_POST['cloud_email']);
       if($sql){
          return array("status" => 1, 'data' => 'Cloud Added successfully');
       }
       else{
           return array("status" => 0, 'data' => 'error occurred, try again.');
       }
}

//delete tunnel section
function delete_tunnel($data){
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
            return array("toclient"=>$_SESSION['token'], "status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'delete_tunnel', 'value'=>$data['id']);
          }
      //}
   }
}

function gateway_change($id, $val, $token){
    global $db;
    $arr=array("id"=>$id, "value"=>$val);
    $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='gateway_change' AND `tunnel_id`=".$id." AND `token`='".$token."'");
    if($sql->num_rows==0){
        $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$id.", '".$token."', 'gateway_change', '".serialize($arr)."')");
    }else{
        $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='gateway_change' AND `tunnel_id`=".$id." AND `token`='".$token."'");
    }
    return array("status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', "type"=>"gateway_change", "value"=>$arr);
}

function status_change($id, $val, $token){

    global $db;
    $arr=array("id"=>$id, "value"=>$val);
    $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='status_change' AND `tunnel_id`=".$id." AND `token`='".$token."'");
    if($sql->num_rows==0){
        $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$id.", '".$token."', 'status_change', '".serialize($arr)."')");
    }else{
        $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='status_change' AND `tunnel_id`=".$id." AND `token`='".$token."'");
    }
    return array("status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', "type"=>"status_change", "value"=>$arr);
}

function bidirection_change($id, $val, $token){
  global $db;
  //$_SESSION['users_data'][$id]["bidirection_change"]=array("id"=>$id, "value"=>$val);
  $arr=array("id"=>$id, "value"=>$val);
  $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='bidirection_change' AND `tunnel_id`=".$id." AND `token`='".$token."'");
  if($sql->num_rows==0){
    $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$id.", '".$token."', 'bidirection_change', '".serialize($arr)."')");
  }else{
    $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='bidirection_change' AND `tunnel_id`=".$id." AND `token`='".$token."'");
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

function internet_change($id, $val, $token){
  global $db;
  //$_SESSION['users_data'][$id]["internet_change"]=array("id"=>$id, "value"=>$val);
  $arr=array("id"=>$id, "value"=>$val);
  $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='internet_change' AND `tunnel_id`=".$id." AND `token`='".$token."'");
  if($sql->num_rows==0){
    $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$id.", '".$token."', 'internet_change', '".serialize($arr)."')");
  }else{
    $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='internet_change' AND `tunnel_id`=".$id." AND `token`='".$token."'");
  }
  //if($sql=$db->query("UPDATE `tunnels_data` SET `internet`=".$val." WHERE `tunnel_id`=".$id)){
      $arr=array("id"=>$id, "value"=>$val);
      // $res=remote($_SESSION['user_id'], $id, "internet_change", $arr, "b");
      // if($res==1){
        return array("status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', "type"=>"internet_change", "value"=>$arr);
      // }
  //}
}

function route_change($id, $val, $token){
  global $db;
  //$_SESSION['users_data'][$id]["route_change"]=array("id"=>$id, "value"=>$val);
  $arr=array("id"=>$id, "value"=>$val);
  $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='route_change' AND `tunnel_id`=".$id." AND `token`='".$token."'");
  if($sql->num_rows==0){
    $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$id.", '".$token."', 'route_change', '".serialize($arr)."')");
  }else{
    $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='route_change' AND `tunnel_id`=".$id." AND `token`='".$token."'");
  }
  // if($sql=$db->query("UPDATE `tunnels_data` SET `route`=".$val." WHERE `tunnel_id`=".$id)){
      $arr=array("id"=>$id, "value"=>$val);
      // $res=remote($_SESSION['user_id'], $id, "route_change", $arr, "b");
      // if($res==1){
        return array("status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', "type"=>"route_change", "value"=>$arr);
      // }
  // }
}

function plan_change($id, $val, $token){
  global $db;
  //$_SESSION['users_data'][$id]["plan_change"]=array("id"=>$id, "value"=>$val);
  $arr=array("id"=>$id, "value"=>$val);
  $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='plan_change' AND `tunnel_id`=".$id." AND `token`='".$token."'");
  if($sql->num_rows==0){
    $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$id.", '".$token."', 'plan_change', '".serialize($arr)."')");
  }else{
    $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='plan_change' AND `tunnel_id`=".$id." AND `token`='".$token."'");
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
function addTunnel($data, $token){
   global $db;
   $tunnel=array();
   $real_ip="";
   $subnet_ip="";
   $customer_id=0;
   $sql = $db->query("SELECT `customer_id` FROM `customers_data` WHERE `Cash_amount`>0 and `token` = '".$token."'");
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
            $tunnel[0]['token']=$token;
       }
       //return array("status" => 0, 'data' => 'Unexpected error occured.');
       $res=remote("", "add_new_tunnel", $tunnel, "a", $token);
       //return $res;
       if($res==1){
        /* if($val['type']=="server"){ //There is no $val! check tunnels add!!!
            $db->query("UPDATE `server_subnets` SET `used_ips`='1'  WHERE `subnet`='".$subnet_ip."'");
         }*/
       }
   }else{
       return array("status" => 2, 'data' => 'You have no balace to do any operations, please recharge ur account.');
   }
   if($res==1){
      return array("status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'add_tunnels', "uid"=>$customer_id, 'value'=>$tunnel);
   }
}

function add_server_clone($clone_id, $token){
  global $db;
  $tunnel=array();
  $sql2=$db->query("SELECT `subnet` FROM `server_subnets` WHERE `used_ips`=0");

  if($sql2->num_rows>0){
      $row2=$sql2->fetch_assoc();
      $subnet_clone=$row2['subnet'];
      $uname=server_username();
      $upass=server_password();
      $sql1=$db->query("SELECT * FROM `tunnels_data` WHERE `tunnel_id`=".$clone_id);
      $row_clone=$sql1->fetch_assoc();
        $row_clone['uname']=$uname;
        $row_clone['upass']=$upass;
        $row_clone['cloud_ip']=$subnet_clone;
        $row_clone['token']=$token;
        $row_clone['clone_id']=$clone_id;

        $tunnel['tunnel']=$row_clone;
        $tunnel['acl_info']=get_own_acl_info($clone_id);

        $res=remote("", "add_server_clone", $tunnel, "a", $token);
        //$_SESSION['users_data'][$id]["add_server_clone"]=$tunnel;
        if($res==1){
          $db->query("UPDATE `server_subnets` SET `used_ips`='1'  WHERE `subnet`='".$subnet_clone."'");
          return array("status"=>1, "data"=>"Your request under process, please wait...", 'message_type'=>'reply', "type"=>"add_server_clone", "uid"=>$row_clone['customer_id'], "value"=>$tunnel);
        }
      //}
  }else{
    return array("status"=>0, "data"=>"Unexpected error occured, try again");
  }
}
//does nothing?
function add_client_clone($clone_id, $token){
  global $db;
  $tunnel=array();
    if(!isset($row2)) $row2 = array();
  $subnet_clone=$row2['subnet'];
  $sql1=$db->query("SELECT * FROM `tunnels_data` WHERE `tunnel_id`=".$clone_id);
  $row_clone=$sql1->fetch_assoc();

    $tunnel['tunnel']=$row_clone;
    $tunnel['acl_info']=get_own_acl_info($clone_id);
    $res=remote("", "add_client_clone", $tunnel, "a", $token);
    //$_SESSION['users_data'][$id]["add_client_clone"]=$tunnel;
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

function edit_display($data, $token){
  global $db;
  //$sql="UPDATE `tunnels_data` SET `display_name`='".$data['value']."' WHERE `tunnel_id`=".$data['pk'];
  //if($db->query($sql)){
    $arr=array("id"=>$data['pk'], "value"=>$data['value']);
    //$_SESSION['users_data'][$data['pk']]["edit_display"]=$arr;
    $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='edit_display' AND `tunnel_id`=".$data['pk']." AND `token`='".$token."'");
    if($sql->num_rows==0){
      $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$data['pk'].", '".$token."', 'edit_display', '".serialize($arr)."')");
    }else{
      $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='edit_display' AND `tunnel_id`=".$data['pk']." AND `token`='".$token."'");
    }
    // $res=remote($_SESSION['user_id'], $data['pk'], "edit_display", $arr, "a");
    // if($res==1){
      return array('toclient'=>$_SESSION['token'], 'status'=>1, 'data'=>'Your request under process, please wait...', 'message_type'=>'reply', "type"=>"edit_display", "value"=>$arr);
    // }
  //}
}

function request_real_ip($data, $token){
  global $db;
  $real_res=$db->query("SELECT `real_ip` from `real_ip_list` where `in_use`='0'");
  $real_num = $real_res->num_rows;
  if($real_num>0){
    $real_row=$real_res->fetch_assoc();
    //if($db->query("UPDATE `tunnels_data` SET `real_ip`='".$real_row['real_ip']."' WHERE `tunnel_id`=".$data['id'])){

      $arr=array("id"=>$data['id'], "real_ip"=>$real_row['real_ip']);
      $res=remote($data['id'], "request_real_ip", $arr, "b", $token);
      // $res=remote($_SESSION['user_id'], $data['id'], "request_real_ip", array("id"=>$data['id'], "real_ip"=>$real_row['real_ip']), "b");
      if($res==1){
        $db->query("UPDATE `real_ip_list` SET `in_use`=1 WHERE `real_ip`='".$real_row['real_ip']."'");
        return array('toclient'=>$_SESSION['token'], 'status'=>1, 'data'=>'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'request_real_ip', 'id'=>$data['id'], 'value'=>$real_row['real_ip']);
      }
    //}
  }else{
    return array('status'=>0, 'data'=>'Real ip not assigned, Try again');
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

function packages($type, $plan_id, $id){
  global $db;
  if($type=="server"){
    $t_qry=$db->query("SELECT `real_ip` FROM `tunnels_data` WHERE `tunnel_id`=".$id);
    $t_qry_row=$t_qry->fetch_assoc();
    $real_chk=$db->query("SELECT `is_active` FROM `real_ip_list` WHERE `real_ip`='".$t_qry_row['real_ip']."'");
    $real_chk_row=$real_chk->fetch_assoc();
    if(isset($t_qry_row['real_ip']) && $real_chk_row['is_active']==1){
      $sqlPackage=$db->query("SELECT SUM(`tunnel` + `gateway` + `bidirection` + `realip`) total FROM `package_data` WHERE `plan_id`=".$plan_id);
    }else {
      $sqlPackage=$db->query("SELECT SUM(`tunnel` + `gateway` + `bidirection`) total FROM `package_data` WHERE `plan_id`=".$plan_id);
    }
    $rowPackage=$sqlPackage->fetch_assoc();
  } else if($type=="client"){
    $sqlPackage=$db->query("SELECT SUM(`tunnel` + `bidirection`) total FROM `package_data` WHERE `plan_id`=".$plan_id);
    $rowPackage=$sqlPackage->fetch_assoc();
  }
  //print_r($package);
  return $rowPackage['total'];
}

function change_tunnel($data){
    global $db;
    $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE (`type`='change_tunnel_client' OR `type`='change_tunnel_server') AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
    $sql2=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='create_new_acl' AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
    if($data['type']=="server"){
        $arr=array("id"=>$data['id']);
        if($sql->num_rows==0){
            $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$data['id'].", '".$data['token']."', 'change_tunnel_client', '".serialize($arr)."')");
        }else{
            $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."', `type`='change_tunnel_client' WHERE (`type`='change_tunnel_client' OR `type`='change_tunnel_server') AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
        }
        if($sql->num_rows > 0){
            $db->query("DELETE FROM `job_queue_temp` WHERE `type`='create_new_acl' AND `tunnel_id`=".$data['id']." AND `token`='".$data['token']."'");
        }

        //$db->query("INSERT INTO `job_queue` (`tunnel_id`, `action`, `group`, `new_data`, `added_time`, `token`) VALUES('".$data2['id']."', 'create_new_acl', 'a', '".serialize($data2)."', now(), '".$data2['token']."')");
        // $res=remote($data['id'], "change_tunnel_client", $arr, "b", $data['token']);
        //  if($res==1){
        return array('toclient'=>$_SESSION['token'], 'status'=>1, 'data'=>'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'change_tunnel_to_server', 'value'=>$arr);
        //  }
    }else if($data['type']=="client"){
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
        // $res=remote($data['id'], "change_tunnel_server", $arr, "b", $data['token']);
        // if($res==1){
        return array('status'=>1, 'data'=>'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'change_tunnel_to_client', 'value'=>$arr);
        //  }
    }
}
/*
function change_tunnel($data){
  global $db;
  if($data['type']=="server"){
        $arr=array("id"=>$data['id']);
        $res=remote($data['id'], "change_tunnel_client", $arr, "b", $data['token']);
        if($res==1){
          return array('toclient'=>$_SESSION['token'], 'status'=>1, 'data'=>'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'change_tunnel_to_server', 'value'=>$arr);
        }
  }else if($data['type']=="client"){
      $arr=array("id"=>$data['id']);
      $res=remote($data['id'], "change_tunnel_server", $arr, "b", $data['token']);
      if($res==1){
        return array('status'=>1, 'data'=>'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'change_tunnel_to_client', 'value'=>$arr);
      }
  }
}*/

function change_location($data, $token){
  global $db;
  //$sql="UPDATE `tunnels_data` SET `location`='".$data['value']."' WHERE `tunnel_id`=".$data['pk'];
  //if($db->query($sql)){
    $arr=array("id"=>$data['pk'], "value"=>$data['value']);
    //$_SESSION['users_data'][$data['pk']]["change_location"]=$arr;
    $sql=$db->query("SELECT * FROM `job_queue_temp` WHERE `type`='change_location' AND `tunnel_id`=".$data['pk']." AND `token`='".$token."'");
    if($sql->num_rows==0){
      $db->query("INSERT INTO `job_queue_temp` (`tunnel_id`, `token`, `type`, `data`) VALUES(".$data['pk'].", '".$token."', 'change_location', '".serialize($arr)."')");
    }else{
      $db->query("UPDATE `job_queue_temp` SET `data`='".serialize($arr)."' WHERE `type`='change_location' AND `tunnel_id`=".$data['pk']." AND `token`='".$token."'");
    }
    // $res=remote($_SESSION['user_id'], $data['pk'], "change_location", $arr, "b");
    // if($res==1){
      return array('toclient'=>$_SESSION['token'], 'status'=>1, 'data'=>'Your request under process, please wait...', 'message_type'=>'reply', "type"=>"change_location", "value"=>$arr);
    // }
  //}
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
  //echo "SELECT * FROM `job_queue_temp` WHERE `tunnel_id`=".$id." AND `token`='".$token."'";die;
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
      //unset($_SESSION["users_data"][$id]);
        if(!isset($user_id)) $user_id = ""; //What is it?????
      return array('toclient'=>$_SESSION['token'], 'status'=>1, 'data'=>'Your request submitted, please wait for while', 'message_type'=>'reply', 'type'=>'save_a_tunnel', 'value'=>$id, 'cust_id'=>$user_id);
    }
  }else{
    return array('status'=>0, 'data'=>'Your request either submitted, or not changed yet, please try again');
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

function delete_cloud($cloud_id, $token){
  global $db;
  if($db->query("DELETE FROM `clouds_data` WHERE `cloud_id`=".$cloud_id)){
    $sql=$db->query("SELECT * FROM `tunnels_data` WHERE `cloud_id`=".$cloud_id);
    if($sql->num_rows>0){
      while ($row=$sql->fetch_assoc()) {
        $data['id']=$row['tunnel_id'];
        $data['token']=$token;
        delete_tunnel($data);
      }
      $_SESSION['msg']="Cloud is_deleted successfully and its related tunnel will be removed soon";
    }
  }
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

function remote_server_delete($data){
  global $db;
  if($db->query("DELETE FROM `remote_server_list` WHERE `id`=".$data['id'])){
    return 1;
  }
}

function test_all_remote(){
  global $db;
  $arr=array();
  $sql=$db->query("SELECT * FROM `remote_server_list`");
  if($sql->num_rows>0){
      while($res=$sql->fetch_assoc()){
          $conn = new mysqli($res['remote_ip'], $res['server_uname'], $res['server_pass']);
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
      }
      $conn->close();
      return $arr;
  }
}

function delete_user_by_admin($customer_id){
  global $db;
  $delete_qry="DELETE FROM `customers_data` WHERE `customer_id`=".$customer_id;
  $delete_succ=$db->query($delete_qry);
  return $delete_succ;
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

function get_tunnels($token){
  global $db;
  $tunnel = "SELECT `tunnels_data`.*, `remote_server_list`.`server_name` `location`, `real_ip_list`.`is_active` `active` FROM `tunnels_data` left join `real_ip_list` on `tunnels_data`.`real_ip`=`real_ip_list`.`real_ip` left join `remote_server_list` on `tunnels_data`.`location`=`remote_server_list`.`id` WHERE  `tunnels_data`.`user_token`='".$db->real_escape_string($token)."' and `tunnels_data`.`is_deleted`=0";
    //echo $tunnel." order by group_id asc ";die;
    $sql=$db->query($tunnel." order by group_id asc, group_id");
    $data=array();
    while($row=$sql->fetch_assoc()){
        $data[]=$row;
    }
    return array("type"=>"get_tunnels", "message_type"=>"reply", "data"=>$data);
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
function dev_status_toggle($data)
{
    global $db;

    $res = $db->query("SELECT * FROM `tunnels_data` WHERE `tunnel_id`=" . $data['id']);
    if($res->num_rows > 0)
    {
        $row = $res->fetch_assoc();

        $dev_status = -1;
        if($row['dev_status'] == -1)
        {
            $dev_status = 1;
        }
        $db->query("UPDATE `tunnels_data` SET `dev_status`=" . $dev_status . " WHERE `tunnel_id`=" . $data['id']);
        return array('status' =>1 , 'message'=>array('st' => $dev_status, 'DeV' => $row['DeV']));
    }
    return array('status' =>0 , 'message'=>'Error performing request');
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
          $acl_info[$id]["is_installed"]=is_acl_installed($cur_tunnel_id,$value['id']);

          if($acl_info[$id]["is_installed"]==1){
              $acl_info[$id]["is_subscribed"] = 0;
          }else{
              $acl_info[$id]["is_subscribed"]=is_acl_subscribed($cur_tunnel_id,$value['id']);
          }

          $acl_info[$id]["status"]=check_acl_status($cur_tunnel_id,$value['id']);
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
                'short' => 'T'
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
/////////////////////////////////
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
        $sql="select td.* from tunnels_data as td, tunnel_acl_relation as tar where tar.tunnel_id=td.tunnel_id and tar.id='".$data['id']."'";
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


        $sql="select acl_id from user_acl_relation where tunnel_id='".$tunnel_id."' and status='1'";
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
  $res=remote($data['tid'], "save_acl_values", $data, "a", $data["token"]);
    if($res==1){
      return array("toclient"=>$_SESSION['token'], "status" => 1, 'data' => 'Your request under process, please wait...', 'message_type'=>'reply', 'type'=>'save_acl_values', 'value'=>$data['id']);
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

                <div class="page-content">
                    <div class="content" style="padding-top: 0px;">
                        <div class="page-title col-md-12">
                            <div class="cloud-name cloud-name-<?php echo($cloud_id); ?>">
                                <?php echo($cloud_name); ?>
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

                                <div class="meta width-30"><div class="cursor" id="chk_all_tunnel" data-toggle="tooltip" data-placement="bottom" data-val="0" title="Select all tunnels"><i class="fa  fa-square-o"></i></div><a href="javascript:void(0);" class="tunnel_vew_by_tnl tunnel_vew_by_tnl_<?php echo $cloud_id; ?>" data-cloud="<?php echo $cloud_id; ?>" data-dif="client"><i class="fa fa-sort"></i></a></div>

                                <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Groups"><i class="fa fa-fw fa-group"></i>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="tunnel_vew_by_grp" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>

                                <div class="meta width-120" data-toggle="tooltip" data-placement="bottom" title="Tunnel name"><i class="fa fa-cog"></i>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="tunnel_vew_by_name" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>
                                <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Bidirection mode"><i class="fa fa-chevron-left"></i><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="tunnel_vew_by_bidirection" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>
                                <div class="meta width-80" data-toggle="tooltip" data-placement="bottom" title="Location"><i class="fa fa-fw fa-globe"></i></div>
                                <div class="meta width-270" data-toggle="tooltip" data-placement="bottom" title="DeV">DeV</div>
                                <div class="meta width-80" data-toggle="tooltip" data-placement="bottom" title="VPN IP"><i class="fa  fa-map-pin"></i></div>
                                <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Points" style="margin-left: 4px;"><i class="fa fa-fw fa-dollar"></i></div>
                                <div class="meta width-80" data-toggle="tooltip" data-placement="bottom" title="Real IP">Real IP</div>
                                <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Gateway mode">Gateway&nbsp;&nbsp;<a href="javascript:void(0);" class="tunnel_vew_by_bidirection" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>
                                <div class="meta">&nbsp;</div>
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
            $tunnels_data=array();
            $tunnels_data[]=$tunnel_data;
            echo tunnels($tunnels_data);


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

            <div class="page-content">
                <div class="content" style="padding-top: 0px;">
                    <div class="page-title col-md-12">
                        <div class="cloud-name cloud-name-<?php echo($cloud_id); ?>">
                            <?php echo($cloud_name); ?>
                        </div>
                        <!--<span class="delete_cloud" data-val="<?php /*echo $cloud_id */?>"><i class="fa fa-trash pull-right cursor" data-toggle="tooltip" data-placement="top" title="Delete this Cloud"></i></span>-->
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

                            <div class="meta width-30"><div class="cursor" id="chk_all_tunnel" data-toggle="tooltip" data-placement="bottom" data-val="0" title="Select all tunnels"><i class="fa  fa-square-o"></i></div><a href="javascript:void(0);" class="tunnel_vew_by_tnl tunnel_vew_by_tnl_<?php echo $cloud_id; ?>" data-cloud="<?php echo $cloud_id; ?>" data-dif="client"><i class="fa fa-sort"></i></a></div>

                            <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Groups"><i class="fa fa-fw fa-group"></i>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="tunnel_vew_by_grp" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>

                            <div class="meta width-120" data-toggle="tooltip" data-placement="bottom" title="Tunnel name"><i class="fa fa-cog"></i>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="tunnel_vew_by_name" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>
                            <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Bidirection mode"><i class="fa fa-chevron-left"></i><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="tunnel_vew_by_bidirection" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>
                            <div class="meta width-80" data-toggle="tooltip" data-placement="bottom" title="Location"><i class="fa fa-fw fa-globe"></i></div>
                            <div class="meta width-270" data-toggle="tooltip" data-placement="bottom" title="DeV">DeV</div>
                            <div class="meta width-80" data-toggle="tooltip" data-placement="bottom" title="VPN IP"><i class="fa  fa-map-pin"></i></div>
                            <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Points" style="margin-left: 4px;"><i class="fa fa-fw fa-dollar"></i></div>
                            <div class="meta width-80" data-toggle="tooltip" data-placement="bottom" title="Real IP">Real IP</div>
                            <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Gateway mode">Gateway&nbsp;&nbsp;<a href="javascript:void(0);" class="tunnel_vew_by_bidirection" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>
                            <div class="meta">&nbsp;</div>
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

?>

