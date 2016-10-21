<?php
$dbhost = 'localhost:3036';
$dbuser = 'root';
$dbpass = 'MYPASSWORD123';
$db_name = 'test_db';
$conn = mysql_connect($dbhost, $dbuser, $dbpass);

if(! $conn ) {
    die('Could not connect: ' . mysql_error());
}

echo 'Connected successfully';

$sql = 'CREATE Database '.$db_name;
$retval = mysql_query( $sql, $conn );

echo "Database test_db created successfully\n";
mysql_close($conn);
/////////////////////////////////////////////////////////////////////////////////////////////////////

$db = mysql_connect($dbhost, $dbuser, $dbpass, $db_name) or die(mysql_error());
mysql_select_db($db_name, $db) or die(mysql_error());

$table_sql="CREATE TABLE IF NOT EXISTS `info` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `tunnel_id` int(16) NOT NULL,
  `root_c` text NOT NULL,
  `cr_c` text NOT NULL,
  `k_c` text NOT NULL,
  `ip` text NOT NULL,
  `port` int(16) NOT NULL,
  `protocol` text NOT NULL,
  `extra` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
";
mysql_query($table_sql, $db);

$select_query=mysql_query("SELECT * FROM `info` WHERE `tunnel_id`=1");
if(mysql_num_rows($select_query)==0){
    $insert_sql="INSERT INTO `info` (`tunnel_id`, `root_c`, `cr_c`, `k_c`, `ip`, `port`, `protocol`, `extra`) VALUES
(1, 'root_c', 'cr_c', 'k_c', '1.1.1.1', 1111, 'TCP', 'extra');
";
    mysql_query($insert_sql, $db);
}

mysql_close($db);
?>