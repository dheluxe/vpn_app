<?php
$is_local=true;
$config = array(
    'DB_HOST'=>'localhost',
    'DB_USER'=>'root',
    'DB_PASS'=>'kdcsev113$db',
    'DB_NAME'=>'newvpn',
    'SITE_STATUS' => true,
    'REG_STATUS' => 1
);
if($is_local){
    $config = array(
        'DB_HOST'=>'localhost',
        'DB_USER'=>'root',
        'DB_PASS'=>'',
        'DB_NAME'=>'newvpn',
        'SITE_STATUS' => true,
        'REG_STATUS' => 1
    );
}


if(!$is_local){
    if(!defined("ROOT_URL")){
        define("ROOT_URL","http://198.211.127.72/vpn");
    }
    if(!defined("MAIN_SERVER_IP")){
        define("MAIN_SERVER_IP","198.211.127.72");
    }
    if(!defined("WEB_SOCKET_PORT")){
        define("WEB_SOCKET_PORT","8880");
    }
    if(!defined("MAIN_SERVER_SSH_USER_NAME")){
        define("MAIN_SERVER_SSH_USER_NAME","root");
    }
    if(!defined("MAIN_SERVER_SSH_USER_PASS")){
        define("MAIN_SERVER_SSH_USER_PASS","kdcsev113@pass");
    }
    if(!defined("ROOT_PATH")){
        define("ROOT_PATH","/var/www/html/vpn");
    }
}else{
    if(!defined("ROOT_URL")){
        define("ROOT_URL","http://localhost/vpn_site/vpn");
    }
    if(!defined("MAIN_SERVER_IP")){
        define("MAIN_SERVER_IP","198.211.127.72");
    }
    if(!defined("WEB_SOCKET_PORT")){
        define("WEB_SOCKET_PORT","8880");
    }
    if(!defined("MAIN_SERVER_SSH_USER_NAME")){
        define("MAIN_SERVER_SSH_USER_NAME","root");
    }
    if(!defined("MAIN_SERVER_SSH_USER_PASS")){
        define("MAIN_SERVER_SSH_USER_PASS","kdcsev113@pass");
    }
    if(!defined("ROOT_PATH")){
        define("ROOT_PATH","c://xampp/htdocs/vpn_site/vpn");
    }
}

$google = array(
    'auth_url'=>'https://accounts.google.com/o/oauth2/auth',
    'redirect_uri'=>'http://localhost/demovpn/app/request.php?request=google_auth',
    'client_id'=>'308090466622-gmsgsjesgeo50fn608spudica8c6ucts.apps.googleusercontent.com',
    'client_secret'=>'3mqrqEKRPjX7gOD04VC6zLYo',
    'api_key'=>'AIzaSyAsz_wyXWlarJ8jVil77fYMe8nECmv4BEY'
);