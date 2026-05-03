<?php
// index.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once 'config/database.php';
require_once 'core/Model.php';
require_once 'core/Controller.php';
require_once 'core/Router.php';

$router = require 'routes/web.php';
$router->dispatch();
?>