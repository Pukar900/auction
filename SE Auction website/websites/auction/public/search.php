<?php
include 'db_connection.php';
session_start();

// Fetch categories from the database for search filter
$categoryStmt = $pdo->prepare("SELECT * FROM category ORDER BY name ASC");
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle search form submission
$searchTitle = isset($_POST['search_title']) ? $_POST['search_title'] : '';
$searchCategory = isset($_POST['category']) ? $_POST['category'] : '';
$searchStartDate = isset($_POST['auction_start_date']) ? $_POST['auction_start_date'] : '';
$searchEndDate = isset($_POST['auction_end_date']) ? $_POST['auction_end_date'] : '';

// Build SQL query for search
$sql = "SELECT * FROM auctions WHERE 1";

// Append filters to SQL query based on user input
if (!empty($searchTitle)) {
    $sql .= " AND title LIKE :search_title";
}

if (!empty($searchCategory)) {
    $sql .= " AND category_id = :category";
}

if (!empty($searchStartDate)) {
    $sql .= " AND auction_start_date >= :auction_start_date";
}

if (!empty($searchEndDate)) {
    $sql .= " AND auction_end_date <= :auction_end_date";
}

$sql .= " ORDER BY auction_start_date DESC";  // Optional sorting by start date

// Prepare the search query
$stmt = $pdo->prepare($sql);

// Bind parameters
if (!empty($searchTitle)) {
    $stmt->bindValue(':search_title', "%" . $searchTitle . "%");
}
if (!empty($searchCategory)) {
    $stmt->bindValue(':category', $searchCategory, PDO::PARAM_INT);
}
if (!empty($searchStartDate)) {
    $stmt->bindValue(':auction_start_date', $searchStartDate);
}
if (!empty($searchEndDate)) {
    $stmt->bindValue(':auction_end_date', $searchEndDate);
}

// Execute the query
$stmt->execute();

// Fetch all the results
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auction Search</title>
    <link rel="stylesheet" href="index.css"> <!-- Add the stylesheet -->
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
        }

        nav {
            background-color: #22223b;
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
        }

        nav a:hover {
            background-color: #4a4e69;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: #22223b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #4a4e69;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        /* Footer */
        footer {
            background-color: #4a4e69;
            color: white;
            text-align: center;
            padding: 6px;
            position: fixed;
            width: 100%;
            bottom: 0;
            left: 0;
            z-index: 1000;
            /* Ensure the footer stays above other content */
        }

        .container {
            padding-bottom: 50px;
            /* Adjust the bottom padding to make space for the fixed footer */
        }
    </style>
</head>

<body>

    <!-- Navigation Bar (Header) -->
    <header>
        <div class="header-top">
            <div class="logo">
                <img src="./images/logo.jpg" alt="Logo">
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
    <div class="container">
        <h1>Auction Search</h1>

        <!-- Search Form -->
        <form method="POST" action="search.php">
            <label for="search_title">Title:</label>
            <input type="text" id="search_title" name="search_title"
                value="<?php echo htmlspecialchars($searchTitle); ?>">

            <label for="category">Category:</label>
            <select name="category" id="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['category_id']; ?>" <?php echo ($searchCategory == $category['category_id']) ? 'selected' : ''; ?>>
                        <?php echo $category['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="auction_start_date">Start Date:</label>
            <input type="date" id="auction_start_date" name="auction_start_date"
                value="<?php echo htmlspecialchars($searchStartDate); ?>">

            <label for="auction_end_date">End Date:</label>
            <input type="date" id="auction_end_date" name="auction_end_date"
                value="<?php echo htmlspecialchars($searchEndDate); ?>">

            <button type="submit">Search</button>
        </form>

        <h2>Search Results</h2>

        <?php if ($auctions): ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Starting Price</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Lot Number</th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($auctions as $auction): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($auction['title']); ?></td>
                            <td><?php echo htmlspecialchars($auction['category_id']); // Fetch category name if needed ?></td>
                            <td><?php echo number_format($auction['starting_price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($auction['auction_start_date']); ?></td>
                            <td><?php echo htmlspecialchars($auction['auction_end_date']); ?></td>
                            <td><?php echo htmlspecialchars($auction['lot_number']); ?></td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No auctions found matching your search criteria.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 Auction Site. All rights reserved.</p>
    </footer>

</body>

</html>