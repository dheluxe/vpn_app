<?php @session_start();
if(isset($_GET['request'])){
  require_once 'includes/config.php';
  require_once 'includes/connection.php';
  require_once 'api/api_function.php';
  include_once 'includes/functions.php';
  include_once 'includes/PayPal.class.php';

  //include_once 'class.PHPWebSocket.php';

    /*if($_GET['request']!="dologin" && $_GET['request']!="user_register" && $_GET['request']!="admin_login"){
        if(!isset($_SESSION['vpn_user']) || trim($_SESSION['vpn_user']) == ""){
            echo("empty_session");
            die;
        }
    }*/

  switch($_GET['request']){

    case 'job_queue_info':
      $result=job_queue_info();
      //print_r($result);
      echo json_encode($result);
    break;

      //login section
    case 'dologin':
   // print_r($_POST);
        $var_dologin=dologin($_POST);
        echo json_encode($var_dologin);
        break;

      //registration sction
    case 'user_register':
        $var_doregister=doregister($_POST);
        echo json_encode($var_doregister);
        break;

      //delete contact section
    case 'del_cus':
        $get_cus_id=$_GET['cus_id'];
        $var_del_cus=dodel_cus($get_cus_id);
        echo json_encode($var_del_cus);
        $pth='contacts.php';
        header("Location:".$pth);
        break;

       //add cloud section
	  case 'add_cloud':
          if(!empty($_POST)){
              $result=add_cloud($_POST);
              echo json_encode($result);
          }
        break;

    case 'add_contact':
        $var_doadd_contact=doaddcontact($_POST);
        echo json_encode($var_doadd_contact);
        break;

    case 'update_profile':
        $var_doupdate_profile=doupdate_profile($_POST);
        echo json_encode($var_doupdate_profile);
        break;

    case 'changePassword':
        $pass_updateresult=changePassword($_POST);
        echo json_encode($pass_updateresult);
       break;

    case 'add_voucher':
      //print_r($_POST);
        $addVoucher=addVoucher($_POST);
        echo json_encode($addVoucher);
       break;

    case 'redirect_to_auth':
        $url = $google['auth_url']."?scope=https://www.googleapis.com/auth/contacts.readonly&state=".$_SESSION['vpn_user']."&redirect_uri=".$google['redirect_uri']."&client_id=".$google['client_id']."&&approval_prompt=force&response_type=code";
            header("location:".$url);
        break;

    case 'google_auth':
        if(isset($_GET['code'])){
            $postData = "grant_type=authorization_code&client_id=".$google['client_id']."&client_secret=".$google['client_secret']."&redirect_uri=".$google['redirect_uri']."&code=".$_GET['code'];
            $auth_data = curl_get_file_contents("https://accounts.google.com/o/oauth2/token", $postData);
            $auth_info = json_decode($auth_data, 1);
            if(isset($auth_info['access_token'])){
                $xmlresponse = curl_get_file_contents("https://www.google.com/m8/feeds/contacts/default/full?max-results=10000&alt=json&oauth_token=".$auth_info['access_token']);
                //header("Content-Type: application/atom+xml");
                $result = json_decode( $xmlresponse, 1 );
                //print_r($result['feed']['entry'][0]['title']['$t']);
                $i = 0;

                if(isset($result['feed']['entry']) and !empty($result['feed']['entry'])) {

                    foreach ($result['feed']['entry'] as $key => $value) {

                        if($db->query("INSERT INTO `users_data` SET `name`='".$db->real_escape_string($value['title']['$t'])."',`user_email`='".$db->real_escape_string($value['gd$email'][0]['address'])."'")) {
                            $id = $db->insert_id;
                        }
                        else{
                            $sql = $db->query("SELECT `id` FROM `users_data` WHERE `user_email`='".$db->real_escape_string($value['gd$email'][0]['address'])."'");
                            $row = $sql->fetch_assoc();
                            $id = $row['id'];
                        }
                        if($db->query("INSERT INTO `customer_user_relations` SET `customer_id`='" . $db->real_escape_string($_SESSION['user_id']) . "', `user_id`='" . $id . "'"))
                        {
                            $i ++;
                        }
                    }
                }
                $_SESSION['flash_message'] = $i." Contacts Imported";
            }
            else {
                $_SESSION['flash_message'] = "Google Authentication problem";
            }
        }
        else {
            $_SESSION['flash_message'] = "Google Authentication problem";
        }
        header( "Location:contacts.php" );
        break;

    case 'addTunnel':
        $result=addTunnel($_POST, $_GET['token']);
        $json_arr=json_encode($result);
        //socket_msg($_SESSION['token'], $result);
        echo $json_arr;
        break;

    case 'delete_tunnel':
        $result=delete_tunnel($_GET);
        $json_arr=json_encode($result);
        //socket_msg($_SESSION['token'], $result);
        echo $json_arr;
      break;

    case 'gateway_change':
      $id=$_REQUEST['id'];
      $val=$_REQUEST['val'];
      $client_id=$_REQUEST['token'];
      $result=gateway_change($id, $val, $client_id);
      $json_arr=json_encode($result);
      //socket_msg($json_arr);
      echo $json_arr;
    break;

    case 'status_change':
      $id=$_REQUEST['id'];
      $val=$_REQUEST['val'];
      $client_id=$_REQUEST['token'];
      $result=status_change($id, $val, $client_id);
      $json_arr=json_encode($result);
      //socket_msg($json_arr);
      echo $json_arr;
    break;

    case 'bidirection_change':
      $result=bidirection_change($_REQUEST);
      $json_arr=json_encode($result);
      echo $json_arr;
    break;

    case 'internet_change':
      $id=$_REQUEST['id'];
      $val=$_REQUEST['val'];
      $client_id=$_REQUEST['token'];
      $result=internet_change($id, $val, $client_id);
      $json_arr=json_encode($result);
      //socket_msg($json_arr);
      echo $json_arr;
    break;

    case 'route_change':
      $id=$_REQUEST['id'];
      $val=$_REQUEST['val'];
      $client_id=$_REQUEST['token'];
      $result=route_change($_REQUEST);
      $json_arr=json_encode($result);
      //socket_msg($json_arr);
      echo $json_arr;
    break;

    case 'plan_change':
      $id=$_REQUEST['id'];
      $val=$_REQUEST['val'];
      $client_id=$_REQUEST['token'];
      $result=plan_change($id, $val, $client_id);
      $json_arr=json_encode($result);
      echo $json_arr;
    break;

    case 'group_change':
      $id=$_REQUEST['id'];
      $val=$_REQUEST['val'];
      $client_id=$_REQUEST['token'];
      $result=group_change($id, $val, $client_id);
      $json_arr=json_encode($result);
      //socket_msg($json_arr);
      echo $json_arr;
    break;

    case 'add_server_clone':
      $clone_id=$_GET['id'];
      $result=add_server_clone($_GET);
      $json_arr=json_encode($result);
      echo $json_arr;
    break;

    case 'add_client_clone':
      $clone_id=$_GET['id'];
      $result=add_client_clone($_GET);
      $json_arr=json_encode($result);
      echo $json_arr;
    break;

    case 'update_plan':
      $update_plan=update_plan($_POST['selectedVal']);
      $json_arr=json_encode($update_plan);
      //socket_msg($_SESSION['token'], $result);
      echo $json_arr;
    break;

    case 'redirect_paypal':
        $p = new paypal_class;
        $item = 'Recharge with '.$_REQUEST['amount'];
        $amount = floatval($_REQUEST['amount']);
        $currency = 'USD';
        $trans_id="TRANS".time().rand(100,999);
        $success_url = get_url('request', array('request'=>'payment_success', 'trans_id'=>$trans_id));
        $cancel_url = get_url('request', array('request'=>'payment_notify', 'trans_id'=>$trans_id));
        $notify_url = get_url('request', array('request'=>'payment_cancel', 'trans_id'=>$trans_id));
        $paypal_email = 'rajenpal033@gmail.com';
        $remarks="Transaction canceled by user";
        $db->query("insert into `purchase_log` (`customer_id`, `add`, `transaction_id`, `remarks`, `status`) values ('".$db->real_escape_string($_SESSION['user_id'])."', '$amount', '$trans_id', '$remarks', 'c')");

        $p->paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        $p->add_field('business', $paypal_email);
        $p->add_field('return', $success_url);
        $p->add_field('cancel_return', $cancel_url);
        $p->add_field('notify_url', $notify_url);
        $p->add_field('item_name', $item);
        $p->add_field('amount', $amount);
        $p->add_field('currency_code', $currency);
        $p->submit_paypal_post();
        $remarks="Paid Successful with Paypal";
       break;

    case 'payment_success':
       $trans_id=$_GET['trans_id'];
       $remarks="Transaction successful with Paypal";
       $db->query("update `purchase_log` set `status`='s' and `remarks`='$remarks' where transaction_id='$trans_id'");
       $db->query("update `customers_data` set `Cash_amount` = (Cash_amount)+(select `add` from `purchase_log` WHERE `transaction_id`='$trans_id') where `customer_id`=".$db->real_escape_string($_SESSION['user_id'])."'");
       $_SESSION['msg_paypal_s']="Transaction successful with Paypal";
       header("Location:billing.php");
       break;

    case 'payment_notify':
       $trans_id=$_GET['trans_id'];
       $remarks="Transaction Unsuccessful with Paypal";
       $db->query("update `purchase_log` set `status`='i' and `remarks`='$remarks' where transaction_id='$trans_id'");
       $_SESSION['msg_paypal_u']="Transaction Unsuccessful with Paypal";
       header("Location:billing.php");
       break;

    case 'payment_cancel':
       $trans_id=$_GET['trans_id'];
       $remarks="Transaction canceled by user";
       $db->query("update `purchase_log` set `status`='i' and `remarks`='$remarks' where transaction_id='$trans_id'");
       header("Location:billing.php");
       break;

    case 'stripe':
       $token_id=$_GET['token_id'];
       $email=$_GET['email'];
       $amount=$_GET['amount']/100;
       $trans_id="TRANS".time().rand(100,999);
       if($token_id!=""){
           $remarks="Transaction successful with Stripe, email id is ".$email." Token id is ".$token_id;
           $db->query("insert into `purchase_log` (`customer_id`, `add`, `transaction_id`, `remarks`, `status`) values ('".$db->real_escape_string($_SESSION['user_id'])."', '$amount', '$trans_id', '$remarks', 's')");
           $db->query("update `customers_data` set `Cash_amount` = (Cash_amount)+'$amount' WHERE customer_id='".$db->real_escape_string($_SESSION['user_id'])."'");
           $_SESSION['msg_stripe_s']="Transaction successful with Stripe";
           header("Location:billing.php");
       }else{
           $remarks="Transaction unsuccessful with Stripe, email id is ".$email." Token id is ".$token_id;
           $db->query("insert into `purchase_log` (`customer_id`, `transaction_id`, `remarks`) values ('".$db->real_escape_string($_SESSION['user_id'])."', '$amount', '$trans_id', '$remarks')");
           $_SESSION['msg_stripe_u']="Transaction unsuccessful with Stripe";
           $status=0;
           header("Location:billing.php");
       }
       break;

    case 'stripe_cancel':
      $trans_id="TRANS".time().rand(100,999);
      $remarks="Transaction canceled by user";
      $db->query("insert into `purchase_log` (`customer_id`, `transaction_id`, `remarks`, `status`) values ('".$db->real_escape_string($_SESSION['user_id'])."', '$trans_id', '$remarks', 'c')");
      $_SESSION['msg_stripe_c']="Transaction unsuccessful with Stripe";
      header("Location:billing.php");
      break;

    case 'getTunnel':
      $result=getTunnel($_GET);
      //print_r($result);die;
      echo json_encode($result);
    break;

    case 'getAllTunnel':
       $result=getAllTunnel($_GET);
       //print_r($result);die;
       echo json_encode($result);
    break;

    case 'edit_email':
      $cid=$_GET['token'];
      $result=edit_email($_POST, $cid);
      $json_arr=json_encode($result);
      //socket_msg($_SESSION['token'], $result);
      echo $json_arr;
    break;

    case 'edit_display':
      $cid=$_GET['token'];
      $data=$_POST;
      $data['token']=$cid;
      $result=edit_display($data);
      $json_arr=json_encode($result);
      echo $json_arr;
    break;

    case 'request_real_ip':
      $result=request_real_ip($_GET);
      $json_arr=json_encode($result);
      echo $json_arr;
    break;

    case 'clear_tunnel_real_ip':
      $cid=$_GET['token'];
      $result=clear_tunnel_real_ip($_GET);
      $json_arr=json_encode($result);
      echo $json_arr;
    break;

    case 'change_tunnel_real_ip':
      $cid=$_GET['token'];
      $result=change_tunnel_real_ip($_GET);
      $json_arr=json_encode($result);
      echo $json_arr;
    break;

    case 'clear_acl_real_ip':
      $cid=$_GET['token'];
      $result=clear_acl_real_ip($_GET);
      $json_arr=json_encode($result);
      echo $json_arr;
    break;

    case 'get_server_name':
      $result=get_server_name();
      $json_arr=json_encode($result);
      //socket_msg($json_arr);
      echo $json_arr;
    break;

    case 'packages':
      $type=$_REQUEST['type'];
      $plan=$_REQUEST['p_id'];
      $id=$_REQUEST['id'];
      $result=packages($type, $plan, $id);
      echo $result;
    break;

    case 'change_tunnel':
      $result=change_tunnel($_GET);
      $json_arr=json_encode($result);
      //socket_msg($_SESSION['token'], $result);
      echo $json_arr;
    break;

    case 'change_location':
      $cid=$_GET['token'];
      $data=$_POST;
      $data['token']=$cid;
      $result=change_location($data);
      $json_arr=json_encode($result);
      //socket_msg($_SESSION['token'], $result);
      echo $json_arr;
    break;

    case 'real_ip_status':
      $cid=$_GET['token'];
      $result=real_ip_status($_GET, $cid);
      $json_arr=json_encode($result);
      //socket_msg($_SESSION['token'], $result);
      echo $json_arr;
    break;

    case 'save_a_tunnel':
      $id=$_REQUEST['id'];
      $result=save_a_tunnel($id, $_GET['token']);
      $json_arr=json_encode($result);
      //socket_msg($_SESSION['token'], $result);
      echo $json_arr;
    break;

    case 'save_all_tunnel':
      $result=save_all_tunnel($_GET['token']);
      $json_arr=json_encode($result);
      //socket_msg($_SESSION['token'], $result);
      echo $json_arr;
    break;

    case 'delete_cloud':
      $result=delete_cloud($_GET['cloud_id'], $_GET['token']);
    break;

    case 'active_user_by_admin':
        $customer_id=$_GET['id'];
        $status_value=$_GET['val'];
        echo active_user_by_admin($customer_id, $status_value);
        break;

    case 'remote_server_status_change':
      remote_server_status_change($_REQUEST);
    break;

    case 'remote_server_edit':
        $result=remote_server_edit($_POST);
        echo json_encode($result);
    break;

    case 'cust_edit':
        $result=cust_edit($_POST);
        echo json_encode($result);
    break;

    case 'plan_edit':
        $result=plan_edit($_POST);
        echo json_encode($result);
    break;

    case 'voucher_edit':
        $result=voucher_edit($_POST);
        echo json_encode($result);
    break;

    case 'remote_server_delete':
      $res=remote_server_delete($_POST);
      echo $res;
    break;

    case 'test_all_remote':
      $res=test_all_remote();
      echo json_encode($res);
    break;

    case 'delete_user_by_admin':
      echo delete_user_by_admin($_REQUEST['id']);
    break;

    case 'delete_voucher_by_admin':
      echo delete_voucher_by_admin($_POST['id']);
    break;

    case 'delete_vpn_by_admin':
      echo delete_vpn_by_admin($_POST['id']);
    break;

    case 'delete_real_by_admin':
      echo delete_real_by_admin($_POST['id']);
    break;

    case 'admin_login':
      $res=admin_login($_REQUEST);
      if($res!=0){
        if(isset($res['id'])){
          $_SESSION['admin_id'] = $res['id'];
        } else if(isset($res['customer_id'])){
          $_SESSION['admin_id'] = $res['customer_id'];
        }
        header('location:admin/cust_info.php');
      }else{
        $_SESSION['msg'] = "Username or password missmatch";
        header('location:admin/index.php');
      }
    break;
    case 'dev_status_toggle':
        $res = dev_status_toggle($_REQUEST);
        echo json_encode($res);
        break;
    case 'login_as_user':
   // print_r($_POST);
        $var_dologin=login_as_user($_GET);
        echo json_encode($var_dologin);
        break;
    case 'get_acl_info':
      $res = get_acl_info($_REQUEST['id']);
      echo json_encode($res);
    break;

    case 'create_new_acl':
      $res = create_new_acl($_REQUEST);
      echo json_encode($res);
    break;

    case 'acl_update':
      $res = acl_update($_POST);
      echo $res;
    break;

    case 'get_acl_val':
      $res=get_acl_val($_REQUEST);
      echo $res;
    break;

    case 'get_available_real_ip':
      $res=get_available_real_ip();
      echo $res;
    break;

    case 'chk_res':
      $res = chk_res($_REQUEST);
      echo json_encode($res);
    break;

    case 'create_acl_clone':
      $res = create_acl_clone($_REQUEST);
      echo json_encode($res);
    break;

    case 'delete_acl':
      $res = delete_acl($_REQUEST);
      echo json_encode($res);
    break;

    case 'clear_acl_values':
      $res = clear_acl_values($_REQUEST);
      echo json_encode($res);
    break;

    case 'change_acl':
      $res = change_acl($_REQUEST);
      echo json_encode($res);
    break;

    case 'set_default_acl':
      $res = set_default_acl($_REQUEST);
      echo json_encode($res);
    break;

    case 'save_acl_values':
      $res = save_acl_values($_POST);
      //print_r($_POST);die;
      echo json_encode($res);
      //print_r(json_decode($_POST['data']));
    break;
    case 'get_acl_detail':
      $res=get_acl_detail($_REQUEST);
      echo json_encode($res);
    break;
    case 'point':
      $res = point($_POST);
      echo $res;
    break;

      case 'send_point_to_friend':
          $res = send_point_to_friend($_POST);
          echo $res;
      break;

    case 'shared_tunnel_search':
      $res=shared_tunnel_search($_POST);
      echo $res;
    break;
    case 'shared_tunnel':
      $res=shared_tunnel($_POST);
      echo $res;
    break;
    case 'task_server_status':
          $res=task_server_status($_GET['tunnelid']);
    echo json_encode($res);
    break;
    case 'get_acl_destination_base':
        $installed_acls=array();
        $cur_tunnel_acls=array();
        $acl_destinations=get_acl_destination_base($_REQUEST['email'], $_SESSION['user_id']);
        $cur_tunnel_acl_info=get_acl_info($_REQUEST['tunnel_id']);
        if(count($cur_tunnel_acl_info['data'])>0){
            $cur_tunnel_acls=array_keys($cur_tunnel_acl_info['data']);
        }
      if($acl_destinations!=0){
          echo json_encode(array('acl_destinations'=>$acl_destinations,'cur_tunnel_acls'=>$cur_tunnel_acls));
      }else{
          echo $acl_destinations;
      }
    break;

    case 'get_acl_destination_base_from_customer_id':
          $installed_acls=array();
          $cur_tunnel_acls=array();
          $acl_destinations=get_customer_acl_destination($_REQUEST['customer_id']);
          $cur_tunnel_acl_info=get_acl_info($_REQUEST['tunnel_id']);
          if(count($cur_tunnel_acl_info['data'])>0){
              $cur_tunnel_acls=array_keys($cur_tunnel_acl_info['data']);
          }
          if($acl_destinations!=0){
              echo json_encode(array('acl_destinations'=>$acl_destinations,'cur_tunnel_acls'=>$cur_tunnel_acls));
          }else{
              echo $acl_destinations;
          }
    break;

    case 'get_customer_acl_destination':

      $acl_destinations=get_customer_acl_destination($_REQUEST['customer_id']);

      if($acl_destinations!=0){
          echo json_encode(array('acl_destinations'=>$acl_destinations));
      }else{
          echo $acl_destinations;
      }
      break;


    case 'cloud_tunnels':
      $cloud_tunnels=cloud_tunnels($_REQUEST['cloud_id'],$_REQUEST['cloud_name'],$_REQUEST['token']);
      echo $cloud_tunnels;
      break;

    case 'install_acl':
      $res = install_acl($_POST, $_SESSION['user_id']);
      echo $res;
      break;

      case 'change_profile_picture':
      $res = change_profile_picture($_FILES['prof_img']);
      echo json_encode($res);
      break;

    case 'acc_logout':
        session_destroy();
        header("Location:login.php");

	  case 'logout':
            session_destroy();
            $pth=$_POST['redirect_to'].'login/';
            print_r($pth);
            die;
            header("Location:".$pth);
          break;

      case 'admin_logout':
        session_destroy();
        header("Location:admin/index.php");
      break;

      case 'set_def_cash':
        $data=$_POST;
        $res=set_def_cash($data);
        if($res){
            echo true;
        }
      break;

      case 'set_point_val':
        $data=$_POST;
        $res=set_point_val($data);
        if($res){
            echo true;
        }
      break;
      case 'check_tunnel_sponsored':
          $tunnel_id=$_REQUEST['id'];
          $res=check_tunnel_sponsored($tunnel_id);
          echo json_encode($res);
      break;
      case 'remove_sharing':
          $data=$_REQUEST;
          $res=remove_sharing($data);
          echo json_encode($res);
      break;
      case 'save_acl_name_description':
          $data=$_GET;
          $res=save_acl_name_description($data);
          if($res){
              echo true;
          }else{
              echo false;
          }
          break;
      case 'change_searchable':
          $data=$_GET;
          $res=change_searchable($data);
          echo $res;
          break;
      case 'get_tunnels_for_cloud':
          $cloud_id=$_REQUEST['cloud_id'];
          $res=get_tunnels_for_cloud($cloud_id);
          echo json_encode($res);
          break;
      case 'get_tunnels_from_ids':
          $data=$_POST;
          $res=get_tunnels_from_ids($data);
          echo $res;
          break;
      case 'get_sponsor_tunnels_from_ids':
          $data=$_POST;
          $res=get_sponsor_tunnels_from_ids($data);
          echo $res;
          break;
      case 'get_customers':
          $data=$_REQUEST;
          get_customers($data);
          break;
      case 'set_friend':
          $data=$_REQUEST;
          $res=set_friend($data);
          echo $res;
          break;
      case 'get_friends':
          $data=$_REQUEST;
          get_friends($data);
          break;
      case 'get_request_friends':
          $data=$_REQUEST;
          get_request_friends($data);
          break;
      case 'get_rejected_friends':
          $data=$_REQUEST;
          get_rejected_friends($data);
          break;
      case 'update_badge_cnt':
          $data=$_REQUEST;
          $res=update_badge_cnt($data);
          echo(json_encode($res));
          break;
      case 'get_friends_for_dialog':
          $data=$_REQUEST;
          get_friends_for_dialog($data);
          break;
      case 'update_cloud':
          $data=$_REQUEST;
          update_cloud($data);
          break;
      case 'destroy_account':
          session_destroy();
          $token=$_GET['token'];
          $res=destroy_account($token);
          echo(json_encode($res));
          break;
      case 'get_cost_data_from_tunnel_id':
          $tunnel_id=$_GET['tunnel_id'];
          $res=get_cost_data_from_tunnel_id($tunnel_id);
          echo(json_encode($res));
          break;
      case 'get_remote_server_info':
          $tunnel_id=$_GET['tunnel_id'];
          $res=get_remote_server_info($_GET);
          echo(json_encode($res));
          break;
      case 'set_remote_server_info':
          $res=set_remote_server_info($_GET);
          echo(json_encode($res));
          break;
      case 'save_diagram_data':
          $res=save_diagram_data($_POST);
          print_r($res);
          break;
      case 'get_badge_cnt':
          $res=get_badge_cnt($_GET['id']);
          echo(json_encode($res));
          break;
      case 'run_ospf':
          $res=run_ospf($_GET);
          print_r($res);
          break;
      case 'stop_ospf':
          $res=stop_ospf($_GET);
          print_r($res);
          break;
      default:
          echo "404 not found";
    }
}



function socket_msg($token, $json){
  $messagetosend =  array('toclient' => $token,
            'data' => $json);
  try{
    $context = new ZMQContext();
    //print_r($context);
    $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'pushsock');
    $socket->connect("tcp://localhost:8880");
    $socket->send(json_encode($messagetosend));
  }
  catch(Exception $e){
    echo $e->getMessage();
  }
}