
<?php @session_start();
//print_r($_SESSION);die;
require_once 'includes/config.php';
require_once 'includes/connection.php';
include_once 'api/api_function.php';

global $db;
$selected_customer_id=0;
if(isset($_REQUEST['customer_id'])){
    $selected_customer_id=$_REQUEST['customer_id'];
}
$sql=$db->query("SELECT * FROM `clouds_data` WHERE `user_token`='".$db->real_escape_string($_SESSION['token'])."'");
$sql1=$db->query("SELECT * FROM `clouds_data` WHERE `cloud_id` IN (SELECT `cloud_id` FROM `shared_tunnel` WHERE `shared_with`=".$_SESSION['user_id'].")");
$sql2=$db->query("SELECT * FROM `customers_data` WHERE `token`='".$db->real_escape_string($_SESSION['token'])."'");
$sql_point=$db->query("SELECT `settings_value` FROM `settings` WHERE `settings_name`='cast_to_point'");

$row1 = $sql2->fetch_assoc();
$customer_id=$row1['customer_id'];
$_SESSION['uname']=array('0'=>$row1['name']);
$_SESSION['profile_image']=$row1['profile_image'];
$point = $sql_point->fetch_assoc();
$get_customer_acl_destination=get_customer_acl_destination($row1['customer_id']);
$row1['shared_acl_cnt']=count($get_customer_acl_destination);
?>
<!--sidebar start-->
<aside>
    <div id="sidebar" class="nav-collapse" style="padding-top: 46px;">
        <!-- sidebar menu start-->
        <div class="self-profile-content-row <?php echo($selected_customer_id==$row1['customer_id'] ? 'friend-selected':''); ?>" data-customer_id="<?php echo($row1['customer_id']); ?>">
            <div class="self-profile-info-box">
                <div style="float: left">
                    <img class="profile_short_image" src="<?php echo(($row1['profile_image']!="")?$row1['profile_image']:ROOT_URL.'/assets/img/profiles/demo-user.jpg'); ?>" alt="<?php echo($row1['profile_image']); ?>">
                </div>
                <div class="friend_info" style="float: left;">
                    <div class="self_name"><?php echo($row1['display_name']); ?><?php /*echo('MY SELF'); */?><?php /*echo($row['shared_acl_cnt']); */?></div>
                    <div class="self_tag_id"><?php echo($row1['tag_id']); ?></div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <?php
        $friends_sql="SELECT id FROM `friends_data` WHERE (friend_id=".$_SESSION['customer_id']." OR customer_id=".$_SESSION['customer_id'].") AND status='accepted'";
        $request_friends_sql="SELECT id FROM `friends_data` WHERE (friend_id=".$_SESSION['customer_id']." OR customer_id=".$_SESSION['customer_id'].") AND status='request'";
        $rejected_friends_sql="SELECT id FROM `friends_data` WHERE (friend_id=".$_SESSION['customer_id']." OR customer_id=".$_SESSION['customer_id'].") AND status='rejected'";
        $friends_cnt=$db->query($friends_sql)->num_rows;
        $request_friends_cnt=$db->query($request_friends_sql)->num_rows;
        $rejected_friends_cnt=$db->query($rejected_friends_sql)->num_rows;
        ?>
        <div class="tab-bar">
            <input type="hidden" name="selected_tab" id="selected_tab" value="friends"/>
            <div class="tab-element tab-element-selected" data-type="friends" style="border-right: 1px solid #ffffff;">
                Friend(<span class="current-friend-count"><?php echo($friends_cnt); ?></span>)

            </div>
            <div class="tab-element" data-type="request" style="border-right: 1px solid #ffffff;">
                Request(<span class="request-friend-count"><?php echo($request_friends_cnt); ?></span>)
            </div>
            <div class="tab-element" data-type="rejected">
                Rejected(<span class="rejected-friend-count"><?php echo($rejected_friends_cnt); ?></span>)
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="friend-search-box">
            <input type="text" class="friend-search-text" style="padding-left: 40px!important;" placeholder="Search for friends..."/>
        </div>

        <div id="friend-list-box" style="height: 90%;">
            <div class="friends-box">
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
                    <div class="left-friend-list-content-row <?php echo ($odd_even_class); ?> <?php echo ($last_class); ?> custom_popup_context_item"
                        data-friend_id="<?php echo ($friend_id); ?>" data-customer_name="<?php echo ($row['name']); ?>">
                        <div class="profile-info-box" style="float: left;">
                            <div style="float: left">
                                <img class="friend_short_image"
                                     src="<?php echo (($row['profile_image']!="")?$row['profile_image']:ROOT_URL.'/assets/img/profiles/demo-user.jpg'); ?>"
                                     alt="<?php echo ($row['profile_image']); ?>">
                            </div>
                            <div class="friend_info" style="float: left;">
                                <div class="friend_name"><?php echo ($row['display_name']); ?></div>
                                <div
                                    class="friend_tag_id"><?php echo ($row['tag_id']); ?>: <?php echo ($row['shared_acl_cnt']); ?></div>
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
            </div>
            <div class="request-friends-box hidden"></div>
            <div class="rejected-friends-box hidden"></div>
        </div>
        <!-- sidebar menu end-->
    </div>
