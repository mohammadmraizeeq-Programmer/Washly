<?php
/**
 * Washly - Car Cleaning Management Platform
 * Configuration Template
 */

// --- 1. Database Configuration ---
$host = 'localhost';
$username = 'YOUR_DATABASE_USERNAME'; 
$password = 'YOUR_DATABASE_PASSWORD'; 
$database = 'washly';

// --- 2. MySQLi Connection ---
$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Define constants for use across the application
define('DB_HOST', $host);
define('DB_NAME', $database);
define('DB_USER', $username);
define('DB_PASS', $password);

// --- 3. Base URL Path Logic ---
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
$host_name = $_SERVER['HTTP_HOST'];
$current_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$parts = explode('/', trim($current_dir, '/'));
$base_path = '/' . ($parts[0] ?? 'Washly'); 

define('BASE_URL', $protocol . '://' . $host_name . $base_path);
?>