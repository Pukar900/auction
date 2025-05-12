<?php
include '../db_connection.php';
session_start();

// Redirect non-admins
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ./log/login.php");
    exit;
}

// Fetch Stats
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalAuctions = $pdo->query("SELECT COUNT(*) FROM auctions")->fetchColumn();
$activeAuctions = $pdo->query("SELECT COUNT(*) FROM auctions WHERE auction_end_date >= CURDATE()")->fetchColumn();
$auctions = $pdo->query("
    SELECT a.*, 
           c.name AS category,
           CASE 
               WHEN a.auction_end_date >= CURRENT_TIMESTAMP THEN 'active'
               ELSE 'inactive'
           END AS status
    FROM auctions a 
    LEFT JOIN category c ON a.category_id = c.category_id 
    ORDER BY a.auction_start_date DESC
")->fetchAll(PDO::FETCH_ASSOC);


$totalBids = $pdo->query("SELECT COUNT(*) FROM bids")->fetchColumn();

// Fetch Users
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Auctions
$auctions = $pdo->query("
    SELECT a.*, c.name AS category 
    FROM auctions a 
    LEFT JOIN category c ON a.category_id = c.category_id 
    ORDER BY a.auction_start_date DESC
")->fetchAll(PDO::FETCH_ASSOC);


// Fetch Top Bids
$topBids = $pdo->query("
    SELECT b.*, u.name AS username, a.title 
    FROM bids b 
    JOIN users u ON b.user_id = u.id 
    JOIN auctions a ON b.auction_id = a.id 
    ORDER BY b.amount DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

function displayValue($value)
{
    return htmlspecialchars($value ?? 'Not Available');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Antique Art Auction</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f9f9f9;
        }

        header,
        footer {
            background: #444;
            color: white;
            padding: 1rem;
            text-align: center;
        }

        nav {
            background: #eee;
            padding: 1rem;
            text-align: center;
        }

        nav a {
            margin: 0 1rem;
            text-decoration: none;
            font-weight: bold;
            color: #333;
        }

        section {
            padding: 1rem;
            border-bottom: 1px solid #ccc;
            background: white;
            margin-bottom: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background: #f4f4f4;
        }

        .btn {
            padding: 6px 12px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
        }

        .btn-danger {
            background: red;
        }

        .stats div {
            margin: 10px 0;
            font-size: 1.1rem;
        }

        .section-title {
            font-size: 1.5rem;
            margin-top: 1rem;
            font-weight: bold;
        }

        .logout-btn {
            float: right;
            margin-top: -2.5rem;
        }
    </style>
</head>

<body>

    <header>
        <h1>Admin Dashboard</h1>
        <p>Welcome, Admin</p>
        <form method="POST" action="logout_admin.php" class="logout-btn">
            <button type="submit" class="btn">Logout</button>
        </form>
    </header>

    <nav>
        <a href="#overview">Overview</a>
        <a href="#users">Users</a>
        <a href="#auctions">Auctions</a>
        <a href="#bids">Top Bids</a>
    </nav>

    <main>

        <!-- Overview -->
        <section id="overview">
            <div class="section-title">📊 Overview</div>
            <div class="stats">
                <div>Total Users: <strong><?= $totalUsers ?></strong></div>
                <div>Total Auctions: <strong><?= $totalAuctions ?></strong></div>
                <div>Active Auctions: <strong><?= $activeAuctions ?></strong></div>
                <div>Total Bids Placed: <strong><?= $totalBids ?></strong></div>
            </div>
        </section>

        <!-- Users -->
        <section id="users">
            <div class="section-title">👥 User Management</div>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= displayValue($user['id']) ?></td>
                        <td><?= displayValue($user['name']) ?></td>
                        <td><?= displayValue($user['email']) ?></td>
                        <td><?= displayValue($user['created_at']) ?></td>
                        <td>
                            <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <!-- Auction Management -->
        <section id="auctions">
            <div class="section-title">🎨 Auction Management</div>
            <table>
                <tr>
                    <th>Lot</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Start Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php
                // Function to determine if auction is active or inactive
                function getAuctionStatus($endDate)
                {
                    $currentDate = new DateTime(); // Current date and time
                    $endDate = new DateTime($endDate); // Auction end date
                
                    // If auction end date is greater than or equal to current date, it's active
                    if ($endDate >= $currentDate) {
                        return 'active';
                    } else {
                        return 'inactive';
                    }
                }

                // Fetch and display the auctions
                foreach ($auctions as $auction):
                    ?>
                    <tr>
                        <td><?= $auction['lot_number']; ?></td>
                        <td><?= htmlspecialchars($auction['title']); ?></td>
                        <td><?= htmlspecialchars($auction['category']); ?></td>
                        <td>$<?= $auction['starting_price']; ?></td>
                        <td>
                            <?php
                            // Get auction status dynamically
                            echo ucfirst(getAuctionStatus($auction['auction_end_date']));
                            ?>
                        </td>
                        <td>
                            <a href="admin_edit_auctions.php?id=<?= $auction['id']; ?>" class="btn">Edit</a>
                            <a href="../Auction/deleteAuction.php?id=<?= $auction['id']; ?>" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to delete this auction?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>


        <!-- Top Bids -->
        <section id="bids">
            <div class="section-title">💰 Top Bids</div>
            <table>
                <tr>
                    <th>User</th>
                    <th>Auction</th>
                    <th>Bid Amount</th>
                    <th>Date</th>
                </tr>
                <?php foreach ($topBids as $bid): ?>
                    <tr>
                        <td><?= displayValue($bid['username']) ?></td>
                        <td><?= displayValue($bid['title']) ?></td>
                        <td>$<?= displayValue($bid['amount']) ?></td>
                        <td><?= displayValue($bid['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>

    </main>

    <footer>
        &copy; 2025 Antique Art Auction Admin Panel. All rights reserved.
    </footer>

</body>

</html>