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

/* Pending */
$sql = "SELECT COUNT(*) AS total
        FROM overtime_records
        WHERE status = 'Pending'";
$result = $conn->query($sql);
$pending = $result->fetch_assoc()["total"];

/* Approved */
$sql = "SELECT COUNT(*) AS total
        FROM overtime_records
        WHERE status = 'Approved'";
$result = $conn->query($sql);
$approved = $result->fetch_assoc()["total"];

/* Rejected */
$sql = "SELECT COUNT(*) AS total
        FROM overtime_records
        WHERE status = 'Rejected'";
$result = $conn->query($sql);
$rejected = $result->fetch_assoc()["total"];

/* Total */
$sql = "SELECT COUNT(*) AS total
        FROM overtime_records";
$result = $conn->query($sql);
$total = $result->fetch_assoc()["total"];

/* Recent requests */
$sql = "SELECT o.*, u.name
        FROM overtime_records o
        JOIN employees e ON o.employee_id = e.id
        JOIN users u ON e.user_id = u.id
        ORDER BY o.date DESC";

$recent = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard - Overtime System</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<div class="figma-app">

    <!-- SIDEBAR -->
<aside class="figma-sidebar admin-sidebar">

    <div class="figma-logo">

        <div class="logo-shield">
            <span>⏱</span>
        </div>

        <div class="logo-text">
            OVERTIME SYSTEM
        </div>

        <div class="admin-label">
            ADMIN
        </div>

    </div>

    <nav class="figma-nav">

        <a href="dashboard.php"
           class="figma-nav-item active">
            Dashboard
        </a>

        <a href="requests.php"
           class="figma-nav-item">
            Requests
        </a>

        <a href="employees.php"
           class="figma-nav-item">
            Employees
        </a>

        <a href="../logout.php"
           class="figma-nav-item">
            Logout
        </a>

    </nav>

</aside>


    <!-- MAIN -->

    <main class="figma-main">

        <!-- HEADER -->

        <header class="figma-header">

            <h1>ADMIN DASHBOARD</h1>

            <div class="employee-portal">

                <span>ADMIN PORTAL</span>

                <div class="user-avatar">
                    A
                </div>

            </div>

        </header>


        <!-- CONTENT -->

        <section class="admin-content">

            <div class="admin-heading">

                <h2>Dashboard Overview</h2>

                <p>
                    Monitor overtime requests and approval activities.
                </p>

            </div>


            <!-- STAT CARDS -->

            <div class="admin-cards">

                <div class="admin-card">

                    <div class="admin-card-icon pending-icon">
                        !
                    </div>

                    <p>Pending Requests</p>

                    <strong>
                        <?php echo $pending; ?>
                    </strong>

                </div>


                <div class="admin-card">

                    <div class="admin-card-icon approved-icon">
                        ✓
                    </div>

                    <p>Approved Requests</p>

                    <strong>
                        <?php echo $approved; ?>
                    </strong>

                </div>


                <div class="admin-card">

                    <div class="admin-card-icon rejected-icon">
                        ×
                    </div>

                    <p>Rejected Requests</p>

                    <strong>
                        <?php echo $rejected; ?>
                    </strong>

                </div>


                <div class="admin-card">

                    <div class="admin-card-icon total-icon">
                        #
                    </div>

                    <p>Total Requests</p>

                    <strong>
                        <?php echo $total; ?>
                    </strong>

                </div>

            </div>


            <!-- RECENT REQUESTS -->

            <div class="admin-recent-card">

                <div class="admin-section-title">

                    <span>▤</span>

                    Recent Overtime Requests

                </div>

                <div class="admin-divider"></div>


                <div class="admin-table-wrapper">

                    <table class="admin-table">

                        <thead>

                            <tr>

                                <th>Employee</th>
                                <th>Date</th>
                                <th>Hours</th>
                                <th>Amount</th>
                                <th>Status</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if ($recent && $recent->num_rows > 0): ?>

                            <?php while ($row = $recent->fetch_assoc()): ?>

                                <tr>

                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row["name"]
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo date(
                                            "d/m/Y",
                                            strtotime($row["date"])
                                        );
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo number_format(
                                            $row["hours"],
                                            0
                                        );
                                        ?> hrs
                                    </td>

                                    <td>
                                        RM <?php
                                        echo number_format(
                                            $row["amount"],
                                            2
                                        );
                                        ?>
                                    </td>

                                    <td>

                                        <span class="admin-status <?php
                                            echo strtolower(
                                                $row["status"]
                                            );
                                        ?>">

                                            <?php
                                            echo htmlspecialchars(
                                                $row["status"]
                                            );
                                            ?>

                                        </span>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="5"
                                    class="admin-empty">

                                    No overtime requests found.

                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>


                <a href="requests.php"
                   class="admin-view-btn">

                    View All Requests →

                </a>

            </div>

        </section>

    </main>

</div>

</body>
</html>