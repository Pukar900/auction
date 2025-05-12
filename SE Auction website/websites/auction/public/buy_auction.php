<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get the auction id from the query parameter
if (isset($_GET['auction_id']) && is_numeric($_GET['auction_id'])) {
    $auctionId = $_GET['auction_id'];

    // Fetch auction details
    $auctionStmt = $pdo->prepare("SELECT * FROM auctions WHERE id = ?");
    $auctionStmt->execute([$auctionId]);
    $auction = $auctionStmt->fetch(PDO::FETCH_ASSOC);

    if (!$auction) {
        echo "Auction not found.";
        exit;
    }

    // Check if the auction has been sold
    if ($auction['status'] == 'sold') {
        echo "This auction has already been sold.";
        exit;
    }

    // Fetch the highest bid for this auction
    $bidStmt = $pdo->prepare("SELECT * FROM bids WHERE auction_id = ? ORDER BY amount DESC LIMIT 1");
    $bidStmt->execute([$auctionId]);
    $highestBid = $bidStmt->fetch(PDO::FETCH_ASSOC);

    if (!$highestBid) {
        echo "No bids have been placed for this auction.";
        exit;
    }

    // Fetch the user's balance
    $userStmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $userStmt->execute([$_SESSION['user_id']]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
    $userBalance = $user['balance'] ?? 0.00;

    // Check if the user has enough balance to make the purchase
    if ($userBalance < $highestBid['amount']) {
        echo "You do not have enough balance to buy this item.";
        exit;
    }

    // Proceed with purchase
    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Insert into the purchases table
        $purchaseStmt = $pdo->prepare(
            "INSERT INTO purchases (user_id, auction_id, purchase_price, purchase_date) 
            VALUES (?, ?, ?, NOW())"
        );
        $purchaseStmt->execute([$_SESSION['user_id'], $auctionId, $highestBid['amount']]);

        // Update auction status to 'sold'
        $updateAuctionStmt = $pdo->prepare("UPDATE auctions SET status = 'sold' WHERE id = ?");
        $updateAuctionStmt->execute([$auctionId]);

        // Deduct the amount from the user's balance
        $newBalance = $userBalance - $highestBid['amount'];
        $updateUserBalanceStmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $updateUserBalanceStmt->execute([$newBalance, $_SESSION['user_id']]);

        // Delete the highest bid from the bids table
        $deleteBidStmt = $pdo->prepare("DELETE FROM bids WHERE id = ?");
        $deleteBidStmt->execute([$highestBid['id']]);

        // Commit the transaction
        $pdo->commit();

        echo "Purchase successful! You have successfully bought the auction item.";
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $pdo->rollBack();
        echo "An error occurred during the purchase process. Please try again. Error: " . $e->getMessage();
    }
} else {
    echo "Invalid auction ID.";
}
?>
