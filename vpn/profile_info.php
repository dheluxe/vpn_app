<?php
//////////////////////////////////////////////////////////////////////////////////////////////
$get_cusid=$_SESSION['user_id'];
$sql_user = $db->query("SELECT * FROM `customers_data` where `customer_id`='".$get_cusid."'");
$row = $sql_user->fetch_assoc(); ?>
<div class="page-content">
    <div class="content">
        <div class="row">
            <div class="alert alert-success" role="alert" style=<?php echo (isset($_SESSION['msg'])?"display: block;":"display: none;"); ?>><?php echo $_SESSION['msg'] ?></div>
            <div class="col-lg-6">
                <div class="alert alert-success" role="alert" id="manual_add_success_message" style="display: none;"></div>
                <div class="alert alert-danger" role="alert" id="manual_add_error_message" style="display: none;"></div>
                <div id="edtprofile-response-message"></div>
                <h4 class="pr_heading">Edit your profile</h4>
                <form id="profile_pic_change" method="post" enctype="multipart/form-data">
                    <div id="crop-avatar-user custom_row">
                        <div class="profile_image_div">
                            <img src="<?php echo(($row['profile_image']!='')?$row['profile_image']:ROOT_URL.'/assets/img/profiles/demo-user.jpg'); ?>" class="profile_image_viewers" id="profile_image_viewer">
                        </div>
                        <div>
                            <input id="profile_image" type="file" name="profile_image">
                        </div>
                    </div>

                    <input type="hidden" name="get_cus_id" id="get_cus_id" value="<?php echo $get_cusid;?>">
                    <div class="custom_row">
                        <label for="ex3">Name: </label>
                        <input class="form-control" id="name" type="text" value="<?php echo $row['name'];?>" name="name">
                    </div>
                    <div class="custom_row">
                        <label for="ex3">Display Name: </label>
                        <input class="form-control" id="display_name" type="text" value="<?php echo $row['display_name'];?>" name="display_name">
                    </div>
                    <div class="custom_row">
                        <label for="ex3">Phone number: </label>
                        <input class="form-control" id="phone" type="text" value="<?php echo $row['phone'];?>" pattern= "[7-9]{1}[0-9]{9}" name="phone">
                    </div>
                    <div class="custom_row">
                        <label for="ex3">Your mail address: </label>
                        <input class="form-control" id="email" type="text" value="<?php echo $row['email'];?>" name="email" disabled>
                    </div>
                    <div class="custom_row">
                        <label for="ex3">Your Recovery Email address: </label>
                        <input class="form-control" id="remail" type="text" value="<?php echo $row['remail'];?>" name="reemail">
                    </div>

                    <div class="custom_row">
                        <input type="submit" class="btn btn-primary btn-success btn-recovery" id="update-profile-btn" name="update-profile-btn" value="Save">
                    </div>

                </form>

            </div>
            <div class="col-lg-6">
                <h4 class="pr_heading">Change your password</h4>
                <div id="edtpass-response-message"></div>
                <form id="change_password_form">
                    <div class="col-lg-12 custom_row">
                        <label for="ex3">Old password: </label>
                        <input type="password" class="form-control" id="opassword" type="text" name="opassword">
                    </div>
                    <div class="col-lg-12 custom_row">
                        <label for="ex3">New password: </label>
                        <input type="password" class="form-control" id="password" type="text" value="" name="password">
                    </div>
                    <div class="col-lg-12 custom_row">
                        <label for="ex3">Confirm password: </label>
                        <input type="password" class="form-control" id="cfmPassword" type="text" name="cfmPassword" required>
                    </div>
                    <div class="col-lg-12 custom_row">
                        <button type="submit" name="submit" value="Submit" class="btn btn-primary btn-success btn-cpassword" href="javascript:void(0)" id="btn-editpass-profile"/>Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="addNewRow"></div>
</div>