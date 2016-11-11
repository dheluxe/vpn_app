<?php
ob_start();
$load = sys_getloadavg();
$result="";
$result.= date("Y-m-d H:i:s") . ";";
$result.= "cpu load:" . $load[0] . ";";
$free = shell_exec('free -m');
$free = (string)trim($free);
$free_arr = explode("\n", $free);
$mem = explode(" ", $free_arr[1]);
$mem = array_filter($mem);
$mem = array_merge($mem);
$memory_usage = $mem[2]/$mem[1]*100;
$result.= "mem percentage:" . $memory_usage . ";";

///////////////////disk usage////////////////////////////////////////////////////////
$free = shell_exec('df -ah');
$free = (string)trim($free);
$free_arr = explode("\n", $free);
$disk = explode(" ", $free_arr[6]);
$disk = array_filter($disk);
$disk = array_merge($disk);
$disk_usage = $disk[4];
$result.= "Disk usage:" . $disk_usage . ";";

////////////////////band width///////////////////////////////////////////////////////
//shell_exec('apt-get update');
shell_exec('DEBIAN_FRONTEND=noninteractive apt-get -y install ifstat');
//$free=shell_exec("ifstat -t -i eth0 0.5");
$cmd = "ifstat -t -i eth0 0.5";
$descriptorspec = array(
    0 => array("pipe", "r"),   // stdin is a pipe that the child will read from
    1 => array("pipe", "w"),   // stdout is a pipe that the child will write to
    2 => array("pipe", "w")    // stderr is a pipe that the child will write to
);
flush();
$process = proc_open($cmd, $descriptorspec, $pipes, realpath('./'), array());
$s="";
if (is_resource($process)) {
    $s = fgets($pipes[1]);
    $s = fgets($pipes[1]);
    $s = fgets($pipes[1]);
    $s = fgets($pipes[1]);
}
flush();
$free = (string)trim($s);
$band_width = explode(" ", $free);
$band_width = array_filter($band_width);
$band_width = array_merge($band_width);
$band_width_in = $band_width[1];
$band_width_out=$band_width[2];
$result.= "Band width:" . $band_width_in . "KB/s(in),".$band_width_out."KB/s(out)";
ob_end_clean();
echo $result;
?>