</aside>
<!--sidebar end-->

<script>
    $.contextMenu({
        selector: '.custom_popup_context_item',
        callback: function(key, options) {
            console.log(key);
            console.log(options);
            if(key=="send_point"){
                $('#send_point_to_friend').modal('show');
            }
        },
        items: {
            "send_point": {name: "Send Point",accesskey: "e"},
            "delete": {name: "Delete"},
            "sep1": "---------",
            "quit": {name: "Quit"}
        }
    });

    $('body').on('mousedown','.custom_popup_context_item',function(e){
        var customer_id=$(this).attr("data-friend_id");
        var customer_name=$(this).attr("data-friend_name");
        $("#send_point_to_friend #friend_id").val(customer_id);
        $("#send_point_to_friend #friend_name").html(customer_name);
    });

    /*$.contextMenu({
        selector: '.color-box',
        callback: function(key, options) {
            console.log(key);
            console.log(options);
            if(key=="clear"){

            }
            else if(key=="undo"){

            }
        },
        items: {
            "clear": {name: "Clear"},
            "undo": {name: "Undo"}
        }
    });
*/
    $.contextMenu({
        selector: '.color-box',
        callback: function(key, options) {
            console.log(key);
            console.log(options);
            if(key=="clear"){
                console.log(acl_value);
                var tunnel_id=$(this).attr("data-tid");
                var acl_info=$(this).attr("class").split(" ")[1].split("-");
                var id=acl_info[2];
                var type=acl_info[1];
                var data=acl_info[0];
                if(acl_value[id]==undefined)
                    acl_value[id]={};
                if(acl_value[id][type]==undefined)
                    acl_value[id][type]={};

                acl_value[id][type][data]="";
                $("."+data+"-"+type+"-"+id).css("color","#000000");
                $("."+data+"-"+type+"-"+id).css("opacity","0.3");
                notify_msg("warning", "You have to save this settings...");
                console.log(acl_value);
            }
            else if(key=="undo"){
                console.log(acl_value);
                var tunnel_id=$(this).attr("data-tid");
                var acl_info=$(this).attr("class").split(" ")[1].split("-");
                var id=acl_info[2];
                var type=acl_info[1];
                var data=acl_info[0];
                if(acl_value[id][type][data]!=undefined){
                    delete acl_value[id][type][data];
                }
                $("."+data+"-"+type+"-"+id).css("color","#ffffff");
                $("."+data+"-"+type+"-"+id).css("opacity","1");
                console.log(acl_value);
            }
        },
        items: {
            "clear": {name: "Clear"},
            "undo": {name: "Undo"}
        }
    });

</script>

<div class="modal fade" id="send_point_to_friend" tabindex="-1" role="dialog" aria-labelledby="send_point_to_friend" aria-hidden="true">
    <div class="modal-dialog" style="z-index:1200">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Send point to <span id="friend_name">your friend</span></h4>
            </div>
            <form action="" id="send_point_form" method="post">
                <div class="modal-body">
                    <div class="alert alert-success" role="alert" id="manual_add_success_message" style="display: none;"></div>
                    <div class="alert alert-danger" role="alert" id="manual_add_error_message" style="display: none;"></div>

                    <div class="form-group" style="position:relative;">
                        <input type="hidden" name="friend_id" id="friend_id" value="">
                        <input type="text" name="point" class="form-control" value="" placeholder="Give points" style="padding-left: 40px !important; margin-bottom: 8px;">
                        <span class="doller"><i class="fa fa-fw fa-money"></i></span>
                    </div>
                    <input type="hidden" name="my_id" id="my_id" value="<?php echo $_SESSION['user_id'];?>">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="popup_send_point_btn" type="button">Send</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>