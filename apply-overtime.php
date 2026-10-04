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

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $date = $_POST["date"] ?? "";
    $start_time = $_POST["start_time"] ?? "";
    $end_time = $_POST["end_time"] ?? "";
    $reason = trim($_POST["reason"] ?? "");

    /* Validation */
    if (
        empty($date) ||
        empty($start_time) ||
        empty($end_time) ||
        empty($reason)
    ) {

        $message = "Please complete all required fields.";
        $message_type = "error";

    } else {

        /* Calculate working hours */
        $start = strtotime($start_time);
        $end = strtotime($end_time);

        /* Support overnight overtime */
        if ($end <= $start) {
            $end += 24 * 60 * 60;
        }

        $hours = ($end - $start) / 3600;

        /* Maximum overtime validation */
        if ($hours <= 0) {

            $message = "Invalid overtime duration.";
            $message_type = "error";

        } elseif ($hours > 12) {

            $message = "Overtime cannot exceed 12 hours.";
            $message_type = "error";

        } else {

            /*
             * Overtime calculation
             *
             * Hourly Rate = Monthly Salary / 26 / 8
             * OT Amount = Hourly Rate × OT Rate × OT Hours
             */

            $hourly_rate = $employee["salary"] / 26 / 8;

            $ot_rate = 1.5;

            $amount = $hourly_rate * $ot_rate * $hours;

            /* Insert request */
            $sql = "INSERT INTO overtime_records
                    (employee_id, date, start_time, end_time, hours, ot_rate, amount, reason)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "isssddds",
                $employee["id"],
                $date,
                $start_time,
                $end_time,
                $hours,
                $ot_rate,
                $amount,
                $reason
            );

            if ($stmt->execute()) {

                $message = "Overtime request submitted successfully.";
                $message_type = "success";

            } else {

                $message = "Unable to submit overtime request.";
                $message_type = "error";
            }
        }
    }
}

/* Get user information */
$sql = "SELECT name FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Apply Overtime - Overtime System</title>

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
               class="figma-nav-item active">
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
                    echo strtoupper(
                        substr($user["name"] ?? "E", 0, 1)
                    );
                    ?>

                </div>

            </div>

        </header>


        <!-- CONTENT -->

        <section class="apply-content">

            <div class="apply-page-heading">

                <h2>Apply Overtime</h2>

                <p>
                    Submit your completed extra hours for verification
                    and compensation approval.
                </p>

            </div>


            <?php if (!empty($message)): ?>

                <div class="apply-message <?php echo $message_type; ?>">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>


            <!-- FORM CARD -->

            <div class="overtime-form-card">

                <div class="form-title">

                    <span class="form-title-icon">▱</span>

                    <span>New Overtime Request</span>

                </div>


                <div class="form-divider"></div>


                <form method="POST"
                      action="apply-overtime.php">


                    <!-- DATE -->

                    <div class="figma-form-group">

                        <label for="date">
                            Date
                        </label>

                        <input
                            type="date"
                            id="date"
                            name="date"
                            required
                        >

                    </div>


                    <!-- TIME -->

                    <div class="time-row">

                        <div class="figma-form-group">

                            <label for="start_time">
                                Start Time
                            </label>

                            <input
                                type="time"
                                id="start_time"
                                name="start_time"
                                required
                            >

                        </div>


                        <div class="figma-form-group">

                            <label for="end_time">
                                End Time
                            </label>

                            <input
                                type="time"
                                id="end_time"
                                name="end_time"
                                required
                            >

                        </div>

                    </div>


                    <!-- DEPARTMENT -->

                    <div class="figma-form-group">

                        <label for="department">
                            Department
                        </label>

                        <select
                            id="department"
                            name="department"
                            disabled
                        >

                            <option>
                                <?php
                                echo htmlspecialchars(
                                    $employee["department"]
                                );
                                ?>
                            </option>

                        </select>

                    </div>


                    <!-- REASON -->

                    <div class="figma-form-group">

                        <label for="reason">
                            Reason / Description
                        </label>

                        <textarea
                            id="reason"
                            name="reason"
                            rows="4"
                            placeholder="Please outline the specific tasks completed during these overtime hours..."
                            required
                        ></textarea>

                    </div>


                    <!-- INFORMATION BOX -->

                    <div class="overtime-info">

                        <span class="info-icon">ⓘ</span>

                        <span>
                            Overtime requests are routed automatically
                            to your department head for secondary approval.
                        </span>

                    </div>


                    <!-- SUBMIT -->

                    <button
                        type="submit"
                        class="figma-submit-btn"
                    >
                        Submit Request
                    </button>

                </form>

            </div>

        </section>

    </main>

</div>

</body>
</html>