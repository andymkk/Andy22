<?php
session_start();
include 'connect.php';

// Enable debugging (Remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = strtolower(trim($_POST['email'])); // Convert email to lowercase
    $password = trim($_POST['password']); // Store password in plain text (NOT RECOMMENDED)
    $yearofstudy = $_POST['yearofstudy'];
    $course = trim($_POST['course']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // For initial admin setup only - remove after creating first admin
    $check_first_user = "SELECT COUNT(*) as count FROM users";
    $first_user_result = $conn->query($check_first_user);
    $first_user = $first_user_result->fetch_assoc();
    if ($first_user['count'] == 0) {
        $is_admin = 1;
    }

    // Check if email already exists
    $checkemail = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkemail);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Email address already exists!";
    } else {
        $sql = "INSERT INTO users (name, email, password, yearofstudy, course, is_admin) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $email, $password, $yearofstudy, $course, $is_admin);
        
        if ($stmt->execute()) {
            // Start session and log in user
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $name;
            $_SESSION['yearofstudy'] = $yearofstudy;
            $_SESSION['course'] = $course;
            $_SESSION['is_admin'] = $is_admin;

            // Redirect to appropriate dashboard
            if ($is_admin) {
                header("Location: admin.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strathbuddy - Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome to Strathbuddy</h1>
            <p>Your partner in academic success</p>
        </header>
        <section class="register-section">
            <h2>Register-Use your school email</h2>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form action="register.php" method="POST">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="yearofstudy">Year of Study:</label>
                <select id="yearofstudy" name="yearofstudy" required>
                    <option value="">Select Year</option>
                    <option value="1">First Year</option>
                    <option value="2">Second Year</option>
                    <option value="3">Third Year</option>
                    <option value="4">Fourth Year</option>
                    <option value="5">Fifth Year</option>
                </select>

                <label for="course">Course:</label>
                <input type="text" id="course" name="course" required>

                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <label for="is_admin">Admin User:</label>
                    <input type="checkbox" id="is_admin" name="is_admin" value="1">
                <?php endif; ?>

                <button type="submit" name="register">Register</button>
            </form>
            <p>Already have an account? <a href="index.php">Login here</a></p>
        </section>
    </div>
</body>
</html>