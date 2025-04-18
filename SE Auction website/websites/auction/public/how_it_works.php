<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>How It Works - Antique Art Auction</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="index.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            color: #333;
        }

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

        .how-it-works {
            padding: 3rem 2rem;
            max-width: 800px;
            margin: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.8s ease-out;
        }

        .how-it-works h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: #2c3e50;
            font-size: 2rem;
        }

        .step {
            background-color: #f9f9ff;
            margin-bottom: 1.5rem;
            padding: 1.2rem;
            border-left: 5px solid #6c63ff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, background-color 0.3s;
        }

        .step:hover {
            background-color: #eef1ff;
            transform: scale(1.02);
        }

        .step h3 {
            margin-top: 0;
            color: #333;
            font-size: 1.3rem;
        }

        .step p {
            margin: 0.5rem 0 0;
            line-height: 1.6;
        }

        .step a {
            color: #4a60e0;
            text-decoration: underline;
        }

        .final-note {
            margin-top: 2rem;
            font-size: 1.1rem;
            text-align: center;
            color: #444;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
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
        <a href="../contact/contact.php">Contact</a>
    </nav>

    <section class="how-it-works">
        <h2>How It Works</h2>

        <div class="step">
            <h3>🖼️ Step 1: Browse Artworks</h3>
            <p>Visit the <a href="current_auctions.php">Current Auctions</a> page to explore a curated collection of
                rare and antique artworks. Use the search bar or browse by categories to find pieces that interest you.
            </p>
        </div>

        <div class="step">
            <h3>📝 Step 2: Register an Account</h3>
            <p>Click on the <strong>Register</strong> button on the homepage or <a href="./log/register.php">register
                    here</a>. Fill in your details to create a secure account. You’ll need an email and password to get
                started.</p>
        </div>

        <div class="step">
            <h3>⚡ Step 3: Place a Bid</h3>
            <p>Once registered, go to any artwork listing and click on <strong>"Place Bid"</strong>. Enter your bid
                amount and submit. Make sure to keep an eye on the auction timer!</p>
        </div>

        <div class="step">
            <h3>🏆 Step 4: Win the Auction</h3>
            <p>If you’re the highest bidder when the auction ends, congratulations! You’ll receive a confirmation email
                and details about payment and shipping.</p>
        </div>

        <div class="step">
            <h3>💳 Step 5: Payment & Delivery</h3>
            <p>Pay securely via our payment gateway. Once payment is confirmed, your artwork will be packed and shipped
                safely to your address.</p>
        </div>

        <p class="final-note">Join our community of passionate collectors. Discover, bid, and own history!</p>
    </section>


</body>

</html>