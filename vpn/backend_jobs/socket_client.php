<?php
define("_IP",    "198.211.127.72");
define("_PORT",  "8880");
$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_connect($sock, _IP, _PORT);
msg("socket connect to "._IP.":"._PORT."n");

while(1)
{
    msg("Enter command time or quit : ");
    $stdin = ereg_replace("n|r", "", read_data());
    $stdin = substr($stdin, 0, 4);
    if($stdin == "time" || $stdin == "quit")
    {
        msg("Input command : ".$stdin."\n");
    }else
    {
        msg("invalid command (not send) : ".$stdin."\n");
        continue;
    }
    socket_write($sock, $stdin);
    $sMsg  = socket_read($sock, 4096);
    if(substr($sMsg, 0, 4) == 'quit')
    {
        socket_close($sock);
        exit;
    }else
    {
        msg("recived data : ".$sMsg."\n");
    }
}

function read_data()
{
    $in = fopen("php://stdin", "r");
    $in_string = fgets($in, 255);
    fclose($in);
    return $in_string;
}

function msg($msg)
{
    echo "CLIENT >> ".$msg;
}
?>

?>
