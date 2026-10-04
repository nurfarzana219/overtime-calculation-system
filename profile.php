<?php
session_start();
require_once "db/connection.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];

/* Get user information */
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User record not found.");
}

/* Get employee information */
$sql = "SELECT * FROM employees WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if (!$employee) {
    die("Employee record not found.");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Profile - Overtime System</title>

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


    <!-- MAIN AREA -->

    <main class="figma-main">

        <!-- TOP HEADER -->

        <header class="figma-header">

            <h1>WELCOME BACK!</h1>

            <div class="employee-portal">

                <span>EMPLOYEE PORTAL</span>

                <div class="user-avatar">

                    <?php
                    echo strtoupper(
                        substr($user["name"], 0, 1)
                    );
                    ?>

                </div>

            </div>

        </header>


        <!-- PROFILE CONTENT -->

        <section class="profile-content">

            <!-- PAGE HEADING -->

            <!-- PAGE HEADING -->

<div class="profile-heading-row">

    <div class="profile-heading">

        <h2>My Profile</h2>

        <p>
            View and manage your professional identity
            and personal records.
        </p>

    </div>


    <a href="edit-profile.php"
       class="edit-profile-btn">
        ✎ Edit Profile
    </a>

</div>


<!-- SUCCESS MESSAGE -->

<?php if (isset($_GET["updated"]) && $_GET["updated"] == "1"): ?>

    <div class="profile-success-message">
        ✓ Profile updated successfully.
    </div>

<?php endif; ?>


            <!-- EMPLOYEE SUMMARY -->

            <div class="profile-summary-card">

                <div class="profile-avatar-large">
                    <?php
                    echo strtoupper(
                        substr($user["name"], 0, 1)
                    );
                    ?>
                </div>


                <div class="profile-summary-details">

                    <h3>
                        <?php
                        echo htmlspecialchars($user["name"]);
                        ?>
                    </h3>

                    <p class="profile-position">
                        <?php
                        echo htmlspecialchars(
                            $employee["position"]
                        );
                        ?>
                    </p>

                    <p class="profile-employee-id">
                        Employee ID:
                        <?php
                        echo htmlspecialchars(
                            $employee["employee_id"]
                        );
                        ?>
                    </p>

                </div>

            </div>


            <!-- INFORMATION CARDS -->

            <div class="profile-info-grid">


                <!-- EMPLOYEE INFORMATION -->

                <div class="profile-info-card">

                    <h3>Employee Information</h3>

                    <div class="profile-card-divider"></div>


                    <div class="profile-fields-grid">

                        <div class="profile-field">

                            <label>Full Name</label>

                            <div class="profile-value">
                                <?php
                                echo htmlspecialchars(
                                    $user["name"]
                                );
                                ?>
                            </div>

                        </div>


                        <div class="profile-field">

                            <label>Email Address</label>

                            <div class="profile-value">
                                <?php
                                echo htmlspecialchars(
                                    $user["email"]
                                );
                                ?>
                            </div>

                        </div>


                        <div class="profile-field">

                            <label>Department</label>

                            <div class="profile-value">
                                <?php
                                echo htmlspecialchars(
                                    $employee["department"]
                                );
                                ?>
                            </div>

                        </div>


                        <div class="profile-field">

                            <label>Position</label>

                            <div class="profile-value">
                                <?php
                                echo htmlspecialchars(
                                    $employee["position"]
                                );
                                ?>
                            </div>

                        </div>


                        <div class="profile-field">

                            <label>Employee ID</label>

                            <div class="profile-value">
                                <?php
                                echo htmlspecialchars(
                                    $employee["employee_id"]
                                );
                                ?>
                            </div>

                        </div>


                        <div class="profile-field">

                            <label>Phone Number</label>

                            <div class="profile-value">
    <?php
    if (!empty($employee["phone"])) {
        echo htmlspecialchars($employee["phone"]);
    } else {
        echo "Not provided";
    }
    ?>
</div>

                        </div>

                    </div>

                </div>


                <!-- WORK & COMPENSATION -->

                <div class="profile-info-card compensation-card">

                    <h3>Work &amp; Compensation</h3>

                    <div class="profile-card-divider"></div>

<div class="compensation-row">

    <span>Date Joined</span>

    <strong>
        <?php
        if (!empty($employee["date_joined"])) {
            echo date(
                "d/m/Y",
                strtotime($employee["date_joined"])
            );
        } else {
            echo "Not provided";
        }
        ?>
    </strong>

</div>


                    <div class="compensation-row">

                        <span>Work Schedule</span>

                        <strong>
                            Mon - Fri, 9 AM - 6 PM
                        </strong>

                    </div>


          <div class="compensation-row">

    <span>Manager/Supervisor</span>

    <strong>
        <?php
        if (!empty($employee["manager"])) {
            echo htmlspecialchars($employee["manager"]);
        } else {
            echo "Not provided";
        }
        ?>
    </strong>

</div>


                    <div class="compensation-row">

                        <span>Overtime Rate</span>

                        <strong class="overtime-rate">
                            1.5 × hourly rate
                        </strong>

                    </div>

                </div>

            </div>

        </section>

    </main>

</div>

</body>
</html>