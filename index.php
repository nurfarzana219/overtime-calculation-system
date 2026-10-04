<?php
session_start();

require_once "db/connection.php";

$message = "";

/* =========================
   USER LOGIN
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login_user"])) {

    $name = trim($_POST["name"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name == "" || $password == "") {

        $message = "Please enter your name and password.";

    } else {

        $sql = "SELECT * FROM users WHERE name = ? LIMIT 1";

        $stmt = $conn->prepare($sql);

        if ($stmt) {

            $stmt->bind_param("s", $name);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows == 1) {

                $user = $result->fetch_assoc();

                

                if (password_verify($password, $user["password"])) {

                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["name"] = $user["name"];
                    $_SESSION["email"] = $user["email"];
                    $_SESSION["role"] = $user["role"];

                    if ($user["role"] == "admin") {

                        header("Location: admin/dashboard.php");

                    } else {

                        header("Location: dashboard.php");

                    }

                    exit();

                } else {

                    $message = "Incorrect password.";

                }

            } else {

                $message = "User not found.";

            }

            $stmt->close();

        } else {

            $message = "Database error: " . $conn->error;

        }
    }
}


/* =========================
   ADMIN LOGIN
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["admin_login"])) {

    $sql = "SELECT * FROM users WHERE role = 'admin' LIMIT 1";

    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {

        $admin = $result->fetch_assoc();

        $_SESSION["user_id"] = $admin["id"];
        $_SESSION["name"] = $admin["name"];
        $_SESSION["email"] = $admin["email"];
        $_SESSION["role"] = "admin";

        header("Location: admin/dashboard.php");
        exit();

    } else {

        $message = "No admin account found.";

    }
}
?>






<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Overtime Calculation System</title>

    <!-- IMPORTANT -->
    <link rel="stylesheet" href="css/style.css">

</head>


<body>

<div class="login-page">


    <!-- =====================================
         LEFT SIDE
    ====================================== -->

    <div class="left-panel">


        <!-- SYSTEM TITLE -->

        <div class="system-title">

            OVERTIME CALCULATION SYSTEM

        </div>



<!-- OVERTIME SYSTEM LOGO -->
<div class="login-system-logo">

    <div class="login-logo-circle">
        <span class="login-stopwatch">⏱</span>
    </div>

</div>



        <!-- TAGLINE -->

        <div class="tagline">

            Smart, Simple, Accurate.

        </div>


    </div>



    <div class="login-form-container">

    <div class="login-heading">
        <h1>Log In</h1>
        <p>Welcome back. Please enter your account details.</p>
    </div>


    <?php if (!empty($message)): ?>

        <div class="login-error">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <form method="POST" action="" class="modern-login-form">

        <!-- NAME -->
        <div class="login-form-group">

            <label for="name">
                Your Name
            </label>

            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter your name"
                required
            >

        </div>


        <!-- PASSWORD -->
        <div class="login-form-group">

            <label for="password">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                required
            >

        </div>


        <!-- REMEMBER ME -->
        <div class="remember-row">

            <label class="remember-label">

                <input
                    type="checkbox"
                    name="remember"
                >

                <span>Remember Me</span>

            </label>

        </div>


        <!-- LOGIN -->
        <button
            type="submit"
            name="login_user"
            class="modern-login-btn"
        >
            LOG IN
        </button>

    </form>


    <!-- REGISTER -->
    <div class="register-section">

        <span>
            Don't have an account?
        </span>

        <a href="register.php">
            Create an account
        </a>

    </div>


    <!-- OR -->
    <div class="login-or">

        <span></span>

        <p>or</p>

        <span></span>

    </div>


    <a href="admin/login.php" class="modern-admin-btn">
    LOG IN AS ADMIN USER
    </a>

</div>