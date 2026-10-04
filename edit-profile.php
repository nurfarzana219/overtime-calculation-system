<?php

session_start();
require_once "db/connection.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

if ($_SESSION["role"] == "admin") {
    header("Location: admin/dashboard.php");
    exit();
}

$user_id = $_SESSION["user_id"];

/* GET EMPLOYEE INFORMATION */
/* GET EMPLOYEE INFORMATION */
$sql = "SELECT
            users.name,
            users.email,
            employees.employee_id,
            employees.department,
            employees.position,
            employees.salary,
            employees.date_joined,
            employees.manager,
            employees.phone
        FROM users
        JOIN employees
            ON users.id = employees.user_id
        WHERE users.id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$employee = $result->fetch_assoc();

$stmt->close();

if (!$employee) {
    die("Employee profile not found.");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Profile - Overtime System</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="figma-app">

    <!-- SIDEBAR -->
    <aside class="figma-sidebar">

        <div class="figma-logo">

            <div class="logo-shield">
                <span>⏱</span>
            </div>

            <div class="logo-text">
                OVERTIME SYSTEM
            </div>

        </div>


        <nav class="figma-nav">

            <a href="dashboard.php"
               class="figma-nav-item">
                Dashboard
            </a>

            <a href="apply-overtime.php"
               class="figma-nav-item">
                Apply Overtime
            </a>

            <a href="history.php"
               class="figma-nav-item">
                History
            </a>

            <a href="profile.php"
               class="figma-nav-item active">
                Profile
            </a>

            <a href="logout.php"
               class="figma-nav-item">
                Logout
            </a>

        </nav>

    </aside>


    <!-- MAIN -->
    <main class="figma-main">

        <!-- HEADER -->
        <header class="figma-header">

            <h1>WELCOME BACK!</h1>

            <div class="employee-portal">

                <span>EMPLOYEE PORTAL</span>

                <div class="user-avatar">
                    <?php
                    echo strtoupper(
                        substr($employee["name"], 0, 1)
                    );
                    ?>
                </div>

            </div>

        </header>


        <!-- EDIT PROFILE CONTENT -->
        <section class="edit-profile-content">

            <div class="edit-profile-heading">

                <div>
                    <h2>Edit Profile</h2>

                    <p>
                        Update your personal and professional information.
                    </p>
                </div>

                <a href="profile.php"
                   class="back-profile-btn">
                    ← Back to Profile
                </a>

            </div>


            <!-- PROFILE SUMMARY -->
            <div class="edit-profile-summary">

                <div class="edit-profile-avatar">

                    <?php
                    echo strtoupper(
                        substr($employee["name"], 0, 1)
                    );
                    ?>

                </div>

                <div>

                    <h3>
                        <?php
                        echo htmlspecialchars(
                            $employee["name"]
                        );
                        ?>
                    </h3>

                    <p class="edit-profile-position">

                        <?php
                        echo htmlspecialchars(
                            $employee["position"] ?? "Employee"
                        );
                        ?>

                    </p>

                    <p class="edit-profile-id">

                        Employee ID:
                        <?php
                        echo htmlspecialchars(
                            $employee["employee_id"]
                        );
                        ?>

                    </p>

                </div>

            </div>


            <!-- EDIT FORM -->
            <form
                method="POST"
                action="update-profile.php"
                class="edit-profile-form"
            >

                <div class="edit-profile-card">

                    <div class="edit-profile-card-title">

                        <div>
                            <h3>Personal Information</h3>

                            <p>
                                Edit your personal contact information.
                            </p>
                        </div>

                    </div>


                    <div class="edit-profile-divider"></div>


                    <div class="edit-profile-grid">


                        <!-- FULL NAME -->
                        <div class="edit-form-group">

                            <label>
                                Full Name
                            </label>

                            <input
                                type="text"
                                name="name"
                                value="<?php
                                echo htmlspecialchars(
                                    $employee["name"]
                                );
                                ?>"
                                required
                            >

                        </div>


                        <!-- EMAIL -->
                        <div class="edit-form-group">

                            <label>
                                Email Address
                            </label>

                            <input
                                type="email"
                                name="email"
                                value="<?php
                                echo htmlspecialchars(
                                    $employee["email"]
                                );
                                ?>"
                                required
                            >

                        </div>


                        <!-- PHONE -->
                        <div class="edit-form-group">

                            <label>
                                Phone Number
                            </label>

                            <input
                                type="text"
                                name="phone"
                                value="<?php
                                echo htmlspecialchars(
                                    $employee["phone"] ?? ""
                                );
                                ?>"
                                placeholder="Enter phone number"
                            >

                        </div>


                        <!-- EMPLOYEE ID -->
                        <div class="edit-form-group">

                            <label>
                                Employee ID
                            </label>

                            <input
                                type="text"
                                value="<?php
                                echo htmlspecialchars(
                                    $employee["employee_id"]
                                );
                                ?>"
                                readonly
                                class="readonly-field"
                            >

                            <small>
                                Employee ID cannot be changed.
                            </small>

                        </div>

                    </div>

                </div>

<!-- PROFESSIONAL INFORMATION -->

<div class="edit-profile-card">

    <div class="edit-profile-card-title">

        <div>
            <h3>Professional Information</h3>

            <p>
                Your employment information.
            </p>
        </div>

    </div>

    <div class="edit-profile-divider"></div>

    <div class="edit-profile-grid">

        <!-- DEPARTMENT -->

        <div class="edit-form-group">

            <label>
                Department
            </label>

            <input
                type="text"
                value="<?php
                echo htmlspecialchars(
                    $employee["department"]
                );
                ?>"
                readonly
                class="readonly-field"
            >

            <small>
                Contact admin to change department.
            </small>

        </div>


        <!-- POSITION -->

        <div class="edit-form-group">

            <label>
                Position
            </label>

            <input
                type="text"
                value="<?php
                echo htmlspecialchars(
                    $employee["position"]
                );
                ?>"
                readonly
                class="readonly-field"
            >

            <small>
                Contact admin to change position.
            </small>

        </div>


        <!-- SALARY -->

        <div class="edit-form-group">

            <label>
                Basic Salary
            </label>

            <input
                type="text"
                value="RM <?php
                echo number_format(
                    $employee["salary"],
                    2
                );
                ?>"
                readonly
                class="readonly-field"
            >

            <small>
                Salary can only be changed by admin.
            </small>

        </div>

    </div>

</div>


                    <div class="edit-profile-divider"></div>


                    <div class="edit-profile-grid">


                        <!-- DEPARTMENT -->
                        <div class="edit-form-group">

                            <label>
                                Department
                            </label>

                            <input
                                type="text"
                                name="department"
                                value="<?php
                                echo htmlspecialchars(
                                    $employee["department"] ?? ""
                                );
                                ?>"
                                placeholder="Enter department"
                            >

                        </div>


                        <!-- POSITION -->
                        <div class="edit-form-group">

                            <label>
                                Position
                            </label>

                            <input
                                type="text"
                                name="position"
                                value="<?php
                                echo htmlspecialchars(
                                    $employee["position"] ?? ""
                                );
                                ?>"
                                placeholder="Enter position"
                            >

                        </div>


                        <!-- DATE JOINED -->
                        <div class="edit-form-group">

                            <label>
                                Date Joined
                            </label>

                            <input
                                type="date"
                                name="date_joined"
                                value="<?php
                                echo htmlspecialchars(
                                    $employee["date_joined"] ?? ""
                                );
                                ?>"
                            >

                        </div>


                        <!-- WORK SCHEDULE -->
                        <div class="edit-form-group">

                            <label>
                                Work Schedule
                            </label>

                            <input
                                type="text"
                                name="work_schedule"
                                value="<?php
                                echo htmlspecialchars(
                                    $employee["work_schedule"] ?? ""
                                );
                                ?>"
                                placeholder="Example: Mon - Fri, 9 AM - 6 PM"
                            >

                        </div>


                        <!-- MANAGER -->
                        <div class="edit-form-group full-width">

                            <label>
                                Manager / Supervisor
                            </label>

                            <input
                                type="text"
                                name="manager"
                                value="<?php
                                echo htmlspecialchars(
                                    $employee["manager"] ?? ""
                                );
                                ?>"
                                placeholder="Enter manager or supervisor name"
                            >

                        </div>

                    </div>

                </div>


                <!-- BUTTONS -->
                <div class="edit-profile-actions">

                    <a href="profile.php"
                       class="cancel-profile-btn">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="save-profile-btn"
                    >
                        ✓ Save Changes
                    </button>

                </div>

            </form>

        </section>

    </main>

</div>

</body>

</html>