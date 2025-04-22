<?php
include '../db_connection.php';
session_start();

if (!isset($_GET['id'])) {
    echo "Auction ID is missing.";
    exit();
}

$auctionId = $_GET['id'];

// Fetch the auction to delete
$query = "SELECT * FROM auctions WHERE id = ? AND user_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$auctionId, $_SESSION['user_id']]);
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$auction) {
    // Auction not found or user doesn't have permission to delete
    echo "Auction not found or you don't have permission to delete it.";
    exit();
}

// Check if the user confirmed the deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle the deletion of the auction
    $deleteQuery = "DELETE FROM auctions WHERE id = ?";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->execute([$auctionId]);

    // Redirect to the home page after deletion
    header("Location: ../index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Deletion</title>
</head>
<body>
    <h1>Confirm Deletion</h1>
    <p>Are you sure you want to delete the auction titled <strong><?php echo htmlspecialchars($auction['title']); ?></strong>?</p>
    
    <!-- Confirmation form -->
    <form method="POST">
        <button type="submit" name="confirm_delete" value="yes">Yes, delete it</button>
        <a href="../index.php">Cancel</a>
    </form>
</body>
</html>
