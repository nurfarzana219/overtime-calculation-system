<?php

session_start();
require_once "db/connection.php";

/* CHECK LOGIN */
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

/* ADMIN SHOULD NOT USE EMPLOYEE PROFILE */
if ($_SESSION["role"] == "admin") {
    header("Location: admin/dashboard.php");
    exit();
}

/* ONLY ALLOW POST REQUEST */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: profile.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");


/* VALIDATION */

if ($name == "" || $email == "") {
    die("Name and email are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Please enter a valid email address.");
}


/* CHECK IF EMAIL IS USED BY ANOTHER USER */

$check_sql = "SELECT id
              FROM users
              WHERE email = ?
              AND id != ?
              LIMIT 1";

$check_stmt = $conn->prepare($check_sql);

if (!$check_stmt) {
    die("Database error: " . $conn->error);
}

$check_stmt->bind_param(
    "si",
    $email,
    $user_id
);

$check_stmt->execute();

$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {

    $check_stmt->close();

    die("This email address is already being used by another account.");
}

$check_stmt->close();


/* UPDATE USER */

$sql = "UPDATE users
        SET name = ?, email = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param(
    "ssi",
    $name,
    $email,
    $user_id
);

if ($stmt->execute()) {

    /* UPDATE SESSION NAME */
    $_SESSION["name"] = $name;
    $_SESSION["email"] = $email;

    $stmt->close();

    header("Location: profile.php?updated=1");
    exit();

} else {

    $error = $stmt->error;

    $stmt->close();

    die("Unable to update profile: " . $error);
}

?>