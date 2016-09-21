<?php
//print_r($_SESSION);die;
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

