<?php
require_once 'vendor/autoload.php';

$dbConfig = require 'app/Config/Database.php';
$db = new \CodeIgniter\Database\MySQLi\Connection($dbConfig['default']);

$user = $db->table('users')->where('Email', 'admin@gmail.com')->get()->getRowArray();
var_dump($user);