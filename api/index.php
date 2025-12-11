<?php


// Fix for Vercel: Force script name to root index.php to prevent Laravel from stripping '/api' prefix
$_SERVER['SCRIPT_NAME'] = '/index.php';

require __DIR__ . '/../public/index.php';
