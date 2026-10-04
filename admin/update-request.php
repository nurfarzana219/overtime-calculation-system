<?php

session_start();
require_once "../db/connection.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION["role"] != "admin") {
    header("Location: ../dashboard.php");
    exit();
}

$id = $_POST["id"] ?? 0;
$status = $_POST["status"] ?? "";
$admin_remark = trim($_POST["admin_remark"] ?? "");

$allowed_statuses = ["Approved", "Rejected"];

if (!in_array($status, $allowed_statuses)) {
    die("Invalid status.");
}

if ($id == 0) {
    die("Invalid request ID.");
}

if ($admin_remark == "") {
    die("Admin remark is required.");
}

$sql = "UPDATE overtime_records
        SET status = ?, admin_remark = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param(
    "ssi",
    $status,
    $admin_remark,
    $id
);

$stmt->execute();

$stmt->close();

header("Location: requests.php");
exit();

?>