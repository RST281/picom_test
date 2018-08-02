<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

include_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/autoload.php');

$loader = new AutoLoad;
$registry = new RegData;
$logger = new Logger($registry);
$registry->set('Logger', $logger);
$db = new DB($registry);
$registry->set('DB', $db);
$core = new CORE($registry);
$registry->set('CORE', $core);