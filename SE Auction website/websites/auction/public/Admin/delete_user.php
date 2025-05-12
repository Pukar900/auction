<?php
session_start();
include_once '../db_connection.php'; // Your PDO connection setup

// Check if the user ID is provided via GET and is a number
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = (int)$_GET['id'];

    try {
        // Delete the user from the database
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        // Redirect back to the user management page
        header("Location: admin_index.php?message=User+deleted+successfully");
        exit;
    } catch (PDOException $e) {
        echo "Error deleting user: " . $e->getMessage();
    }
} else {
    // Invalid ID
    header("Location: admin_index.php?error=Invalid+User+ID");
    exit;
}
?>
