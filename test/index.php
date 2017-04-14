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

echo "<pre>";
$results = [];
$db->loadDriver(\Howl\DBManager::MYSQL_DRIVER, $config);
$db->table("items");
$db->columns(["name", "description", "category_id"]);
$db->values(["Nuevo item (Updated)","description...", "1"]);
$db->condition("id", "7");
$results = $db->delete();
var_dump($results);

