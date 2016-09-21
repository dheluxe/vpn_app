<?php
global $db;
$query3="";
if(!isset($_SESSION['token'])){
    $query3="SELECT * FROM `customers_data` WHERE `customer_id`=1";
}else{
    $query3="SELECT * FROM `customers_data` WHERE `token`<>'".$db->real_escape_string($_SESSION['token'])."'";
}
$sql3=$db->query($query3);

?>

<!-- ACL search modal -->
<div id="ACLsearchModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg" style="width: 1340px;">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Search destination ACL</h4>
      </div>
      <div class="modal-body">
        <div class="row">

            <div class="left-friend-panel" style="margin-top: -16px; width:160px;">
                <div class="left-friend-list" style="border: none;">
                    <div class="left-friend-list-title" style="margin-top: 50px;">
                        Friends List
                    </div>
                    <div class="left-friend-list-content">
                        <?php
                        $all_friends=array();
                        while($row=$sql3->fetch_assoc()){
                        $get_customer_acl_destination=get_customer_acl_destination($row['customer_id']);
                        $row['shared_acl_cnt']=count($get_customer_acl_destination);
                        $all_friends[]=$row;
                        //print_r($row);
                        ?>
                        <div class="left-friend-list-content-row1 <?php echo($selected_customer_id==$row['customer_id'] ? 'friend-selected':''); ?>" data-customer_id="<?php echo($row['customer_id']); ?>" data-customer_name="<?php echo($row['name']); ?>">
                            <div class="profile-info-box">
                                <div style="float: left">
                                    <img class="profile_short_image" src="<?php echo(($row['profile_image']!="")?$row['profile_image']:ROOT_URL.'/assets/img/profiles/demo-user.jpg'); ?>" alt="<?php echo($row['profile_image']); ?>">
                                </div>
                                <div class="friend_info" style="float: left;">
                                    <div class="friend_name"><?php echo($row['name']); ?><?php /*echo($row['shared_acl_cnt']); */?></div>
                                    <div class="friend_tag_id"><?php /*echo($row['tag_id']); */?></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="profile-extra-info-box">
                                <div class="like-box custom-info-box">
                                    <div class="icon_description">
                                        <?php echo($row['tag_id']); ?>
                                    </div>
                                </div>
                                <div class="friends-box custom-info-box">
                                    <div class="icon_description">
                                        <?php echo($row['shared_acl_cnt']); ?>
                                    </div>
                                </div>

                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>



            <div class="" style="margin-top: -15px;margin-bottom: -15px; width:87%; float: left; border-left: 1px solid #e4e4e4;">
                <div class="col-lg-12" style="margin-bottom: 10px;">
                    <div id="msg" class=""></div>
                </div>
                <div class="col-lg-12">
                    <div class="col-lg-6">
                        <input type="text" class="form-control acl_search_input" placeholder="Enter email id to search destination acl"/>
                        <level class=""></level>
                    </div>
                    <div class="col-lg-2">
                        <input type="button" class="btn acl_search_btn" value="Search">
                    </div>
                    <div class="col-lg-4">

                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="acl_search_result" style="overflow: auto; width: 1160px;">
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- end of ACL search modal -->
    <script src="assets/js/jquery-1.8.3.min.js"></script>
    <script src="assets/js/jquery.nicescroll.js" type="text/javascript"></script>
    <script src="assets/js/common-scripts.js"></script>



<script src="assets/js/initial.js" type="text/javascript"></script>

<script src="assets/plugins/bootstrapv3/js/bootstrap.min.js" type="text/javascript"></script>
<!-- crop image js -->
<script src="assets/crop/dist/cropper.min.js" type="text/javascript"></script>

<script src="assets/crop/dist/js/main.js" type="text/javascript"></script>
<!-- end of crop image js -->

<script src="webarch/js/jquery.dragscroll.js" type="text/javascript"></script>

<script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-block-ui/jqueryblockui.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-numberAnimate/jquery.animateNumbers.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>

<script src="webarch/js/webarch.js" type="text/javascript"></script>

<script src="webarch/js/jquery-ui.min.js" type="text/javascript"></script>

<script src="assets/js/chat.js" type="text/javascript"></script>

<script src="assets/plugins/bootstrap-select2/select2.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-datatable/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-datatable/extra/js/dataTables.tableTools.min.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/plugins/datatables-responsive/js/datatables.responsive.js"></script>
<script type="text/javascript" src="assets/plugins/datatables-responsive/js/lodash.min.js"></script>
<script src="assets/js/datatables.js" type="text/javascript"></script>

<script src="assets/plugins/jquery-mixitup/jquery.mixitup.min.js" type="text/javascript"></script>
<script src="assets/js/jquery.notify.min.js" type="text/javascript"></script>
<script src="assets/js/fancywebsocket.js" type="text/javascript"></script>

<script src="assets/js/search_results.js" type="text/javascript"></script>
<script src="assets/js/select2.min.js" type="text/javascript"></script>
<script src="assets/js/bootstrap-editable.min.js" type="text/javascript"></script>

<script src="webarch/js/popover.min.js" type="text/javascript"></script>


<script src="assets/js/tabcontent.js" type="text/javascript"></script>
<?php
if(isset($_REQUEST['customer_id'])){
    ?>
    <script>
        var customer_id="<?php echo($_REQUEST['customer_id']); ?>";
    </script>

    <script src="assets/js/customer_frined.js" type="text/javascript"></script>
<?php
}
?>
<script src="assets/js/panel_layout.js" type="text/javascript"></script>



<script type="text/javascript">
    var user_cloud="<?php echo $_SESSION['cloud']; ?>";
</script>



<script type = "text/javascript">
    $( document ).ready(function() {
        if ($.cookie('hide-after-load') == 'yes') {
          $('.amount_div').hide();
          $('.amount_div1').show();
        }else{
            $('.amount_div').show();
          $('.amount_div1').hide();
        }
    $('.hide_amount').click(function(){
        {
            $.cookie('hide-after-load', 'yes', {expires: 7 });
            $('.amount_div1').show();
            $('.amount_div').hide();
        }
    });
    $('.show_amount').click(function(){
        {
            $.cookie('hide-after-load', '', {expires: 0 });
             $('.amount_div').show();
            $('.amount_div1').hide();
        }
    });
});
</script>



<?php ob_end_flush() ?>