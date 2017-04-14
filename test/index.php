<?php
require_once '../Howl/HowlAutoload.php';
# We register the autoload function
\Howl\HowlAutoload::initAutoload();
$mysqlDriver = new \Howl\Drivers\MySql\MySqlDriver([
    'host' => 'localhost',
    'user' => 'root',
    'database' => 'test',
    'password' => 'jko123',
]);
echo "<pre>";
$mysqlDriver->query("SELECT * FROM items");

var_dump($results);

