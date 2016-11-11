
<?php
//ini_set('session.gc_maxlifetime', 10*60*60);
@session_start();
//print_r($_SESSION);die;
require_once 'includes/config.php';
require_once 'includes/connection.php';
include_once 'api/api_function.php';
if(!$config['SITE_STATUS'])
{
    header('Location: ../vpn/offline.html');
    die();
}
ob_end_flush();

global $db;

//////////////////////////////////////////////////////////////////////////////////////////////
if(isset($_POST['update-profile-btn'])){
    if($_POST['update-profile-btn']=='Save'){
        date_default_timezone_set('UTC');
        $nowDate = new DateTime('now');
        $strNowDate    =   $nowDate->format('Y_m_d_H_i_s');
        $profile_image="";
        //print_r($_FILES);

        $file=isset($_FILES['profile_image']) ? $_FILES['profile_image']:array();
        if(isset($file['name']) && $file['name']!=="" && intval($file['error'])===0){
            $image_array=array('image/jpeg','image/png','image/gif','image/bmp');
            //if(in_array($file['type'],$image_array))
            {
                $image_file=$strNowDate.'_image_'.$file["name"];
                $filename=ROOT_DIR.'/assets/img/profiles/'.$image_file;
                move_uploaded_file($file['tmp_name'],$filename);
                $profile_image=ROOT_URL.'/assets/img/profiles/'.$image_file;
            }
        }
        if($profile_image==""){
            $db->query("update `customers_data` set `name`='".$db->real_escape_string($_POST['name'])."', display_name='".$db->real_escape_string($_POST['display_name'])."', phone=".$db->real_escape_string($_POST['phone']).", remail='".$db->real_escape_string($_POST['reemail'])."' WHERE `customer_id`=".$db->real_escape_string($_SESSION['user_id'])."");
        }else{
            $db->query("update `customers_data` set `name`='".$db->real_escape_string($_POST['name'])."', display_name='".$db->real_escape_string($_POST['display_name'])."', phone=".$db->real_escape_string($_POST['phone']).", profile_image='".$profile_image."', remail='".$db->real_escape_string($_POST['reemail'])."' WHERE `customer_id`=".$db->real_escape_string($_SESSION['user_id'])."");
        }

        unset($_POST['update-profile-btn']);

    }
}

$selected_customer_id=0;
if(isset($_REQUEST['customer_id'])){
    $selected_customer_id=$_REQUEST['customer_id'];
}

$sql=$db->query("SELECT * FROM `clouds_data` WHERE `user_token`='".$db->real_escape_string($_SESSION['token'])."'");
//echo "SELECT * FROM `clouds_data` WHERE `cloud_id` = (SELECT `cloud_id` FROM `shared_tunnel` WHERE `shared_with`=".$_SESSION['user_id'].")";die;
$sql1=$db->query("SELECT * FROM `clouds_data` WHERE `cloud_id` IN (SELECT `cloud_id` FROM `shared_tunnel` WHERE `shared_with`=".$_SESSION['user_id'].")");

$sql2=$db->query("SELECT * FROM `customers_data` WHERE `token`='".$db->real_escape_string($_SESSION['token'])."'");

$sql3=$db->query("SELECT * FROM `customers_data` WHERE `token`<>'".$db->real_escape_string($_SESSION['token'])."'");

$sql_point=$db->query("SELECT `settings_value` FROM `settings` WHERE `settings_name`='cast_to_point'");


$row1 = $sql2->fetch_assoc();
$customer_id=$row1['customer_id'];
$_SESSION['uname']=array('0'=>$row1['name']);
$_SESSION['profile_image']=$row1['profile_image'];
$_SESSION['customer_id']=$row1['customer_id'];
$_SESSION['customer_email']=$row1['email'];
$point = $sql_point->fetch_assoc();

?>

<body>
<div id="blockDiv"></div>
<script>
    var customer_id="<?php echo($selected_customer_id); ?>";
    var current_customer_id="<?php echo($_SESSION['customer_id']); ?>"
    var current_customer_email="<?php echo($_SESSION['customer_email']); ?>"
</script>
<section id="container">
    <!--header start-->
    <header class="header black-bg">
        <div class="sidebar-toggle-box" style="display : none;">
            <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
        </div>
        <!--logo start-->
        <a href="#" class="logo get_remote_server_info"><b>Pro-Monitoring Service</b></a>
        <!--logo end-->

        <div class="tabs-panel-top">
            <div class="tabs-panel-top-block-left tabs-panel-top-billing">
                <a href="billing.php" target="_blank" class="tabs-panel-top-block-link tabs-panel-top-billing-link">Billing</a>
            </div>
            <div class="tabs-panel-top-block-left tabs-panel-top-contacts">
                <a href="contacts.php" target="_blank" class="tabs-panel-top-block-link tabs-panel-top-contacts-link">Contacts</a>
            </div>

            <div class="pull-right" style="margin-top:-3px;">
                <ul class="nav quick-section ">
                    <li class="quicklinks">
                        <a data-toggle="dropdown" class="dropdown-toggle pull-right" href="#" id="user-options">
                        <span style="margin-right:5px;">
                            <img class="profile_top_image" src="<?php echo(($_SESSION['profile_image']!='')?$_SESSION['profile_image']:ROOT_URL.'/assets/img/profiles/demo-user.jpg'); ?>" alt="<?php echo $_SESSION['profile_image']; ?>"/>
                        </span>
                        </a>
                        <ul class="dropdown-menu  pull-right" role="menu" aria-labelledby="user-options">
                            <li>
                                <div class="header_account_email"><?php echo $_SESSION['email'] ?></div>
                                <div class="header_account_destroy">Destroy Account</div>
                                <a href="request.php?request=acc_logout"><i class="fa fa-power-off"></i>&nbsp;&nbsp;Log Out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="pull-right tag_div">
                <div class="">
                    <b class=""> #<?php echo $row1['display_name'].$row1['tag_id']; ?></b>
                </div>
            </div>

            <div class="pull-right amount_div">
                <div class="show_div">
                    <b class="show_div_b" data-p="<?php echo $point['settings_value']; ?>">Available Points: <?php echo round($row1['Cash_amount'], 2) * $point['settings_value'] ?></b>
                </div>
                <i class="fa fa-eye-slash hide_amount" title="Hide"></i>
            </div>
            <div class="account_searchable_switch_block">
                <input id="cmn-toggle-<?php echo $customer_id; ?>" class="cmn-toggle cmn-toggle-round account_searchable_switch" data-customer_id="<?php echo $customer_id; ?>" type="checkbox" <?php echo($row1['is_searchable']==1?"checked":""); ?>>
                <label for="cmn-toggle-<?php echo $customer_id; ?>"></label>
            </div>
            <div class="pull-right amount_div1" style="display:none">
                <i class="fa fa-eye show_amount" title="Show"></i>
            </div>
            <div class="clearfix"></div>
        </div>
    </header>
    <!--header end-->



