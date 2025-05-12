<?php
// current_auctions.php

require_once 'db_connection.php';
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Fetch auctions ordered by auction_date ascending (ending soon first)
$stmt = $pdo->prepare("SELECT * FROM auctions ORDER BY auction_start_date ASC");
$stmt->execute();
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Current Auctions - Antique Art Auction</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            color: #333;
        }

        /* Header Section */
        header {
            background-color: #4a4e69;
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: -75px;
            flex-wrap: wrap;
        }

        .logo img {
            height: 60px;
            border-radius: 10px;
        }

        .search-container {
            display: flex;
            align-items: center;
        }

        .search-container form {
            display: flex;
            align-items: center;
        }

        .search-container input[type="text"] {
            padding: 8px 12px;
            border-radius: 5px 0 0 5px;
            border: none;
            outline: none;
            font-size: 1rem;
            width: 200px;
        }

        .search-container button {
            padding: 8px 12px;
            border: none;
            background-color: #9a8c98;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            border-radius: 0 5px 5px 0;
        }

        .search-container button:hover {
            background-color: #c9ada7;
        }

        /* Auth Section */
        .auth-section {
            margin-top: 1rem;
        }

        .add-auction-btn {
            background-color: #6a5acd;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 5px;
        }

        .add-auction-btn:hover {
            background-color: #5a4abf;
        }

        /* Navigation */
        nav {
            background-color: #22223b;
            text-align: center;
            padding: 10px 0;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }

        /* nav a:hover {
            color: #ffa;
        } */

        

        /* Auction Grid Section */
        .auction-section {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
            padding: 2rem;
            animation: fadeIn 1s ease-in;
        }

        .item-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .item-card:hover {
            transform: scale(1.03);
        }

        .item-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .item-content {
            padding: 1rem;
        }

        .item-content h3 {
            margin-bottom: 0.5rem;
            color: #4b0082;
        }

        .item-content p {
            font-size: 0.95rem;
            color: #555;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .header-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-container {
                margin-top: 1rem;
                width: 100%;
            }

            .search-container input[type="text"] {
                width: 100%;
                border-radius: 5px;
                margin-bottom: 0.5rem;
            }

            .search-container button {
                width: 100%;
                border-radius: 5px;
            }

            .hero-text {
                font-size: 1.2rem;
                padding: 0.5rem 1rem;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="header-top">
            <div class="logo">
                <img src="./images/logo_f.jpg" alt="Logo">
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

    </header>

    <nav>
        <a href="index.php">Home</a>
        <a href="current_auctions.php">Current Auctions</a>
        <a href="how_it_works.php">How It Works</a>
        <a href="./contact/contact.php">Contact</a>
    </nav>

    <section class="auction-section">
        <?php foreach ($auctions as $auction): ?>
            <div class="item-card">
                <img src="./Auction/getImage.php?id=<?php echo htmlspecialchars($auction['id']); ?>"
                    alt="<?php echo htmlspecialchars($auction['title']); ?>">
                <div class="item-content">
                    <h3><?php echo htmlspecialchars($auction['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($auction['description'])); ?><br>
                        <strong>Starting bid:</strong> $<?php echo htmlspecialchars($auction['starting_price']); ?><br>
                        <small><strong>Ends on:</strong>
                            <?php echo date('M d, Y H:i', strtotime($auction['auction_start_date'])); ?></small>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

</body>

</html>