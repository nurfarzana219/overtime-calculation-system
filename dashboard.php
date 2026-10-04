<?php
session_start();
require_once "db/connection.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];

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

$employee_id = $employee["id"];

/* Total OT Hours */
$sql = "SELECT COALESCE(SUM(hours), 0) AS total_hours
        FROM overtime_records
        WHERE employee_id = ?
        AND status = 'Approved'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$total_hours = $result->fetch_assoc()["total_hours"];

/* Total OT Payment - Approved only */
$sql = "SELECT COALESCE(SUM(amount), 0) AS total_payment
        FROM overtime_records
        WHERE employee_id = ?
        AND status = 'Approved'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$total_payment = $result->fetch_assoc()["total_payment"];

/* Pending Requests */
$sql = "SELECT COUNT(*) AS pending_requests
        FROM overtime_records
        WHERE employee_id = ?
        AND status = 'Pending'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$pending_requests = $result->fetch_assoc()["pending_requests"];

/* Approved Requests */
$sql = "SELECT COUNT(*) AS approved_requests
        FROM overtime_records
        WHERE employee_id = ?
        AND status = 'Approved'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$approved_requests = $result->fetch_assoc()["approved_requests"];

/* Get user name */
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

/* Recent overtime records */
/* All overtime records */
$sql = "SELECT date, start_time, end_time, hours, amount, status
        FROM overtime_records
        WHERE employee_id = ?
        ORDER BY date DESC, start_time DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$recent_records = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Overtime System</title>

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
       class="figma-nav-item active">
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
       class="figma-nav-item">
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
                    $name = $user["name"] ?? "Employee";
                    echo strtoupper(substr($name, 0, 1));
                    ?>
                </div>

            </div>

        </header>


        <!-- DASHBOARD CONTENT -->
        <section class="dashboard-content">


            <!-- STATISTICS -->
            <div class="dashboard-cards">

                <!-- Total OT Hours -->
                <div class="dashboard-card">

                    <div class="card-icon icon-hours">
                        ⌛
                    </div>

                    <h3>Total OT Hours</h3>

                    <div class="card-divider"></div>

                    <strong>
                        <?php echo number_format($total_hours, 0); ?> hrs
                    </strong>

                </div>


                <!-- Total OT Payment -->
                <div class="dashboard-card">

                    <div class="card-icon icon-payment">
                        💵
                    </div>

                    <h3>Total OT Payment</h3>

                    <div class="card-divider"></div>

                    <strong>
                        RM <?php echo number_format($total_payment, 2); ?>
                    </strong>

                </div>


                <!-- Pending Requests -->
                <div class="dashboard-card">

                    <div class="card-icon icon-pending">
                        📄
                    </div>

                    <h3>Pending Requests</h3>

                    <div class="card-divider"></div>

                    <strong>
                        <?php echo $pending_requests; ?>
                    </strong>

                </div>


                <!-- Approved Requests -->
                <div class="dashboard-card">

                    <div class="card-icon icon-approved">
                        ✓
                    </div>

                    <h3>Approved Requests</h3>

                    <div class="card-divider"></div>

                    <strong>
                        <?php echo $approved_requests; ?>
                    </strong>

                </div>

            </div>


            <!-- RECENT OVERTIME -->
            <div class="recent-section">

                <div class="recent-title">
                    <span class="recent-icon">⌛</span>
                    <span>Recent Overtime</span>
                </div>


                <div class="recent-table">

                    <div class="table-header">

                        <div>Date</div>
                        <div>Start Time</div>
                        <div>End Time</div>
                        <div>Hours</div>
                        <div>Amount</div>
                        <div>Status</div>

                    </div>


                    <?php if ($recent_records->num_rows > 0): ?>

                        <?php while ($row = $recent_records->fetch_assoc()): ?>

                            <div class="table-row">

                                <div>
                                    <?php
                                    echo date("d/m/Y", strtotime($row["date"]));
                                    ?>
                                </div>

                                <div>
                                    <?php
                                    echo date("g:i A", strtotime($row["start_time"]));
                                    ?>
                                </div>

                                <div>
                                    <?php
                                    echo date("g:i A", strtotime($row["end_time"]));
                                    ?>
                                </div>

                                <div>
                                    <?php echo number_format($row["hours"], 0); ?> hrs
                                </div>

                                <div>
                                    RM<?php echo number_format($row["amount"], 2); ?>
                                </div>

                                <div>
                                    <span class="dashboard-status <?php echo strtolower($row["status"]); ?>">
                                        <?php echo htmlspecialchars($row["status"]); ?>
                                    </span>
                                </div>

                            </div>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <div class="no-records">
                            No overtime records found.
                        </div>

                    <?php endif; ?>

                </div>


                <!-- APPLY BUTTON -->
                <a href="apply-overtime.php" class="dashboard-apply-btn">

                    <span class="plus-icon">+</span>

                    Apply Overtime

                </a>

            </div>

        </section>

    </main>

</div>

</body>
</html>