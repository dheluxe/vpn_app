
<?php //@session_start();

$customer_id=$_REQUEST['customer_id'];

include_once 'common/session_check.php';
require_once 'includes/config.php';
require_once 'includes/connection.php';

$sql_user = $db->query("SELECT `users_data`.`id`, `users_data`.`user_email`, `users_data`.`name`, `users_data`.`date_added` FROM `users_data` INNER JOIN `customer_user_relations` ON `users_data`.`id`=`customer_user_relations`.`user_id` AND `customer_user_relations`.`user_token`='".$db->real_escape_string($_SESSION['token'])."'");
include_once 'common/head.php';
include_once 'common/header.php';
include_once 'common/sidebar.php';

//print_r($customer_id);
?>


<section id="main-content" style="padding-top: 160px;">

    <input id="tab5" type="radio" name="tabs" class="custom_res_tab" checked>
    <label for="tab5" class="custom_res_tab">Home</label>

    <input id="tab6" type="radio" name="tabs" class="custom_res_tab">
    <label for="tab6" class="custom_res_tab">Services</label>

    <div class="custom_res_tab">
        <div id="content5" class="hidden">
            <?php
            include 'home.php';
            ?>
        </div>
    </div>
</section>


<?php include_once 'common/script.php'; ?>

</body>
</html>

