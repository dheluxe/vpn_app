<?php
// prevent the server from timing out
set_time_limit(0);
require "thread.php";
// include the web sockets server script (the server is started at the far bottom of this file)
require 'class.PHPWebSocket.php';
include(dirname(dirname(__FILE__)).'/includes/config.php');
include ROOT_PATH.'/includes/connection.php';
require ROOT_PATH.'/api/api_function.php';

// when a client sends data to the server
function wsOnMessage($clientID, $message, $messageLength, $binary) {
    print_r($message);
    global $db;
    global $Server;

    echo $message;
    $message=json_decode($message, 1);
    //$ip = long2ip( $Server->wsClients[$clientID][6] );
    // check if message length is 0
    if ($messageLength == 0) {
        $Server->wsClose($clientID);
        return;
    }
    if(is_array($message) && array_key_exists("toclient", $message)){ //send to all clients using this token
        $token=$message["toclient"];
        foreach ($Server->wsValidClients[$token] as $socket_id) {
            $Server->wsSend($socket_id, json_encode($message));
        }
    }else{
        if($message['message_type']=="request"){
            switch ($message['type']) {
                case 'authorize':
                    $token=$message['value']['token'];
                    $sql_check = $db->query("SELECT * FROM `customers_data` WHERE `token`='".$db->escape_string($token)."'");
                    if($sql_check->num_rows == 1){
                        $Server->wsValidClients[$token][]=$clientID;
                    }
                    else{
                        $Server->wsClose($clientID);
                    }
                    break;

                case 'dologin':
                    $arr=$message['value'];
                    $res=dologin($arr);
                    if($res['status'] == 1){
                        $utoken = $res['value']['token'];
                        $Server->wsValidClients[$utoken][]=$clientID;
                    }
                    $message=$res;
                    break;

                case 'dosignup':
                    $arr=$message['value'];
                    $res=doregister($arr);
                    $message=$res;

                    break;
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                case 'destroy_account':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $res=destroy_account($utoken);
                    }
                    $message=$res;
                    break;
                case 'add_cloud':
                    $res=add_cloud($message['data']);
                    $message=$res;
                    break;
                case 'delete_cloud':
                    $res=delete_cloud($message['data']);
                    $message=$res;
                    break;
                case 'get_tunnels':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $res=get_tunnels($utoken);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'get_profile_info':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $res=get_profile_info($utoken);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'get_home_info':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $res=get_home_info($utoken);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'get_social_info':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $res=get_social_info($utoken);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'change_searchable':
                    $res=change_searchable($message['data']);
                    $message=$res;
                    break;
                case 'change_dev_status':
                    $res=change_dev_status($message['data']);
                    $message=$res;
                    break;
                case 'route_change':
                    $res=route_change($message['data']);
                    $message=$res;
                    break;
                case 'plan_change':
                    $res=plan_change($message['data']);
                    $message=$res;
                    break;
                case 'internet_change':
                    $res=internet_change($message['data']);
                    $message=$res;
                    break;
                case 'change_tunnel':
                    $res=change_tunnel($message['data']);
                    $message=$res;
                    break;
                case 'remove_sharing':
                    $res=remove_sharing($message['data']);
                    $message=$res;
                    break;
                case 'add_tunnel':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=add_tunnel($message['data']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'save_a_tunnel':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $res=save_a_tunnel($message['data']['id'], $utoken);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'delete_tunnel':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=delete_tunnel($message['data']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'status_change':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=status_change($message['data']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'gateway_change':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=gateway_change($message['data']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'edit_display':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=edit_display($message['data']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'bidirection_change':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=bidirection_change($message['data']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'change_location':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=change_location($message['data']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'add_server_clone':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=add_server_clone($message['data']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'add_client_clone':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=add_client_clone($message['data']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'request_real_ip':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=request_real_ip($message['data']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'clear_tunnel_real_ip':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=clear_tunnel_real_ip($message['data']);
                        //$Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'change_tunnel_real_ip':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=change_tunnel_real_ip($message['data']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'clear_acl_real_ip':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=clear_acl_real_ip($message['data']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'get_acl_info':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=get_acl_info($message['data']['id']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'delete_acl':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=delete_acl($message['data']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'create_new_acl':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=create_new_acl($message['data']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'set_default_acl':
                    $res=set_default_acl($message['data']);
                    $message=$res;
                    break;
                case 'create_acl_clone':
                    $res=create_acl_clone($message['data']);
                    $message=$res;
                    break;
                case 'save_acl_name_description':
                    $res=save_acl_name_description($message['data']);
                    $message=$res;
                    break;

                case 'get_friend_list':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=get_friend_list($message['data']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'get_request_friend_list':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=get_request_friend_list($message['data']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'get_rejected_friend_list':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=get_rejected_friend_list($message['data']);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;
                case 'set_friend':
                    $res=set_friend($message['data']);
                    if($res['data']['status']=="deleted"){
                        $my_id=$res['data']['friend_id'];
                        $friend_id=$res['data']['customer_id'];
                        $my_token=get_customer_data_from_id($my_id)['token'];
                        $friend_token=get_customer_data_from_id($friend_id)['token'];
                        foreach ($Server->wsValidClients as $key => $value) {
                            if($my_token==$key){
                                foreach($value as $cid){
                                    if($cid==$clientID){
                                        $res['message']="Your request has been completed.";
                                        $Server->wsSend($cid, json_encode($res));
                                    }else{
                                        $res['message']="";
                                        $Server->wsSend($cid, json_encode($res));
                                    }
                                }
                            }elseif($friend_token==$key){ //send to friend.
                                $tmp=$res['data']['friend_id'];
                                $res['data']['friend_id']=$res['data']['customer_id'];
                                $res['data']['customer_id']=$tmp;
                                $res['message']="Your friend has deleted friendship.";
                                foreach($value as $cid){
                                    $Server->wsSend($cid, json_encode($res));
                                }
                            }
                        }
                    }elseif($res['data']['status']=="rejected"){

                    }elseif($res['data']['status']=="accepted"){
                        $my_id=$res['data']['friend_id'];
                        $friend_id=$res['data']['customer_id'];
                        $my_token=get_customer_data_from_id($my_id)['token'];
                        $friend_token=get_customer_data_from_id($friend_id)['token'];
                        foreach ($Server->wsValidClients as $key => $value) {
                            if($my_token==$key){
                                foreach($value as $cid){
                                    if($cid==$clientID){
                                        $res['message']="Your request has been completed.";
                                        $Server->wsSend($cid, json_encode($res));
                                    }else{
                                        $res['message']="";
                                        $Server->wsSend($cid, json_encode($res));
                                    }
                                }
                            }elseif($friend_token==$key){ //send to friend.
                                $tmp=$res['data']['friend_id'];
                                $res['data']['friend_id']=$res['data']['customer_id'];
                                $res['data']['customer_id']=$tmp;
                                $res['message']="Your friend has accepted your request.";
                                foreach($value as $cid){
                                    $Server->wsSend($cid, json_encode($res));
                                }
                            }
                        }
                    }elseif($res['data']['status']=="request"){

                    }

                    $message=null;
                    break;
                case 'get_badge_cnt':
                    $auth=false;
                    $utoken="";
                    foreach ($Server->wsValidClients as $key => $value) {
                        if(in_array($clientID, $value)){
                            $auth=true;
                            $utoken=$key;
                            break;
                        }
                    }
                    if($auth){
                        $message['data']['token']=$utoken;
                        $res=get_badge_cnt($utoken);
                        $Server->wsSend($clientID, json_encode($res));
                    }
                    $message=null;
                    break;

                case 'set_friend':
                    $res=set_friend($message['data']);
                    if($res['data']['status']=="deleted"){
                        $my_id=$res['data']['friend_id'];
                        $friend_id=$res['data']['customer_id'];
                        $my_token=get_customer_data_from_id($my_id)['token'];
                        $friend_token=get_customer_data_from_id($friend_id)['token'];
                        foreach ($Server->wsValidClients as $key => $value) {
                            if($my_token==$key){
                                foreach($value as $cid){
                                    if($cid==$clientID){
                                        $res['message']="Your request has been completed.";
                                        $Server->wsSend($cid, json_encode($res));
                                    }else{
                                        $res['message']="";
                                        $Server->wsSend($cid, json_encode($res));
                                    }
                                }
                            }elseif($friend_token==$key){ //send to friend.
                                $tmp=$res['data']['friend_id'];
                                $res['data']['friend_id']=$res['data']['customer_id'];
                                $res['data']['customer_id']=$tmp;
                                $res['message']="Your friend has deleted friendship.";
                                foreach($value as $cid){
                                    $Server->wsSend($cid, json_encode($res));
                                }
                            }
                        }
                    }
                    $message=null;
                    break;
























                case 'group_change':
                    if(is_valid_client($clientID)){
                        $id=$message['value']['id'];
                        $value=$message['value']['value'];
                        $res=group_change($id, $value, $clientID);
                        $message=$res;
                    }else{
                        $message=array("data"=>"You are not authorized to do any operations");
                        $Server->wsSend($clientID, json_encode($message));
                        $message=null;
                    }
                    break;

                case 'edit_email':
                    if(is_valid_client($clientID)){
                        $res=edit_email($message['value'], $clientID);
                        $message=$res;
                    }else{
                        $message=array("data"=>"You are not authorized to do any operations");
                        $Server->wsSend($clientID, json_encode($message));
                        $message=null;
                    }
                    break;

                case 'real_ip_status':
                    if(is_valid_client($clientID)){
                        $res=real_ip_status($message['value'], $clientID);
                        $message=$res;
                    }else{
                        $message=array("data"=>"You are not authorized to do any operations");
                        $Server->wsSend($clientID, json_encode($message));
                        $message=null;
                    }
                    break;

                case 'save_all_tunnel':
                    if(is_valid_client($clientID)){
                        $res=save_all_tunnel($clientID);
                        $message=$res;
                    }else{
                        $message=array("data"=>"You are not authorized to do any operations");
                        $Server->wsSend($clientID, json_encode($message));
                        $message=null;
                    }
                    break;

                case 'get_subnet':
                    if(is_valid_client($clientID)){
                        $res=get_subnet($message['data']);
                        $message=$res;
                        if($res['status']==0){
                            $Server->wsSend($clientID, json_encode($message));
                            $message=null;
                        }
                    }else{
                        $message=array("data"=>"You are not authorized to do any operations");
                        $Server->wsSend($clientID, json_encode($message));
                        $message=null;
                    }
                    break;
                case 'get_DeV':
                    if(is_valid_client($clientID)){
                        $res=get_dev($message['data']);
                        $message=$res;
                    }else{
                        $message=array("data"=>"You are not authorized to do any operations");
                        $Server->wsSend($clientID, json_encode($message));
                        $message=null;
                    }
                    break;
                case 'create_new_acl':
                    if(is_valid_client($clientID)){
                        $res=create_new_acl($message['data']);
                        $message=$res;
                    }else{
                        $message=array("data"=>"You are not authorized to do any operations");
                        $Server->wsSend($clientID, json_encode($message));
                        $message=null;
                    }
                    break;
                case 'get_acl_val':
                    if(is_valid_client($clientID)){
                        $res=get_acl_val($message['data']);
                        $message=$res;
                    }else{
                        $message=array("data"=>"You are not authorized to do any operations");
                        $Server->wsSend($clientID, json_encode($message));
                        $message=null;
                    }
                    break;
                case 'acl_update':
                    if(is_valid_client($clientID)){
                        $res=acl_update($message['data']);
                        $message=$res;
                    }else{
                        $message=array("data"=>"You are not authorized to do any operations");
                        $Server->wsSend($clientID, json_encode($message));
                        $message=null;
                    }
                    break;
                case 'chk_res':
                    if(is_valid_client($clientID)){
                        $res=chk_res($message['data']);
                        $message=$res;
                    }else{
                        $message=array("data"=>"You are not authorized to do any operations");
                        $Server->wsSend($clientID, json_encode($message));
                        $message=null;
                    }
                    break;
                case 'get_app':
                    if(is_valid_client($clientID)){
                        $res=array("status"=>1, "type"=>"get_app_result", "class"=>$message['class'], "message_type"=>"reply", "data"=>array(array("label"=>"ABC", "value"=>"abc"), array("label"=>"MNO", "value"=>"mno"), array("label"=>"XYZ", "value"=>"xyz")));
                        $message=$res;
                    }else{
                        $message=array("data"=>"You are not authorized to do any operations");
                        $Server->wsSend($clientID, json_encode($message));
                        $message=null;
                    }
                    break;

                case 'process_complete':
                    $arr="";
                    $sql="SELECT * FROM `job_queue` WHERE `is_complete_action`=2 AND `is_seen`=0";
                    $res=$db->query($sql);
                    if($res->num_rows>0){
                        while($result=$res->fetch_assoc()){

                            if($result['action']=="gateway_change"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"gateway_change_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="status_change"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"status_change_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="bidirection_change"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"bidirection_change_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="internet_change"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"internet_change_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="route_change"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"route_change_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="plan_change"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"plan_change_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="group_change"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"group_change_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="add_server_clone"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"add_server_clone_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, "", $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="add_client_clone"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"add_client_clone_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, "", $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="edit_email"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"edit_email_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="edit_display"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"edit_display_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="request_real_ip"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"request_real_ip_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="clear_tunnel_real_ip"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"clear_tunnel_real_ip_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="change_tunnel_real_ip"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"change_tunnel_real_ip_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="clear_acl_real_ip"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"clear_acl_real_ip_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="change_tunnel_client"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"change_tunnel_client_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="change_tunnel_server"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"change_tunnel_server_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="change_location"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"change_location_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="real_ip_status"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"real_ip_status_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="add_new_tunnel"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"add_tunnels_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, "", $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="delete_tunnel"){
                                $update_data=unserialize($result['old_data']);
                                $arr=array("status"=>1, "type"=>"delete_tunnel_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="create_new_acl"){
                                $update_data=unserialize($result['new_data']);
                                $arr=array("status"=>1, "type"=>"create_new_acl_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);

                            }
                            if($result['action']=="delete_acl"){
                                $update_data=unserialize($result['new_data']);
                                $arr=array("status"=>1, "type"=>"delete_acl_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);

                            }
                            if($result['action']=="clear_acl_values"){
                                $update_data=unserialize($result['new_data']);
                                $arr=array("status"=>1, "type"=>"clear_acl_values_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);

                            }
                            if($result['action']=="create_acl_clone"){
                                $update_data=unserialize($result['new_data']);
                                $arr=array("status"=>1, "type"=>"create_acl_clone_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);

                            }
                            if($result['action']=="save_acl_values"){
                                $update_data=unserialize($result['new_data']);
                                $arr=array("status"=>1, "type"=>"save_acl_values_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);

                            }
                            if($result['action']=="change_acl"){
                                $update_data=unserialize($result['new_data']);
                                $arr=array("status"=>1, "type"=>"change_acl_result", "message_type"=>"reply", "data"=>$update_data);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="get_remote_server_info"){
                                $old_data=unserialize($result['old_data']);
                                $row=$old_data['row'];
                                $arr=array("status"=>1, "type"=>"get_remote_server_info_result", "message_type"=>"reply", "data"=>$row);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                            if($result['action']=="set_remote_server_info"){
                                $old_data=unserialize($result['old_data']);
                                $row=$old_data['row'];
                                $arr=array("status"=>1, "type"=>"set_remote_server_info_result", "message_type"=>"reply", "data"=>$row);
                                $message=job_done($db, $result['tunnel_id'], $result['job_id'], $result['token'], json_encode($arr));
                                send_reply($result['token'], $Server, $message);
                            }
                        }
                    }
                    $message=null;
                    break;
                case 'deduct_cash':
                    $sql=$db->query("select * from `customers_data` where `is_active`=1");
                    $i = 0;
                    while($row = $sql->fetch_assoc()){
                        $deducted_cash=0;
                        $server_cost=0;
                        $client_cost=0;
                        $now=time();

                        $token=$row['token'];
                        $last_checked=$row['last_checked'];
                        $net=($now - $last_checked);
                        $cash=$row['Cash_amount'];
                        $cent=$cash * 100;
                        if($row['Cash_amount']>0){
                            $sql1=$db->query("select * from `tunnels_data` where `user_token`='".$token."' AND `is_deleted`=0");
                            while($row1 = $sql1->fetch_assoc()){
                                //echo $deducted_cash."<br>";
                                if($row1['tunnel_type']=="server"){

                                    $real_chk=$db->query("SELECT `is_active` FROM `real_ip_list` WHERE `real_ip`='".$row1['real_ip']."'");
                                    $real_chk_row=$real_chk->fetch_assoc();

                                    if(isset($row1['real_ip']) && $real_chk_row['is_active']==1){
                                        $sql_server=$db->query("SELECT SUM(`tunnel` + `gateway` + `bidirection` + `realip`) total FROM `package_data` WHERE `plan_id`=".$row1['plan_id']);
                                    }else {
                                        $sql_server=$db->query("SELECT SUM(`tunnel` + `gateway` + `bidirection`) total FROM `package_data` WHERE `plan_id`=".$row1['plan_id']);
                                    }
                                    $row_server=$sql_server->fetch_assoc();
                                    $server_cost=$server_cost + ($row_server['total'] * 100);
                                    if($deducted_cash>0){
                                        $deducted_cash=$deducted_cash + $server_cost;
                                    }else{
                                        $deducted_cash=$server_cost;
                                    }
                                }else if($row1['tunnel_type']=="client"){
                                    $sql_client=$db->query("SELECT SUM(`tunnel` + `bidirection`) total FROM `package_data` WHERE `plan_id`=".$row1['plan_id']);
                                    $row_client=$sql_client->fetch_assoc();
                                    $client_cost=$client_cost + ($row_client['total'] * 100);
                                    if($deducted_cash>0){
                                        $deducted_cash=$deducted_cash + $client_cost;
                                    }else{
                                        $deducted_cash=$client_cost;
                                    }
                                }
                            }
                            $deducted_cash=($deducted_cash / 2592000);
                            //echo $deducted_cash=floatval($deducted_cash);
                            $cent=$cent - ($deducted_cash  * $net);
                            $cash=($cent /100);
                            $db->query("update `customers_data` set `Cash_amount`='".$cash."', `last_checked`=".$now." where `token`='$token'");
                            $db->query("update `purchase_log` set `deduct`= '$deducted_cash' where `user_token`='$token'");
                            $message=array("status"=>1, "type"=>"deduct_cash_result", "message_type"=>"reply", "data"=>$cash);
                            send_reply($token, $Server, $message);
                            echo $deducted_cash;
                            echo "<br>";
                            /*echo "//////<br>";
                            echo $cash;
                            echo "<br>";*/
                        }
                        $i++;
                    }
                    $message=null;
                    break;
                case 'access_resource':
                    access_resource();
                    $message=null;
                    break;
            }
        }
        //The speaker is the only person in the room. Don't let them feel lonely.
        if($message!=null){
            if($message['type']=="login" || $message['type']=="signup" || $message['type']=="authorize"){
                $Server->wsSend($clientID, json_encode($message));
            }else{
                $token=null;
                foreach ($Server->wsValidClients as $user_token => $value) {
                    foreach ($value as $socket_id) {
                        if($clientID==$socket_id){
                            $token=$user_token;
                            break;
                        }
                    }
                }

                if($token!=null){
                    if(isset($Server->wsValidClients[$token])){
                        foreach ($Server->wsValidClients[$token] as $socket_id) {
                            $Server->wsSend($socket_id, json_encode($message));
                        }
                    }
                }else{
                    $message=array("data"=>"You are not authorized to do any operations");
                    $Server->wsSend($clientID, json_encode($message));
                }
            }
        }
    }
}

function job_done($db, $tunnel_id, $id, $token, $json){
    $db->query("UPDATE `job_queue` SET `is_seen`=1 WHERE `job_id`=".$id);
    $json=json_decode($json, 1);
    $comp_job="no";
    if($tunnel_id!=""){
        $comp_job=sql_job_check($db, $tunnel_id);
    }
    $json["free_field"]=$comp_job;
    return $json;
}

function sql_job_check($db, $tunnel_id){
    $sql_job_check=$db->query("SELECT * FROM `job_queue` WHERE `is_seen`=0 AND `tunnel_id`=".$tunnel_id);
    if($sql_job_check->num_rows>0){
        return "no";
    }else{
        return "yes";
    }
}

function send_reply($token, $Server, $message){
    if(isset($Server->wsValidClients[$token])){
        foreach ($Server->wsValidClients[$token] as $socket_id) {
            $Server->wsSend($socket_id, json_encode($message));
        }
    }
}

function is_valid_client($clientID){
    global $Server;
    foreach ($Server->wsValidClients as $user_token => $value) {
        foreach ($value as $socket_id) {
            if($clientID==$socket_id){
                return true;
            }
        }
    }
    return false;
}

/*function wsOnMessage($clientID, $message, $messageLength, $binary) {
    global $Server;
    $ip = long2ip( $Server->wsClients[$clientID][6] );

    // check if message length is 0
    if ($messageLength == 0) {
        $Server->wsClose($clientID);
        return;
    }

    //The speaker is the only person in the room. Don't let them feel lonely.
    if ( sizeof($Server->wsClients) == 1 )
        $Server->wsSend($clientID, "There isn't anyone else in the room, but I'll still listen to you. --Your Trusty Server");
    else
        //Send the message to everyone but the person who said it
        foreach ( $Server->wsClients as $id => $client )
            if ( $id != $clientID )
                $Server->wsSend($id, "Visitor $clientID ($ip) said \"$message\"");
}*/

// when a client connects
function wsOnOpen($clientID)
{
    global $Server;
    $ip = long2ip( $Server->wsClients[$clientID][6] );
    //echo $clientID;
    $Server->log( "$ip ($clientID) has connected." );
}

// when a client closes or lost connection
function wsOnClose($clientID, $status) {
    global $Server;
    $ip = long2ip( $Server->wsClients[$clientID][6] );

    $Server->log( "$ip ($clientID) has disconnected." );
    foreach ($Server->wsValidClients as $user_token => $value) {
        foreach ($value as  $index=>$socket_id) {
            if($clientID==$socket_id){
                unset($Server->wsValidClients[$user_token][$index]);
                break;
            }
        }
    }
}

/*
function status_responder()
{
    $address = '192.81.220.57';

    $port = 7272;
    $word = "";

    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
    $bind = socket_bind($sock, $address, $port);

    echo socket_listen($sock);
    while (true) {
        $client = socket_accept($sock);
        $input = socket_read($client, 2024);
    }
}
$thrd = new Thread('status_responder');
$thrd->start();
*/

// start the server
$Server = new PHPWebSocket();
$Server->bind('message', 'wsOnMessage');
$Server->bind('open', 'wsOnOpen');
$Server->bind('close', 'wsOnClose');
// for other computers to connect, you will probably need to change this to your LAN IP or external IP,
// alternatively use: gethostbyaddr(gethostbyname($_SERVER['SERVER_NAME']))
//$Server->wsStartServer('198.211.127.72', 8880);
$Server->wsStartServer(MAIN_SERVER_IP, WEB_SOCKET_PORT);
?>