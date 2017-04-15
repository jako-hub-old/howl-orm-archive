<?php
require_once '../Howl/HowlAutoload.php';
# We register the autoload function
\Howl\HowlAutoload::initAutoload();

$db = \Howl\DBManager::getInstance();
$config = [
    'host' => 'localhost',
    'user' => 'root',
    'database' => 'test',
    'password' => 'jko123',
];

require_once 'Items.php';

echo "<pre>";
$db->loadDriver(\Howl\DBManager::MYSQL_DRIVER, $config);
$results = \Test\Items::search()
                        ->rightJoin("categories")
                        ->onEquals("t1.id", "t.id")
                        ->and()
                        ->onEquals("t1.id", "t.id")
                        ->equals("t1.name", "other")
                        ->get();
var_dump($results);

