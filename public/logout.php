<?php
require_once '../src/Database.php';
require_once '../src/Auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

$auth->logout();
header("Location: login.php");
?>
