<?php
session_start();
include 'db_connection.php';

// Flash message after login
$loginSuccess = '';
if (isset($_SESSION['login_success'])) {
    $loginSuccess = $_SESSION['login_success'];
    unset($_SESSION['login_success']);
}

// Check login state
$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

// Check if search query is submitted
if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $search = trim($_GET['query']);
    $stmt = $pdo->prepare(
        "SELECT a.*, c.name AS category_name 
            FROM auctions a 
            LEFT JOIN category c ON a.category_id = c.category_id 
            WHERE a.title LIKE :search 
            OR a.description LIKE :search 
            OR c.name LIKE :search
            ORDER BY a.auction_start_date DESC"
    );
    $stmt->execute(['search' => "%$search%"]);
    $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Fetch all auctions normally
    $query = "SELECT a.*, c.name AS category_name 
                FROM auctions a 
                LEFT JOIN category c ON a.category_id = c.category_id 
                ORDER BY a.auction_start_date DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch user balance if logged in
if ($isLoggedIn) {
    $userStmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $userStmt->execute([$_SESSION['user_id']]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
    $userBalance = $user['balance'] ?? 0.00;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Antique Art Auction</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="index.css">
</head>

<body>

    <header>
        <div class="header-top">
            <div class="logo">
                <img src="./images/logo_f.jpg" alt="Logo">
            </div>
            <div class="search-container">
                <form action="index.php" method="GET">
                    <input type="text" name="query" placeholder="Search auctions..." required>
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>
        <h1>Antique Art Auction</h1>
        <p>Bid on timeless masterpieces</p>

        <?php if ($isLoggedIn): ?>
            <div class="auth-section">
                <a href="./Auction/addAuction.php" class="add-auction-btn">+ Add Auction</a>
            </div>
        <?php endif; ?>

        <!-- Add Balance Form -->
        <?php if ($isLoggedIn): ?>
            <form method="POST" action="add_balance.php" class="balance-form">
                <input type="number" name="amount" step="0.01" min="1" placeholder="Amount to add" required>
                <button type="submit">Add Balance</button>
            </form>

            <!-- Display Current Balance -->
            <div class="balance-display">
                <p><strong>Current Balance:</strong> $<?= number_format($userBalance, 2); ?></p>
            </div>
        <?php endif; ?>
    </header>

    <nav>
        <a href="index.php">Home</a>
        <a href="current_auctions.php">Current Auctions</a>
        <a href="how_it_works.php">How It Works</a>
        <a href="./contact/contact.php">Contact</a>
    </nav>

    <div class="hero">
        <img src="./images/hero_homepage_image.jpg" alt="Auction Banner">
        <div class="hero-text">Discover and Bid on Rare Artifacts</div>
    </div>

    <?php if (!empty($loginSuccess)): ?>
        <div class="alert">
            <?= htmlspecialchars($loginSuccess); ?>
        </div>
    <?php endif; ?>

    <div class="welcome-message">
        <?php
        echo $isLoggedIn
            ? "Welcome back, <strong>" . htmlspecialchars($username) . "</strong>! Enjoy bidding on antique treasures."
            : "Welcome to Antique Art Auction. Please <a href='./log/login.php'>log in</a> to participate in auctions."
            ?>
    </div>

    <section class="auction-section">
        <?php if (empty($auctions)): ?>
            <p style="text-align:center; margin-top: 20px;">No auctions found matching your search.</p>
        <?php endif; ?>

        <?php foreach ($auctions as $auction): ?>
            <?php
            // Fetch highest bid for this auction
            $bidStmt = $pdo->prepare("SELECT MAX(amount) AS highest_bid FROM bids WHERE auction_id = ?");
            $bidStmt->execute([$auction['id']]);
            $bidResult = $bidStmt->fetch(PDO::FETCH_ASSOC);
            $highestBid = $bidResult['highest_bid'] ?? null;
            ?>
            <div class="item-card">
                <img src="../Auction/getImage.php?id=<?= htmlspecialchars($auction['id']); ?>"
                    alt="<?= htmlspecialchars($auction['title']); ?>">
                <div class="item-content">
                    <h3><?= htmlspecialchars($auction['title']); ?></h3>
                    <p><?= htmlspecialchars($auction['description']); ?><br><br>
                        Starting bid: $<?= htmlspecialchars($auction['starting_price']); ?>
                    </p>
                    <p>Lot Number: <?= htmlspecialchars($auction['lot_number']); ?></p>
                    <p><strong>Category:</strong>
                        <?= htmlspecialchars($auction['category_name'] ?? 'Category not provided'); ?></p>

                    <!-- Highest Bid -->
                    <?php if ($highestBid !== null): ?>
                        <p><strong>Highest Bid:</strong> $<?= htmlspecialchars($highestBid); ?></p>
                    <?php else: ?>
                        <p><strong>No bids yet</strong></p>
                    <?php endif; ?>

                    <!-- Display 'Buy' Button only if the auction is not sold and the user did not add the auction -->
                    <?php if ($isLoggedIn && $auction['status'] != 'sold' && $auction['user_id'] != $_SESSION['user_id']): ?>
                        <a href="buy_auction.php?auction_id=<?= $auction['id']; ?>" class="btn btn-success">Buy</a><br><br>
                    <?php elseif ($auction['status'] == 'sold'): ?>
                        <p><strong>This item has been sold.</strong></p>
                    <?php endif; ?>

                    <!-- Show Start Bid button only if auction has started and the user is not the auction creator and auction is not sold -->
                    <?php if ($isLoggedIn && $auction['user_id'] != $_SESSION['user_id'] && $auction['status'] != 'sold'): ?>
                        <?php
                        $currentDate = date('Y-m-d'); // Today's date
                        $auctionStartDate = date('Y-m-d', strtotime($auction['auction_start_date']));
                        ?>
                        <?php if ($currentDate >= $auctionStartDate): ?>
                            <!-- Show Start Bid button only if auction has started -->
                            <a href="start_bid.php?auction_id=<?= $auction['id']; ?>" class="btn btn-primary">Start Bid</a><br><br>
                        <?php else: ?>
                            <!-- Optional: Show a "Coming Soon" message -->
                            <p style="color: gray;"><em>Auction starts on <?= htmlspecialchars($auctionStartDate); ?></em></p><br>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Edit/Delete buttons, only for the logged-in user who added the auction and the auction is not sold -->
                    <?php if ($isLoggedIn && $auction['user_id'] == $_SESSION['user_id'] && $auction['status'] != 'sold'): ?>
                        <a href="./Auction/editAuction.php?id=<?= $auction['id']; ?>" class="btn btn-secondary">Edit</a>
                        <a href="./Auction/deleteAuction.php?id=<?= $auction['id']; ?>" class="btn btn-danger">Delete</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <?php if ($isLoggedIn): ?>
        <div class="auth-section">
            <form method="POST" action="logout.php">
                <button class="logout-btn" type="submit">Logout</button>
            </form>
        </div>
    <?php endif; ?>

    <footer>
        &copy; 2025 Antique Art Auction. All rights reserved.
    </footer>

</body>

</html>