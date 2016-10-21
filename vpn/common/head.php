<?php
//print_r($_SERVER);die;

ob_start();
$root_dir=dirname(dirname(__FILE__));
define("ROOT_DIR",$root_dir );
require_once ROOT_DIR.'/includes/config.php';
require_once ROOT_DIR.'/api/api_function.php';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard,vpn">
    <title>VPN</title>

    <link href="assets/plugins/bootstrap-select2/select2.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="assets/plugins/jquery-datatable/css/jquery.dataTables.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/datatables-responsive/css/datatables.responsive.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="assets/plugins/jquery-superbox/css/style.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="assets/plugins/bootstrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/bootstrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/animate.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/jquery.notify.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/bootstrap-editable.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/overlay-bootstrap.css" rel="stylesheet" type="text/css"/>
    <link href="webarch/css/webarch.css" rel="stylesheet" type="text/css"/>

    <link href="webarch/css/tree.css" rel="stylesheet" type="text/css"/>
    <link href="webarch/css/animate.css" rel="stylesheet" type="text/css"/>
    <link href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <!-- <link href="webarch/css/popbox.css" rel="stylesheet" type="text/css"/> -->
    <link href="assets/css/custom.css" rel="stylesheet" type="text/css"/>

    <link href="webarch/css/popover.min.css" rel="stylesheet" type="text/css"/>
    <link href="webarch/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css"/>

    <!-- crop css library -->
    <link href="assets/crop/dist/cropper.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/crop/dist/css/main.css" rel="stylesheet" type="text/css"/>
    <!-- end of crop css library -->

    <link href="assets/css/tabcontent.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/panel_layout.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/switch_style.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/pagenation.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/jquery.contextMenu.css" rel="stylesheet" type="text/css" />

    <link href="assets/css/style1.css" rel="stylesheet">
    <link href="assets/css/custom_select_box.css" rel="stylesheet">

    <script>
        var MAIN_SERVER_IP="<?php echo MAIN_SERVER_IP; ?>";
        var WEB_SOCKET_PORT="<?php echo WEB_SOCKET_PORT; ?>";
    </script>
    <script src="assets/plugins/jquery/jquery-1.11.3.min.js" type="text/javascript"></script>
    <script src="assets/js/pagination.js" type="text/javascript"></script>
    <script src="assets/js/jquery.contextMenu.js" type="text/javascript"></script>
    <script>
        var token="<?php echo(isset($_SESSION['token'])?$_SESSION['token']:''); ?>";

        function tunnel_template(data,target){
            console.log(data);
            var template_data="";
            var field_data={
                tunnel_ids:data
            };
            $.ajax({
                url : "request.php?request=get_tunnels_from_ids",
                type : "POST",
                data : field_data,
                success : function(resp){
                    $(target).html(resp);
                },
                error : function(){
                }
            });
            return true;
        }

        function sponsor_tunnel_template(data,target){
            console.log(data);
            var template_data="";
            var field_data={
                tunnel_ids:data
            };
            $.ajax({
                url : "request.php?request=get_sponsor_tunnels_from_ids",
                type : "POST",
                data : field_data,
                success : function(resp){
                    $(target).html(resp);
                },
                error : function(){
                }
            });
            return true;
        }
    </script>
</head>
