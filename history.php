<?php
session_start();
require_once "db/connection.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];

/* Get user */
$sql = "SELECT name FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

/* Get employee */
$sql = "SELECT * FROM employees WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$employee_result = $stmt->get_result();
$employee = $employee_result->fetch_assoc();

if (!$employee) {
    die("Employee record not found.");
}

/* Get overtime history */
$sql = "SELECT date, start_time, end_time, hours, ot_rate, amount,
               reason, status, admin_remark
        FROM overtime_records
        WHERE employee_id = ?
        ORDER BY date DESC, start_time DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee["id"]);
$stmt->execute();
$records = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>History - Overtime System</title>

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
               class="figma-nav-item active">
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
                        substr($user["name"] ?? "E", 0, 1)
                    );
                    ?>

                </div>

            </div>

        </header>


        <!-- HISTORY CONTENT -->

        <section class="history-content">

            <div class="history-heading">

                <h2>Overtime History</h2>

                <p>
                    View all submitted overtime requests and their approval status.
                </p>

            </div>


            <div class="history-card">

                <div class="history-card-title">

                    <span class="history-title-icon">▤</span>

                    <span>Overtime Records</span>

                </div>

                <div class="history-divider"></div>


                <div class="history-table-wrapper">

                    <table class="figma-history-table">

    <thead>

        <tr>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Hours</th>
            <th>OT Rate</th>
            <th>Amount</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Admin Remark</th>
        </tr>

    </thead>


    <tbody>

    <?php if ($records && $records->num_rows > 0): ?>

        <?php while ($row = $records->fetch_assoc()): ?>

            <tr>

                <!-- DATE -->
                <td>
                    <?php
                    echo date(
                        "d/m/Y",
                        strtotime($row["date"])
                    );
                    ?>
                </td>


                <!-- START TIME -->
                <td>
                    <?php
                    echo date(
                        "g:i A",
                        strtotime($row["start_time"])
                    );
                    ?>
                </td>


                <!-- END TIME -->
                <td>
                    <?php
                    echo date(
                        "g:i A",
                        strtotime($row["end_time"])
                    );
                    ?>
                </td>


                <!-- HOURS -->
                <td>
                    <?php
                    echo number_format(
                        $row["hours"],
                        0
                    );
                    ?> hrs
                </td>


                <!-- OT RATE -->
                <td>
                    <?php
                    echo number_format(
                        $row["ot_rate"],
                        1
                    );
                    ?>×
                </td>


                <!-- AMOUNT -->
                <td>
                    RM <?php
                    echo number_format(
                        $row["amount"],
                        2
                    );
                    ?>
                </td>


                <!-- EMPLOYEE REASON -->
                <td class="reason-cell">
                    <?php
                    echo htmlspecialchars(
                        $row["reason"]
                    );
                    ?>
                </td>


                <!-- STATUS -->
                <td>

                    <span class="history-status <?php
                        echo strtolower($row["status"]);
                    ?>">

                        <?php
                        echo htmlspecialchars(
                            $row["status"]
                        );
                        ?>

                    </span>

                </td>


                <!-- ADMIN REMARK -->
                <td class="admin-remark-cell">

                    <?php if (!empty($row["admin_remark"])): ?>

                        <?php
                        echo htmlspecialchars(
                            $row["admin_remark"]
                        );
                        ?>


                    <?php elseif ($row["status"] == "Pending"): ?>

                        <span class="remark-pending">
                            Waiting for admin review
                        </span>


                    <?php else: ?>

                        <span class="remark-empty">
                            No remark provided
                        </span>

                    <?php endif; ?>

                </td>

            </tr>

        <?php endwhile; ?>


    <?php else: ?>

        <tr>

            <td colspan="9"
                class="history-empty">

                No overtime records found.

            </td>

        </tr>

    <?php endif; ?>

    </tbody>

</table>