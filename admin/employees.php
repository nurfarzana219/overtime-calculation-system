<?php

session_start();
require_once "../db/connection.php";

/* CHECK LOGIN */

if (!isset($_SESSION["user_id"])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION["role"] != "admin") {
    header("Location: ../dashboard.php");
    exit();
}


/* GET ALL EMPLOYEES */

$sql = "SELECT
            employees.id,
            employees.employee_id,
            employees.department,
            employees.position,
            employees.salary,
            users.name,
            users.email
        FROM employees
        JOIN users
            ON employees.user_id = users.id
        ORDER BY employees.id DESC";

$employees = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Employees - Admin</title>

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
               class="figma-nav-item">
                Requests
            </a>

            <a href="employees.php"
               class="figma-nav-item active">
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

                <h2>Employee Management</h2>

                <p>
                    View employee information and manage salary
                    used for overtime calculation.
                </p>

            </div>


            <?php if (
                isset($_GET["updated"]) &&
                $_GET["updated"] == "1"
            ): ?>

                <div class="admin-success-message">
                    ✓ Employee salary updated successfully.
                </div>

            <?php endif; ?>


            <div class="admin-recent-card">

                <div class="admin-section-title">

                    <span>♙</span>

                    Employees

                </div>

                <div class="admin-divider"></div>


                <div class="admin-table-wrapper">

                    <table class="admin-employee-table">

                        <thead>

                            <tr>

                                <th>Employee</th>
                                <th>Employee ID</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Email</th>
                                <th>Salary</th>
                                <th>Action</th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php if (
                            $employees &&
                            $employees->num_rows > 0
                        ): ?>

                            <?php while (
                                $row = $employees->fetch_assoc()
                            ): ?>

                                <tr>

                                    <td>

                                        <strong>
                                            <?php
                                            echo htmlspecialchars(
                                                $row["name"]
                                            );
                                            ?>
                                        </strong>

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
                                        echo htmlspecialchars(
                                            $row["department"]
                                        );
                                        ?>

                                    </td>


                                    <td>

                                        <?php
                                        echo htmlspecialchars(
                                            $row["position"]
                                        );
                                        ?>

                                    </td>


                                    <td>

                                        <?php
                                        echo htmlspecialchars(
                                            $row["email"]
                                        );
                                        ?>

                                    </td>


                                    <td>

                                        <?php if (
                                            $row["salary"] > 0
                                        ): ?>

                                            <strong>
                                                RM <?php
                                                echo number_format(
                                                    $row["salary"],
                                                    2
                                                );
                                                ?>
                                            </strong>

                                        <?php else: ?>

                                            <span class="salary-not-set">
                                                Not Assigned
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <td>

                                        <a
                                            href="edit-employee.php?id=<?php
                                            echo $row["id"];
                                            ?>"
                                            class="admin-edit-employee-btn"
                                        >
                                            Edit
                                        </a>

                                    </td>

                                </tr>

                            <?php endwhile; ?>


                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="7"
                                    class="admin-empty"
                                >
                                    No employees found.
                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

    </main>

</div>

</body>

</html>