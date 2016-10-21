<?php
@session_start();
include_once 'common/session_check.php';
require_once 'includes/config.php';
require_once 'includes/connection.php';
include_once 'common/head.php';
include_once 'common/header.php';
include_once 'common/sidebar.php';

$sql_user = $db->query("SELECT `users_data`.`id`, `users_data`.`user_email`, `users_data`.`name`, `users_data`.`date_added` FROM `users_data` INNER JOIN `customer_user_relations` ON `users_data`.`id`=`customer_user_relations`.`user_id` AND `customer_user_relations`.`user_token`='".$db->real_escape_string($_SESSION['token'])."'");
?>

<section id="main-content" style="padding-top: 160px;">
    <input id="tab1" type="radio" name="tabs" class="custom_res_tab" checked>
    <label class="custom_res_tab" for="tab1">Home</label>

    <input id="tab2" type="radio" name="tabs" class="custom_res_tab" >
    <label class="custom_res_tab"  for="tab2">Connectivity Manager</label>

    <input id="tab3" type="radio" name="tabs" class="custom_res_tab" >
    <label class="custom_res_tab" for="tab3">Profile Info</label>

    <input id="tab4" type="radio" name="tabs" class="custom_res_tab" >
    <label class="custom_res_tab" for="tab4">Social</label>

    <div class="custom_res_tab">
        <div id="content2" class="hidden">
            <?php
            include 'home.php';
            ?>
        </div>
    </div>
</section>

<div id="dialog-form" class="animate bounceIn" title="ACL Settings">
    <div id="acl_div_cont"></div>
</div>

<div class="modal fade" id="add_cloud" tabindex="-1" role="dialog" aria-labelledby="add_cloud" aria-hidden="true">
    <div class="modal-dialog" style="z-index:1200">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Add New Cloud</h4>
            </div>
            <form id="add_cloud_form">
                <div class="modal-body">
                    <div class="alert alert-success" role="alert" id="manual_add_success_message" style="display: none;"></div>
                    <div class="alert alert-danger" role="alert" id="manual_add_error_message" style="display: none;"></div>

                    <div class="form-group">
                        <label for="recipient-name" class="control-label">Cloud Name:</label>
                        <input type="text" class="form-control" name="cloud_name" id="cloud_name" placeholder="enter cloud name">
                    </div>
                    <input type="hidden" name="cloud_email" id="cloud_email" value="<?php echo $_SESSION['email'] ?>">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"> Save </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$friend_ids=array();
$friends_sql="SELECT * FROM `friends_data` WHERE (friend_id=".$_SESSION['customer_id']." OR customer_id=".$_SESSION['customer_id'].") AND status='accepted'";
$friends_query=$db->query($friends_sql);
if($friends_query->num_rows>0){
    while($row=$friends_query->fetch_assoc()){
        if($row['customer_id']==$_SESSION['customer_id']){
            $friend_ids[]=$row['friend_id'];
        }else{
            $friend_ids[]=$row['customer_id'];
        }
    }
}

$friend_ids_str="xxxxxxx";
if(count($friend_ids)>0){
    $friend_ids_str=implode(",",$friend_ids);
}

$query3="";
$query3="SELECT * FROM `customers_data` WHERE `customer_id` IN (".$friend_ids_str.")";
$sql3=$db->query($query3);
?>

<div id="ACLsearchModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 1030px;margin-bottom: 0px;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Search destination ACL</h4>
            </div>
            <div class="modal-body" style="height: 690px;">
                <div style="border:1px solid #ccc; float:left;">
                    <div id="dialog-sidebar" class="nav-collapse" style="width:180px;">

                    <div id="dialog-friend-list-box" style="height: 657px;">
                        <?php
                        if(count($friend_ids)>0){
                            $all_friends=array();
                            $i=0;
                            while($row=$sql3->fetch_assoc()){
                                $i++;
                                $last_class="";
                                if($i==$sql3->num_rows){
                                    $last_class="left-friend-list-content-row-last";
                                }
                                $odd_even_class="left-friend-list-content-row-odd";
                                if(intval($i/2)*2==$i){
                                    $odd_even_class="left-friend-list-content-row-even";
                                }
                                $get_customer_acl_destination=get_customer_acl_destination($row['customer_id']);
                                $row['shared_acl_cnt']=count($get_customer_acl_destination);
                                $all_friends[]=$row;
                                //print_r($row);
                                ?>
                                <div class="dialog-left-friend-list-content-row <?php echo($odd_even_class); ?> <?php echo($last_class); ?> <?php echo($selected_customer_id==$row['customer_id'] ? 'friend-selected':''); ?>" data-customer_id="<?php echo($row['customer_id']); ?>" data-customer_name="<?php echo($row['name']); ?>">
                                    <div class="dialog-profile-info-box" style="float: left;">
                                        <div style="float: left">
                                            <img class="friend_short_image" src="<?php echo(($row['profile_image']!="")?$row['profile_image']:ROOT_URL.'/assets/img/profiles/demo-user.jpg'); ?>" alt="<?php echo($row['profile_image']); ?>">
                                        </div>
                                        <div class="friend_info" style="float: left;">
                                            <div class="friend_name"><?php echo($row['name']); ?></div>
                                            <div class="friend_tag_id"><?php echo($row['tag_id']); ?>: <?php echo($row['shared_acl_cnt']); ?></div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="dialog-friend-action-box">
                                    <span class="dialog-friend-action">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </span>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            <?php
                            }
                        }
                        ?>
                    </div>
                    <!-- sidebar menu end-->
                    </div>
                </div>
                <div id="dialog-main-content" style="float:left;width:800px;">
                    <div class="row">
                        <div class="col-md-12" style="margin-bottom: 10px;">
                            <div id="msg" class=""></div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 20px;">
                            <div class="col-md-6">
                                <input type="text" class="form-control acl_search_input" placeholder="Enter email id to search destination acl"/>
                                <level class=""></level>
                            </div>
                            <div class="col-md-2">
                                <input type="button" class="btn acl_search_btn" value="Search">
                            </div>
                            <div class="col-md-4">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="acl_search_result">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <!--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
            </div>

        </div>
    </div>
</div>

<div id="sponsorModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 1030px;margin-bottom: 0px;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Select customer for sponsor</h4>
            </div>
            <div class="modal-body">
                <div style="border:1px solid #ccc; float:left;">
                    <div id="dialog-sidebar" class="nav-collapse" style="width:210px;">
                        <div id="sponsor-friend-list-box" style="height: 657px;">

                        </div>
                        <!-- sidebar menu end-->
                    </div>
                </div>
                <div id="dialog-main-content" style="float:left;width:785px;">
                    <div class="row">
                        <div class="col-md-12" style="margin-bottom: 10px;">
                            <div id="msg" class=""></div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 20px;">
                            <div class="col-md-6">
                                <input type="text" class="form-control customer_search_input" placeholder="Enter email/name/tag_id to search customer"/>
                                <level class=""></level>
                            </div>
                            <div class="col-md-2">
                                <input type="button" class="btn customer_search_btn" value="Select">
                            </div>
                            <div class="col-md-4">

                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="customer_search_result">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <!--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
            </div>

        </div>
    </div>
</div>

</section>
<?php include_once 'common/script.php';?>
</body>

</html>
