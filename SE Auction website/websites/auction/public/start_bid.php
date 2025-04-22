<?php
session_start();
include_once 'db_connection.php'; // Include your database connection file

// --- Simulated logged-in user ---
$user_id = $_SESSION['user_id'] ?? 1;

// --- Get auction ID from URL ---
$auction_id = isset($_GET['auction_id']) ? (int) $_GET['auction_id'] : 0;

// --- Handle bid form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bid'])) {
    $bid_amount = floatval($_POST['bid']);

    // Get the current highest bid
    $current_highest_stmt = $pdo->prepare("
        SELECT MAX(amount) AS max_amount
        FROM bids
        WHERE auction_id = ?
    ");
    $current_highest_stmt->execute([$auction_id]);
    $current_highest = $current_highest_stmt->fetchColumn();

    // Compare and insert only if new bid is higher
    if ($bid_amount > $current_highest) {
        $stmt = $pdo->prepare("INSERT INTO bids (user_id, auction_id, amount) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $auction_id, $bid_amount]);
        $success_message = "Your bid of £" . number_format($bid_amount, 2) . " has been placed!";
    } else {
        $error_message = "Your bid must be higher than the current highest bid of £" . number_format($current_highest, 2);
    }
}


// --- Get bid history for the auction ---
$bid_history_stmt = $pdo->prepare("
    SELECT b.amount, u.name, b.created_at
    FROM bids b
    JOIN users u ON b.user_id = u.id
    WHERE b.auction_id = ?
    ORDER BY b.created_at DESC
");
$bid_history_stmt->execute([$auction_id]);
$bid_history = $bid_history_stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <?php if (!empty($success_message)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
<?php elseif (!empty($error_message)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
<?php endif; ?>

    <form method="POST" class="form-inline mb-4">
        <input type="text" name="bid" class="form-control mr-2" placeholder="Enter your bid" required>
        <button type="submit" class="btn btn-success">Submit Bid</button>
    </form>

    <h4>Bid History:</h4>
    <?php if ($bid_history): ?>
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Bidder</th>
                    <th>Amount (£)</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bid_history as $bid): ?>
                    <tr>
                        <td><?= htmlspecialchars($bid['name']) ?></td>
                        <td><?= htmlspecialchars($bid['amount']) ?></td>
                        <td><?= htmlspecialchars($bid['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No bids placed yet.</p>
    <?php endif; ?>


    <a href="index.php" class="btn btn-secondary mt-3">Back to Auctions</a>
</body>

</html>