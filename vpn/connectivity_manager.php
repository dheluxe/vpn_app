<?php
@session_start();
require_once 'includes/config.php';
require_once 'includes/connection.php';
include_once 'api/api_function.php';

$clouds_data=array();
$token=$_SESSION['token'];
$sql="select * from `clouds_data` where `user_token`='".$token."' and `is_deleted`=0";
$res=$db->query($sql);
if($res->num_rows>0){
    while($row=$res->fetch_assoc()){
        $clouds_data[]=$row;
    }
}

$cloud_sql="SELECT * FROM `clouds_data` WHERE `email`='".$_SESSION['customer_email']."' AND `is_shared`=1";
$cloud=array();
$query=$db->query($cloud_sql);
if($query->num_rows==0){
    $cloud_insert_sql="INSERT INTO `clouds_data` (`cloud_name`,`email`,`is_shared`) VALUES ('shared','".$_SESSION['customer_email']."','1')";
    $cloud_insert_query=$db->query($cloud_insert_sql);
    $cloud_insert_id=$db->insert_id;
    $cloud=array('cloud_id'=>$cloud_insert_id,'cloud_name'=>"shared",'is_searchable'=>1);
}else{
    $cloud=$query->fetch_assoc();
}
$cloud['cloud_id']="-1";
$clouds_data[]=$cloud;
?>
<div class="content-block">
    <div class="filter-action-block">
        <div class="filter-block">
            <label for="cloud_selector" class="cloud_selector_label">
                Filter: &nbsp;
                <select id="cloud_selector" name="cloud_selector" class="cloud_select" data-token="<?php echo($token); ?>">
                    <?php
                    if(count($clouds_data)>0){
                        ?>
                        <option value="0">All clouds</option>
                    <?php
                    }
                    foreach($clouds_data as $data){
                        ?>
                        <option value="<?php echo($data['cloud_id']); ?>"><?php echo($data['cloud_name']); ?></option>
                    <?php
                    }
                    ?>
                </select>
            </label>
        </div>
        <div class="add-cloud-block">
            <a href="javascript:;" class="btn btn-rounded btn-primary" data-toggle="modal" data-target="#add_cloud"> Create cloud </a>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="filter-result-block">
        <?php
        foreach($clouds_data as $data){
            $cloud_id=$data['cloud_id'];
            $cloud_name=$data['cloud_name'];
            $is_searchable=$data['is_searchable'];
            cloud_tunnels($cloud_id,$cloud_name,$token);
        }
        //show_shared_tunnels();
        ?>
    </div>
</div>


