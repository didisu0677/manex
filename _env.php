<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once _DIR_ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env menggunakan metode terbaru dari phpdotenv v5.6
$dotenv = Dotenv::createUnsafeImmutable(_DIR_);
$dotenv->load();