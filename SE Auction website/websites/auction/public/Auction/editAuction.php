<?php
include '../db_connection.php';
session_start();

// Check if the user is logged in and has permissions
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$auctionId = $_GET['id'];

// Fetch the auction to edit
$query = "SELECT * FROM auctions WHERE id = ? AND user_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$auctionId, $_SESSION['user_id']]);
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$auction) {
    // Auction not found or user doesn't have permission to edit
    echo "Auction not found or you don't have permission to edit it.";
    exit();
}

// Handle form submission for updating the auction
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $starting_price = $_POST['starting_price'];
    $auction_start_date = $_POST['auction_start_date'];
    $auction_end_date = $_POST['auction_end_date'];
    $category_id = $_POST['category_id'];
    
    // Prepare and execute the update query
    $updateQuery = "UPDATE auctions SET title = ?, description = ?, starting_price = ?, auction_start_date = ?, auction_end_date = ?, category_id = ? WHERE id = ?";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute([$title, $description, $starting_price, $auction_start_date, $auction_end_date, $category_id, $auctionId]);

    // Redirect to the updated auction page
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Auction</title>
</head>
<body>
    <h1>Edit Auction</h1>

    <form method="POST" action="">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($auction['title']); ?>" required><br><br>
        
        <label for="description">Description</label>
        <textarea id="description" name="description"><?= htmlspecialchars($auction['description']); ?></textarea><br><br>
        
        <label for="starting_price">Starting Price</label>
        <input type="number" step="0.01" id="starting_price" name="starting_price" value="<?= htmlspecialchars($auction['starting_price']); ?>" required><br><br>
        
        <label for="auction_start_date">Start Date</label>
        <input type="date" id="auction_start_date" name="auction_start_date" value="<?= htmlspecialchars($auction['auction_start_date']); ?>" required><br><br>
        
        <label for="auction_end_date">End Date</label>
        <input type="date" id="auction_end_date" name="auction_end_date" value="<?= htmlspecialchars($auction['auction_end_date']); ?>" required><br><br>

        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <?php
            // Fetch categories for the dropdown
            $categoryQuery = "SELECT * FROM category";
            $categoryStmt = $pdo->prepare($categoryQuery);
            $categoryStmt->execute();
            $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($categories as $category) {
                $selected = $category['category_id'] == $auction['category_id'] ? 'selected' : '';
                echo "<option value='{$category['category_id']}' $selected>" . htmlspecialchars($category['name']) . "</option>";
            }
            ?>
        </select><br><br>

        <button type="submit">Update Auction</button>
    </form>

</body>
</html>
