<?php
session_start();
include("connect.php");

// Admin check
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? 0;

// Delete user
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['message'] = "User deleted successfully!";
} else {
    $_SESSION['error'] = "Error deleting user: " . $conn->error;
}

$conn->close();
header("Location: admin.php");
exit();
?>