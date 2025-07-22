<?php
session_start();
include("connect.php");

// Redirect to login if not authenticated
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Fetch logged-in user data
$email = $_SESSION['email'];
$sql = "SELECT name, course, yearofstudy FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "Error: User data not found.";
    exit();
}

// Store user details in session
$_SESSION['name'] = $user['name'];
$_SESSION['course'] = $user['course'];
$_SESSION['yearofstudy'] = $user['yearofstudy'];

// Fetch other students from the same course and year from the database
$sql_peers = "SELECT name, email FROM users WHERE course = ? AND yearofstudy = ? AND email != ?";
$stmt_peers = $conn->prepare($sql_peers);
$stmt_peers->bind_param("sss", $_SESSION['course'], $_SESSION['yearofstudy'], $email);
$stmt_peers->execute();
$result_peers = $stmt_peers->get_result();

// Store results in an array
$peers = [];
while ($row = $result_peers->fetch_assoc()) {
    $peers[] = $row;
}

$stmt->close();
$stmt_peers->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strathbuddy - Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome to Strathbuddy, <?php echo htmlspecialchars($_SESSION['name']); ?></h1>
            <p>Your academic dashboard</p>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li><a href="admin.php">Admin Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>
        
        <section class="dashboard-section">
            <h2>Your Information</h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
            <p><strong>Course:</strong> <?php echo htmlspecialchars($_SESSION['course']); ?></p>
            <p><strong>Year of Study:</strong> Year <?php echo htmlspecialchars($_SESSION['yearofstudy']); ?></p>
        </section>

        <section class="peers-section">
            <h2>Students in Your Course & Year</h2>
            <?php if (!empty($peers)): ?>
                <ul>
                    <?php foreach ($peers as $peer): ?>
                        <li><?php echo htmlspecialchars($peer['name']) . " (" . htmlspecialchars($peer['email']) . ")"; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No other students found in your course and year.</p>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>