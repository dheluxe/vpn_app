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

$google = array(
    'auth_url'=>'https://accounts.google.com/o/oauth2/auth',
    'redirect_uri'=>'http://localhost/demovpn/app/request.php?request=google_auth',
    'client_id'=>'308090466622-gmsgsjesgeo50fn608spudica8c6ucts.apps.googleusercontent.com',
    'client_secret'=>'3mqrqEKRPjX7gOD04VC6zLYo',
    'api_key'=>'AIzaSyAsz_wyXWlarJ8jVil77fYMe8nECmv4BEY'
);