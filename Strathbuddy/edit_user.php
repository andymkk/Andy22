<?php
session_start();
include("connect.php");

// Admin check
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? 0;

// Fetch user data
$sql = "SELECT id, name, email, course, yearofstudy, is_admin FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = strtolower(trim($_POST['email']));
    $yearofstudy = $_POST['yearofstudy'];
    $course = trim($_POST['course']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    
    $update_sql = "UPDATE users SET name = ?, email = ?, yearofstudy = ?, course = ?, is_admin = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssii", $name, $email, $yearofstudy, $course, $is_admin, $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "User updated successfully!";
        header("Location: admin.php");
        exit();
    } else {
        $error = "Error updating user: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Edit User</h1>
            <nav>
                <ul>
                    <li><a href="admin.php">Back to Admin Dashboard</a></li>
                </ul>
            </nav>
        </header>
        
        <section class="dashboard-section">
            <form method="POST">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label for="yearofstudy">Year of Study:</label>
                <select id="yearofstudy" name="yearofstudy" required>
                    <option value="1" <?php echo $user['yearofstudy'] == 1 ? 'selected' : ''; ?>>First Year</option>
                    <option value="2" <?php echo $user['yearofstudy'] == 2 ? 'selected' : ''; ?>>Second Year</option>
                    <option value="3" <?php echo $user['yearofstudy'] == 3 ? 'selected' : ''; ?>>Third Year</option>
                    <option value="4" <?php echo $user['yearofstudy'] == 4 ? 'selected' : ''; ?>>Fourth Year</option>
                    <option value="5" <?php echo $user['yearofstudy'] == 5 ? 'selected' : ''; ?>>Fifth Year</option>
                </select>

                <label for="course">Course:</label>
                <input type="text" id="course" name="course" value="<?php echo htmlspecialchars($user['course']); ?>" required>

                <label for="is_admin">Admin User:</label>
                <input type="checkbox" id="is_admin" name="is_admin" value="1" <?php echo $user['is_admin'] ? 'checked' : ''; ?>>

                <button type="submit">Update User</button>
            </form>
        </section>
    </div>
</body>
</html>