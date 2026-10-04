<?php

session_start();
require_once "db/connection.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $employee_id = trim($_POST["employee_id"]);
    $department = trim($_POST["department"]);
    $position = trim($_POST["position"]);
    $salary = $_POST["salary"];

    // Basic validation
    if (
        empty($name) ||
        empty($email) ||
        empty($password) ||
        empty($employee_id) ||
        empty($department) ||
        empty($position) ||
        empty($salary)
    ) {

        $message = "Please fill in all fields.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";
        $message_type = "error";

    } elseif (!is_numeric($salary) || $salary <= 0) {

        $message = "Please enter a valid salary.";
        $message_type = "error";

    } else {

        // Check whether email already exists
        $check_email = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_email);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $message = "This email is already registered.";
            $message_type = "error";

        } else {

            // Check whether employee ID already exists
            $check_employee = "SELECT id FROM employees WHERE employee_id = ?";
            $stmt = $conn->prepare($check_employee);
            $stmt->bind_param("s", $employee_id);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows > 0) {

                $message = "This employee ID is already registered.";
                $message_type = "error";

            } else {

                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Start transaction
                $conn->begin_transaction();

                try {

                    // Create user
                    $sql_user = "
                        INSERT INTO users
                        (name, email, password, role)
                        VALUES (?, ?, ?, 'employee')
                    ";

                    $stmt = $conn->prepare($sql_user);
                    $stmt->bind_param(
                        "sss",
                        $name,
                        $email,
                        $hashed_password
                    );

                    $stmt->execute();

                    // Get newly created user ID
                    $user_id = $conn->insert_id;

                    // Create employee record
                    $sql_employee = "
                        INSERT INTO employees
                        (user_id, employee_id, department, position, salary)
                        VALUES (?, ?, ?, ?, ?)
                    ";

                    $stmt = $conn->prepare($sql_employee);
                    $stmt->bind_param(
                        "isssd",
                        $user_id,
                        $employee_id,
                        $department,
                        $position,
                        $salary
                    );

                    $stmt->execute();

                    // Save both changes
                    $conn->commit();

                    $message = "Registration successful! You can now login.";
                    $message_type = "success";

                } catch (Exception $e) {

                    // Undo changes if something fails
                    $conn->rollback();

                    $message = "Registration failed. Please try again.";
                    $message_type = "error";
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - Overtime Calculation System</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        h1 {
            margin-bottom: 5px;
        }

        .subtitle {
            color: #777;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 13px;
            background: #ef6f61;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.9;
        }

        .message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .success {
            background: #e8f7ee;
            color: #207044;
        }

        .error {
            background: #fdecec;
            color: #a33a3a;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #2563eb;
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>Create Account</h1>

    <p class="subtitle">
        Register for the Overtime Calculation System
    </p>

    <?php if ($message != ""): ?>

        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <form method="POST">

        <div class="form-group">
            <label>Full Name</label>

            <input
                type="text"
                name="name"
                placeholder="Enter your full name"
                required
            >
        </div>


        <div class="form-group">
            <label>Email</label>

            <input
                type="email"
                name="email"
                placeholder="Enter your email"
                required
            >
        </div>


        <div class="form-group">
            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Create a password"
                required
            >
        </div>


        <div class="form-group">
            <label>Employee ID</label>

            <input
                type="text"
                name="employee_id"
                placeholder="Example: EMP-2026-002"
                required
            >
        </div>


        <div class="form-group">
            <label>Department</label>

            <input
                type="text"
                name="department"
                placeholder="Example: Engineering & Technology"
                required
            >
        </div>


        <div class="form-group">
            <label>Position</label>

            <input
                type="text"
                name="position"
                placeholder="Example: Software Engineer"
                required
            >
        </div>


        <div class="form-group">
            <label>Monthly Salary (RM)</label>

            <input
                type="number"
                name="salary"
                step="0.01"
                min="0"
                placeholder="Example: 2000.00"
                required
            >
        </div>


        <button type="submit">
            Create Account
        </button>

    </form>


    <div class="login-link">

        Already have an account?

        <a href="index.php">
            Login here
        </a>

    </div>

</div>

</body>

</html>