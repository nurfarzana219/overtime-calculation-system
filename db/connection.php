<?php

$host = getenv("MYSQLHOST") ?: "localhost";
$port = getenv("MYSQLPORT") ?: "3306";
$username = getenv("MYSQLUSER") ?: "root";
$password = getenv("MYSQLPASSWORD") ?: "";
$database = getenv("MYSQLDATABASE") ?: getenv("MYSQL_DATABASE") ?: "overtime_calculation_system";

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database,
    (int)$port
);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection failed. Please try again later.");
}

$conn->set_charset("utf8mb4");
?>