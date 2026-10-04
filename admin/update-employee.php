<?php

session_start();
require_once "../db/connection.php";

/* CHECK ADMIN */

if (!isset($_SESSION["user_id"])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION["role"] != "admin") {
    header("Location: ../dashboard.php");
    exit();
}


/* ONLY POST */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: employees.php");
    exit();
}


/* GET FORM VALUES */

$employee_record_id =
    (int) ($_POST["employee_record_id"] ?? 0);

$department =
    trim($_POST["department"] ?? "");

$position =
    trim($_POST["position"] ?? "");

$salary =
    (float) ($_POST["salary"] ?? 0);


/* VALIDATE */

if ($employee_record_id <= 0) {
    die("Invalid employee.");
}

if ($department === "") {
    die("Department is required.");
}

if ($position === "") {
    die("Position is required.");
}

if ($salary <= 0) {
    die("Salary must be greater than RM 0.00.");
}


/* UPDATE EMPLOYEE */

$sql = "UPDATE employees
        SET department = ?,
            position = ?,
            salary = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param(
    "ssdi",
    $department,
    $position,
    $salary,
    $employee_record_id
);


if ($stmt->execute()) {

    $stmt->close();

    header("Location: employees.php?updated=1");
    exit();
}

$error = $stmt->error;

$stmt->close();

die("Unable to update employee: " . $error);

?>