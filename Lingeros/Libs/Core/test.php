<?php
header('content-type:text/html; charset=utf8');

require('Db.class.php');
$config = [
	'dbtype'  => 'mysql',
	'dbhost'  => 'localhost',
	'dbuser'  => 'root',
	'dbpsw'   => 'root',
	'dbname'  => 'test',
	'dbcharset' => 'utf8',
];
 
Db::init($config);

$arr = [
	'name' => 'Mike',
	'message' => 'hello world',
];
$where = "name='Mike'";
$sql = 'select * from post'	;
// Db::insert('post', $arr);
// Db::update('post', $arr, $where);
$count = Db::getCount('post');


echo $count;
