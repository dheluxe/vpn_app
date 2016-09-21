<?php
include_once 'common/session_check.php';
require_once 'includes/config.php';
require_once 'includes/connection.php';
require_once 'api/api_function.php';

$cloud_id=$_REQUEST['cloud'];
$_SESSION['cloud']=$cloud_id;
$tunnel = "SELECT `tunnels_data`.*, `remote_server_list`.`server_name` `location`, `real_ip_list`.`is_active` `active` FROM `tunnels_data` left join `real_ip_list` on `tunnels_data`.`real_ip`=`real_ip_list`.`real_ip` left join `remote_server_list` on `tunnels_data`.`location`=`remote_server_list`.`id` WHERE `tunnels_data`.`cloud_id`='".$db->real_escape_string($cloud_id)."' and `tunnels_data`.`user_token`='".$db->real_escape_string($_SESSION['token'])."' and `tunnels_data`.`is_deleted`=0";
    $sql=$db->query($tunnel." order by group_id asc, group_id");
    $data=array();
    while($row=$sql->fetch_assoc()){
        $data[]=$row;
    }
$tunnels_data=array();
foreach($data as $tunnel_data){
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
<?php include_once 'common/head.php'; ?>

<?php include_once 'common/header.php'; ?>
    <a href="#" class="scrollup">Scroll</a>

    <div class="page-content">
        <div id="portlet-config" class="modal hide">
            <div class="modal-header">
                <button data-dismiss="modal" class="close" type="button"></button>
                <h3>Widget Settings</h3>
            </div>
            <div class="modal-body"> Widget settings form goes here </div>
        </div>
        <div class="content">
            <ul class="breadcrumb">
                <li>
                    <p>YOU ARE HERE</p>
                </li>
                <li><a href="#" class="active">Tunnels</a> </li>
            </ul>
            <div class="page-title"> <i class="icon-custom-left"></i>
                <h3>Search - <span class="semi-bold">Results</span></h3>
                <span id="delete_cloud" data-val="<?php echo $cloud_id ?>"><i class="fa fa-trash pull-right cursor" data-toggle="tooltip" data-placement="top" title="Delete this Cloud"></i></span>
            </div>
            <div class="col-md-12">
                <div class="col-md-8">
                    <select id="contact_ms" multiple="multiple">
                    <?php echo $html; ?>
                    </select>
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <div id="Parks" data-uid="<?php echo $_SESSION['user_id'] ?>" class="just list">

                <div class="list_header">
                <div class="meta" data-toggle="tooltip" data-placement="right" title="ACL"><i class="fa fa-cogs"></i></div>


                    <div class="meta" id="SortByName" data-toggle="tooltip" data-placement="right" title="Add tunnels"><a href="javascript:void(0);" data-val="<?php echo $cloud_id; ?>" data-mail="<?php echo $_SESSION['email'] ?>" data-count="0" id="tunnel_add_form_btn"><i class="fa fa-fw fa-plus-circle"></i></a></div>

                    <div class="meta" id="" data-toggle="tooltip" data-placement="right" title="Save all"><a href="javascript:void(0);" data-val="<?php echo $cloud_id; ?>" data-count="0" id="all_tunnel_save_btn"><i class="fa fa-floppy-o"></i></a></div>

                    <div class="meta width-30"><div class="cursor" id="chk_all_tunnel" data-toggle="tooltip" data-placement="bottom" data-val="0" title="Select all tunnels"><i class="fa  fa-square-o"></i></div><a href="javascript:void(0);" id="tunnel_vew_by_tnl" data-cloud="<?php echo $cloud_id; ?>" data-dif="client"><i class="fa fa-sort"></i></a></div>

                    <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Groups"><i class="fa fa-fw fa-group"></i>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" id="tunnel_vew_by_grp" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>


                    <div class="meta width-120" data-toggle="tooltip" data-placement="bottom" title="Tunnel name"><i class="fa fa-cog"></i>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" id="tunnel_vew_by_name" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>
                    <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Bidirection mode"><i class="fa fa-chevron-left"></i><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" id="tunnel_vew_by_bidirection" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>
                    <div class="meta width-80" data-toggle="tooltip" data-placement="bottom" title="Location"><i class="fa fa-fw fa-globe"></i></div>
                    <div class="meta width-270" data-toggle="tooltip" data-placement="bottom" title="DeV">DeV</div>
                    <div class="meta width-80" data-toggle="tooltip" data-placement="bottom" title="VPN IP"><i class="fa  fa-map-pin"></i></div>
                    <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Points" style="margin-left: 4px;"><i class="fa fa-fw fa-dollar"></i></div>
                    <div class="meta width-80" data-toggle="tooltip" data-placement="bottom" title="Real IP">Real IP</div>
                    <div class="meta" data-toggle="tooltip" data-placement="bottom" title="Gateway mode">Gateway&nbsp;&nbsp;<a href="javascript:void(0);" id="tunnel_vew_by_bidirection" data-cloud="<?php echo $cloud_id; ?>" data-dif="asc"><i class="fa fa-sort"></i></a></div>
                    <div class="meta">&nbsp;</div>
                </div>
                <form id="tunnels_form_field" style="display:none;">
                    <input type="button" id="btn_add_tunnel" class="btn btn-sm btn-primary" data-cloud="<?php echo $cloud_id; ?>" value="Submit">
                    <input type="reset" class="btn btn-sm btn-warning" id="tunnel_form_close_btn" value="Cancel">
                </form>
                <div id="tunnel_body" class="tunnel_body_<?php echo $_SESSION['user_id'] ?>">
                    <?php
                     echo tunnels($tunnels_data);
                    ?>
                </div>
            </div>
        </div>

    </div>
</div>

<div id="dialog-form" class="animate bounceIn" title="ACL Settings">
  <div id="acl_div_cont"></div>
</div>

<div id="dialog-sponsore-form" class="animate bounceIn" title="Sponsore Tunnel">

  <div id="sponsore_div_cont">
    <div class="row">
        <input type="hidden" id="all_data">
        <div class="col-lg-10">
          <select id="cust_ms" multiple="multiple">
          <?php echo $html_cust; ?>
          </select>
        </div>
        <div class="col-lg-2">
            <button class="btn btn-primary shared_submit_btn">Submit</button>
        </div>
    </div>
  </div>
</div>

<?php include_once 'common/script.php'; ?>

</body>
</html>
