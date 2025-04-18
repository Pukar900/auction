<?php
session_start();
include_once 'db_connection.php'; // Include your database connection file

// --- Simulated logged-in user ---
$user_id = $_SESSION['user_id'] ?? 1;

// --- Get auction ID from URL ---
$auction_id = isset($_GET['auction_id']) ? (int)$_GET['auction_id'] : 0;

// --- Handle bid form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bid'])) {
    $bid_amount = floatval($_POST['bid']);

    $stmt = $pdo->prepare("INSERT INTO bids (user_id, auction_id, amount) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $auction_id, $bid_amount]);
}

// --- Get highest bid for the auction ---
$highest_bid_stmt = $pdo->prepare("
    SELECT b.amount, u.name
    FROM bids b
    JOIN users u ON b.user_id = u.id
    WHERE b.auction_id = ?
    ORDER BY b.amount DESC
    LIMIT 1
");
$highest_bid_stmt->execute([$auction_id]);
$highest_bid = $highest_bid_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Place a Bid</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body class="container mt-5">

    <h2>Place Your Bid</h2>
    <form method="POST" class="form-inline mb-4">
        <input type="text" name="bid" class="form-control mr-2" placeholder="Enter your bid" required>
        <button type="submit" class="btn btn-success">Submit Bid</button>
    </form>

    <h4>Highest Bid:</h4>
    <?php if ($highest_bid): ?>
        <div class="alert alert-info">
            £<?= htmlspecialchars($highest_bid['amount']) ?> by <?= htmlspecialchars($highest_bid['name']) ?>
        </div>
    <?php else: ?>
        <p>No bids yet.</p>
    <?php endif; ?>

    <a href="index.php" class="btn btn-secondary mt-3">Back to Auctions</a>
</body>
</html>

