<?php
session_start();
include_once 'db_connection.php'; // Include your database connection file

// --- Simulated logged-in user ---
$user_id = $_SESSION['user_id'] ?? 1;

// --- Get auction ID from URL ---
$auction_id = isset($_GET['auction_id']) ? (int) $_GET['auction_id'] : 0;

// --- Get the auction details to fetch the starting bid ---
$auction_stmt = $pdo->prepare("SELECT title, starting_price FROM auctions WHERE id = ?");
$auction_stmt->execute([$auction_id]);
$auction = $auction_stmt->fetch(PDO::FETCH_ASSOC);

// Ensure the auction exists
if (!$auction) {
    die("Auction not found.");
}

$starting_bid = $auction['starting_price']; // Starting bid for the auction

// --- Get the user's current balance ---
$user_balance_stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
$user_balance_stmt->execute([$user_id]);
$user_balance = $user_balance_stmt->fetchColumn();

// --- Handle bid form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bid'])) {
    $bid_amount = floatval($_POST['bid']);

    // Check if the user has enough balance
    if ($user_balance < $bid_amount) {
        $error_message = "You do not have enough balance to place this bid. Your current balance is £" . number_format($user_balance, 2);
    } else {
        // Get previous highest bid
        $prev_stmt = $pdo->prepare("
            SELECT user_id, amount
            FROM bids
            WHERE auction_id = ?
            ORDER BY amount DESC
            LIMIT 1
        ");
        $prev_stmt->execute([$auction_id]);
        $prev_bid = $prev_stmt->fetch(PDO::FETCH_ASSOC);

        // Decide what the current price is (either highest bid or starting price)
        $current_price = $prev_bid ? $prev_bid['amount'] : $starting_bid;

        // Compare and insert only if new bid is higher
        if ($bid_amount > $current_price) {
            // Insert the new bid
            $stmt = $pdo->prepare("INSERT INTO bids (user_id, auction_id, amount) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $auction_id, $bid_amount]);

            // Deduct the bid amount from the user's balance
            $update_balance_stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $update_balance_stmt->execute([$bid_amount, $user_id]);

            $success_message = "Your bid of £" . number_format($bid_amount, 2) . " has been placed!";
        } else {
            $error_message = "Your bid must be higher than £" . number_format($current_price, 2);
        }
    }
}

// --- Get bid history for the auction ---
$bid_history_stmt = $pdo->prepare("SELECT b.amount, u.name, b.created_at FROM bids b JOIN users u ON b.user_id = u.id WHERE b.auction_id = ? ORDER BY b.created_at DESC");
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

    <h2>Place Your Bid on "<?= htmlspecialchars($auction['title']) ?>"</h2>
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
    <?php elseif (!empty($error_message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-inline mb-4">
        <input type="number" name="bid" class="form-control mr-2"
            placeholder="Enter your bid (min £<?= number_format($starting_bid, 2) ?>)" min="<?= $starting_bid ?>"
            required>
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
