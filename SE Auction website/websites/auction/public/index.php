<?php
include 'db_connection.php';
session_start();

// Flash message after login
$loginSuccess = '';
if (isset($_SESSION['login_success'])) {
    $loginSuccess = $_SESSION['login_success'];
    unset($_SESSION['login_success']);
}

// Check login state
$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

// Fetch all auctions
$query = "SELECT * FROM auctions ORDER BY auction_start_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <img src="./images/logo.jpg" alt="Logo">
            </div>
            <div class="search-container">
                <form action="search.php" method="GET">
                    <input type="text" name="query" placeholder="Search artworks..." required>
                    <button type="submit">🔍</button>
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
            : "Welcome to Antique Art Auction. Please <a href='./log/login.php'>log in</a> to participate in auctions.";
        ?>
    </div>

    <section class="auction-section">
        <?php foreach ($auctions as $auction): ?>
            <?php
            // Fetch highest bid for this auction
            $bidStmt = $pdo->prepare("SELECT MAX(amount) AS highest_bid FROM bids WHERE auction_id = ?");
            $bidStmt->execute([$auction['id']]);
            $bidResult = $bidStmt->fetch(PDO::FETCH_ASSOC);
            $highestBid = $bidResult['highest_bid'] ?? null;
            ?>
            <div class="item-card">
                <img src="../Auction/getImage.php?id=<?= htmlspecialchars($auction['id']); ?>" alt="<?= htmlspecialchars($auction['title']); ?>">
                <div class="item-content">
                    <h3><?= htmlspecialchars($auction['title']); ?></h3>
                    <p><?= htmlspecialchars($auction['description']); ?><br><br>
                        Starting bid: $<?= htmlspecialchars($auction['starting_price']); ?>
                    </p>
                    <p>Lot Number: <?= htmlspecialchars($auction['lot_number']); ?></p>

                    <!-- Highest Bid -->
                    <?php if ($highestBid !== null): ?>
                        <p><strong>Highest Bid:</strong> $<?= htmlspecialchars($highestBid); ?></p>
                    <?php else: ?>
                        <p><strong>No bids yet</strong></p>
                    <?php endif; ?>

                    <!-- Start Bid Button -->
                    <a href="start_bid.php?auction_id=<?= $auction['id']; ?>" class="btn btn-primary">Start Bid</a>
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
