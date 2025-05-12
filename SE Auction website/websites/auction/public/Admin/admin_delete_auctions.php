<?php
include '../db_connection.php';
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ./log/login.php");
    exit;
}

// Get auction ID from query parameter
if (isset($_GET['id'])) {
    $auction_id = $_GET['id'];

    // Prepare and execute deletion query
    $stmt = $pdo->prepare("DELETE FROM auctions WHERE id = ?");
    $stmt->execute([$auction_id]);

    // Redirect to auctions page after deletion
    header("Location: admin_index.php?status=deleted");
    exit;
} else {
    // If no auction ID is provided, redirect with error
    header("Location: admin_index.php?error=no_id");
    exit;
}
?>
