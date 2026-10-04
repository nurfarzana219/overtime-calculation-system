<?php

session_start();
require_once "../db/connection.php";

$message = "";


/* =========================================
   ADMIN LOGIN
========================================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name == "" || $password == "") {

        $message = "Please enter your admin name and password.";

    } else {

        /* Only find users with ADMIN role */

        $sql = "SELECT *
                FROM users
                WHERE name = ?
                AND role = 'admin'
                LIMIT 1";

        $stmt = $conn->prepare($sql);

        if ($stmt) {

            $stmt->bind_param("s", $name);

            $stmt->execute();

            $result = $stmt->get_result();


            if ($result->num_rows == 1) {

                $admin = $result->fetch_assoc();


                /* CHECK HASHED PASSWORD */

                if (password_verify(
                    $password,
                    $admin["password"]
                )) {

                    $_SESSION["user_id"] = $admin["id"];
                    $_SESSION["name"] = $admin["name"];
                    $_SESSION["email"] = $admin["email"];
                    $_SESSION["role"] = $admin["role"];


                    /* GO TO ADMIN DASHBOARD */

                    header("Location: dashboard.php");
                    exit();

                } else {

                    $message = "Incorrect admin password.";

                }

            } else {

                $message = "Admin account not found.";

            }

            $stmt->close();

        } else {

            $message = "Database error: " . $conn->error;

        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Login - Overtime System</title>

    <link rel="stylesheet" href="../css/style.css">

</head>


<body class="admin-login-page">


<div class="admin-login-wrapper">


    <!-- =====================================
         LEFT SIDE
    ====================================== -->

    <div class="admin-login-left">


        <div class="admin-login-brand">


            <h1>
                OVERTIME<br>
                CALCULATION SYSTEM
            </h1>


            <div class="admin-login-logo">

                <span>⏱</span>

            </div>


            <p>
                Smart, Simple, Accurate.
            </p>


        </div>


    </div>



    <!-- =====================================
         RIGHT SIDE
    ====================================== -->

    <div class="admin-login-right">


        <div class="admin-login-container">


            <!-- HEADING -->

            <div class="admin-login-heading">


                <span class="admin-login-badge">
                    ADMIN PORTAL
                </span>


                <h2>
                    Admin Login
                </h2>


                <p>
                    Enter your administrator credentials
                    to continue.
                </p>


            </div>



            <!-- ERROR MESSAGE -->

            <?php if (!empty($message)): ?>


                <div class="login-error">

                    <?php
                    echo htmlspecialchars($message);
                    ?>

                </div>


            <?php endif; ?>



            <!-- LOGIN FORM -->

            <form method="POST"
                  action="">


                <!-- ADMIN NAME -->

                <div class="login-form-group">


                    <label for="name">
                        Admin Name
                    </label>


                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Enter admin name"
                        value="<?php
                        echo htmlspecialchars(
                            $_POST["name"] ?? ""
                        );
                        ?>"
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
                        placeholder="Enter admin password"
                        required
                    >


                </div>



                <!-- ADMIN LOGIN BUTTON -->

                <button
                    type="submit"
                    class="admin-login-submit"
                >

                    ADMIN LOGIN

                </button>


            </form>



            <!-- DIVIDER -->

            <div class="admin-login-divider">


                <span></span>


                <p>
                    or
                </p>


                <span></span>


            </div>



            <!-- BACK TO EMPLOYEE LOGIN -->

            <a href="../index.php"
               class="back-employee-login">

                ← BACK TO EMPLOYEE LOGIN

            </a>


        </div>


    </div>


</div>


</body>

</html>