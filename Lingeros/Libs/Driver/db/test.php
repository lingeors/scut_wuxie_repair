<?php
header('content-type:text/html; charset=utf8');
define('DEBUG', true);
echo "<br/>";
require('PDO.class.php');
$config = [
	'dbtype'  => 'mysql',
	'dbhost'  => 'localhost',
	'dbuser'  => 'root',
	'dbpsw'   => 'root',
	'dbname'  => 'test',
	'dbcharset' => 'utf8',
];
 
$pdo = DbPdo::getInstance($config);
$arr = [
	'name' => 'Mike',
	'message' => 'bad boy',
];

$where = 'name="Mike"';
$count = $pdo->insert('post', $arr);
var_dump($count);