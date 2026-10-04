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

/* GET EMPLOYEE ID FROM URL */

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id <= 0) {
    die("Invalid employee.");
}


/* GET EMPLOYEE INFORMATION */

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
        WHERE employees.id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$employee = $result->fetch_assoc();

$stmt->close();


if (!$employee) {
    die("Employee not found.");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Employee - Admin</title>

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

                <h2>Edit Employee</h2>

                <p>
                    <p>
                        Manage employee professional information and salary.
                    </p>
                </p>

            </div>


            <!-- EDIT CARD -->

            <div class="admin-employee-edit-card">

                <div class="admin-section-title">

                    <span>♙</span>

                    Employee Information

                </div>

                <div class="admin-divider"></div>


                <form
                    method="POST"
                    action="update-employee.php"
                    class="admin-employee-form"
                >


                    <!-- DATABASE EMPLOYEE ID -->

                    <input
                        type="hidden"
                        name="employee_record_id"
                        value="<?php echo $employee["id"]; ?>"
                    >


                    <!-- EMPLOYEE NAME -->

                    <div class="admin-form-group">

                        <label>
                            Employee Name
                        </label>

                        <input
                            type="text"
                            value="<?php
                            echo htmlspecialchars(
                                $employee["name"]
                            );
                            ?>"
                            readonly
                        >

                    </div>


                    <!-- EMPLOYEE ID -->

                    <div class="admin-form-group">

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
                        >

                    </div>


                    <!-- DEPARTMENT -->

                    <div class="admin-form-group">

                        <label>
                            Department
                        </label>

                        <input
    type="text"
    name="department"
    value="<?php
    echo htmlspecialchars(
        $employee["department"]
    );
    ?>"
    required
>

                    </div>


                    <!-- POSITION -->

                    <div class="admin-form-group">

                        <label>
                            Position
                        </label>

                        <input
    type="text"
    name="position"
    value="<?php
    echo htmlspecialchars(
        $employee["position"]
    );
    ?>"
    required
>

                    </div>


                    <!-- EMAIL -->

                    <div class="admin-form-group full-width">

                        <label>
                            Email Address
                        </label>

                        <input
                            type="email"
                            value="<?php
                            echo htmlspecialchars(
                                $employee["email"]
                            );
                            ?>"
                            readonly
                        >

                    </div>


                    <!-- SALARY -->

                    <div class="admin-form-group full-width">

                        <label for="salary">
                            Monthly Salary (RM)
                        </label>

                        <input
                            type="number"
                            id="salary"
                            name="salary"
                            min="0.01"
                            step="0.01"
                            value="<?php
                            echo htmlspecialchars(
                                $employee["salary"]
                            );
                            ?>"
                            placeholder="Example: 2500.00"
                            required
                        >

                        <small>
                            Salary is used to calculate this
                            employee's overtime payment.
                        </small>

                    </div>


                    <!-- BUTTONS -->

                    <div class="admin-employee-actions">

                        <a
                            href="employees.php"
                            class="admin-cancel-btn"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="admin-save-btn"
                        >
                            ✓ Save Changes
                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</div>

</body>
</html>