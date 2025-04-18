<?php
require './db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Antique Art Auction</title>
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
            padding: 20px 0;
            text-align: center;
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

        .hero {
            background-image: url('https://images.unsplash.com/photo-1580655957350-5f93f6f9de18?auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-shadow: 2px 2px 4px #000;
            font-size: 2rem;
        }

        .auction-section {
            padding: 40px 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: auto;
        }

        .item-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .item-card:hover {
            transform: scale(1.02);
        }

        .item-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .item-content {
            padding: 15px;
        }

        .item-content h3 {
            margin-top: 0;
        }

        footer {
            background-color: #4a4e69;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 40px;
        }
    </style>
</head>

<body>

    <header>
        <h1>Antique Art Auction</h1>
        <p>Bid on timeless masterpieces</p>
    </header>

    <nav>
        <a href="#">Home</a>
        <a href="#">Current Auctions</a>
        <a href="#">How It Works</a>
        <a href="#">Contact</a>
    </nav>

    <div class="hero">
        Discover Rare Sculptures & Paintings
    </div>

    <section class="auction-section">
        <div class="item-card">
            <img src="https://imgs.search.brave.com/ndZoVjva8xfEhJDZpGURjTmlyLSNm8ZtTX_SHkYUc9g/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9pMC53/cC5jb20vYmxvZy5h/cnRzcGVyLmNvbS93/cC1jb250ZW50L3Vw/bG9hZHMvMjAyMi8w/Ny9Qb2x5a2xlaXRv/cy5qcGVnP3Jlc2l6/ZT00MzAsNjQ0JnNz/bD0x"
                alt="Old Sculpture">
            <div class="item-content">
                <h3>18th Century Marble Sculpture</h3>
                <p>Starting Bid: $5,000</p>
            </div>
        </div>

        <div class="item-card">
            <img src="https://imgs.search.brave.com/PjvdlGCwiylwuh-lAgXulxET5WQVmAwroNMPeTwfHrA/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly91cGxv/YWQud2lraW1lZGlh/Lm9yZy93aWtpcGVk/aWEvY29tbW9ucy9m/L2ZhL0dpb3JnaW9u/ZSxfVGhlX3RlbXBl/c3QuanBn"
                alt="Classic Painting">
            <div class="item-content">
                <h3>Classic European Painting</h3>
                <p>Starting Bid: $3,200</p>
            </div>
        </div>

        <div class="item-card">
            <img src="https://imgs.search.brave.com/8Vr-zEUqfAnEG8mnQKrSI1GZFuRZ16sSv2Yj9nP4dOo/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9hLjFz/dGRpYnNjZG4uY29t/L2FuY2llbnQtc2lj/YW4tY2hpbXUtdHdp/bi1zcG91dGVkLXBv/dHRlcnktdmVzc2Vs/LXByZS1jb2x1bWJp/YW4tYXJ0aWZhY3Qt/Zm9yLXNhbGUvZl85/MjEwMi9mXzQzOTE3/MDQyMTc0MTIyNjQ0/NDg0OS9mXzQzOTE3/MDQyXzE3NDEyMjY0/NDU5MDJfYmdfcHJv/Y2Vzc2VkLmpwZz93/aWR0aD0yNDA"
                alt="Ancient Artifact">
            <div class="item-content">
                <h3>Ancient Artifact Vase</h3>
                <p>Starting Bid: $2,000</p>
            </div>
        </div>
    </section>

    <footer>
        &copy; 2025 Antique Art Auction. All rights reserved.
    </footer>

</body>

</html>