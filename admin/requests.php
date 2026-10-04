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

/* Get all requests */
$sql = "SELECT
            overtime_records.id,
            overtime_records.date,
            overtime_records.start_time,
            overtime_records.end_time,
            overtime_records.hours,
            overtime_records.ot_rate,
            overtime_records.amount,
            overtime_records.reason,
            overtime_records.admin_remark,
            overtime_records.status,
            employees.employee_id,
            users.name
        FROM overtime_records
        JOIN employees
            ON overtime_records.employee_id = employees.id
        JOIN users
            ON employees.user_id = users.id
        ORDER BY overtime_records.date DESC,
                 overtime_records.id DESC";

$requests = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Overtime Requests - Admin</title>

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
       class="figma-nav-item">
        Dashboard
    </a>

    <a href="requests.php"
       class="figma-nav-item active">
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

        <header class="figma-header">

            <h1>ADMIN DASHBOARD</h1>

            <div class="employee-portal">

                <span>ADMIN PORTAL</span>

                <div class="user-avatar">
                    A
                </div>

            </div>

        </header>


        <section class="admin-content">

            <div class="admin-heading">

                <h2>Overtime Requests</h2>

                <p>
                    Review and manage employee overtime submissions.
                </p>

            </div>


            <div class="admin-recent-card">

                <div class="admin-section-title">

                    <span>▤</span>

                    All Overtime Requests

                </div>

                <div class="admin-divider"></div>


                <div class="admin-table-wrapper">

                    <table class="admin-request-table">

                        <thead>

                            <tr>

                                <th>Employee</th>
                                <th>Employee ID</th>
                                <th>Date</th>
                                <th>Hours</th>
                                <th>Amount</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Admin Remark</th>
                                <th>Action</th>

                            </tr>

                        </thead>

<tbody>

<?php if ($requests && $requests->num_rows > 0): ?>

    <?php while ($row = $requests->fetch_assoc()): ?>

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
                echo htmlspecialchars(
                    $row["employee_id"]
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

            <!-- EMPLOYEE REASON -->
            <td class="admin-reason">
                <?php
                echo htmlspecialchars(
                    $row["reason"]
                );
                ?>
            </td>

             <!-- STATUS -->
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

            <!-- ADMIN REMARK -->
            <td class="admin-remark">
                <?php
                echo !empty($row["admin_remark"])
                    ? htmlspecialchars($row["admin_remark"])
                    : "-";
                ?>
            </td>

        

            <!-- ACTION -->
            <td>

                <?php if ($row["status"] == "Pending"): ?>

                    <form
                        method="POST"
                        action="update-request.php"
                        class="request-action-form"
                    >

                        <input
                            type="hidden"
                            name="id"
                            value="<?php echo $row["id"]; ?>"
                        >

                        <textarea
                            name="admin_remark"
                            class="admin-remark-input"
                            placeholder="Enter admin remark..."
                            rows="3"
                            required
                        ></textarea>

                        <div class="request-actions">

                            <button
                                type="submit"
                                name="status"
                                value="Approved"
                                class="approve-btn"
                            >
                                Approve
                            </button>

                            <button
                                type="submit"
                                name="status"
                                value="Rejected"
                                class="reject-btn"
                            >
                                Reject
                            </button>

                        </div>

                    </form>

                <?php else: ?>

                    <span class="action-completed">
                        Completed
                    </span>

                <?php endif; ?>

            </td>

        </tr>

    <?php endwhile; ?>

<?php else: ?>

    <tr>

        <td colspan="9"
            class="admin-empty">

            No overtime requests found.

        </td>

    </tr>

<?php endif; ?>

</tbody>