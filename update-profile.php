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

/* GET FORM VALUES */
$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$date_joined = trim($_POST["date_joined"] ?? "");
$manager = trim($_POST["manager"] ?? "");

/* EMPTY DATE = NULL */
$date_joined = ($date_joined === "") ? null : $date_joined;


/* VALIDATION */

if ($name === "" || $email === "") {
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


/* UPDATE USER INFORMATION */

$user_sql = "UPDATE users
             SET name = ?,
                 email = ?
             WHERE id = ?";

$user_stmt = $conn->prepare($user_sql);

if (!$user_stmt) {
    die("Database error: " . $conn->error);
}

$user_stmt->bind_param(
    "ssi",
    $name,
    $email,
    $user_id
);

if (!$user_stmt->execute()) {

    $error = $user_stmt->error;

    $user_stmt->close();

    die("Unable to update user profile: " . $error);
}

$user_stmt->close();


/* UPDATE EMPLOYEE INFORMATION */

$employee_sql = "UPDATE employees
                 SET date_joined = ?,
                     manager = ?,
                     phone = ?
                 WHERE user_id = ?";

$employee_stmt = $conn->prepare($employee_sql);

if (!$employee_stmt) {
    die("Database error: " . $conn->error);
}

$employee_stmt->bind_param(
    "sssi",
    $date_joined,
    $manager,
    $phone,
    $user_id
);

if (!$employee_stmt->execute()) {

    $error = $employee_stmt->error;

    $employee_stmt->close();

    die("Unable to update employee profile: " . $error);
}

$employee_stmt->close();


/* UPDATE SESSION */

$_SESSION["name"] = $name;
$_SESSION["email"] = $email;


/* RETURN TO PROFILE */

header("Location: profile.php?updated=1");
exit();

?>