<?php
include '../db_connection.php';
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ./log/login.php");
    exit;
}

// Get auction ID from query parameter
if (isset($_GET['id'])) {
    $auction_id = $_GET['id'];

    // Fetch auction details from database
    $stmt = $pdo->prepare("SELECT * FROM auctions WHERE id = ?");
    $stmt->execute([$auction_id]);
    $auction = $stmt->fetch(PDO::FETCH_ASSOC);

    // If auction not found, redirect to dashboard
    if (!$auction) {
        header("Location: admin_index.php?error=not_found");
        exit;
    }

    // Handle form submission to update auction details
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $starting_price = $_POST['starting_price'];
        $auction_start_date = $_POST['auction_start_date'];
        $auction_end_date = $_POST['auction_end_date'];
        $category_id = $_POST['category_id'];

        // Prepare and execute update query
        $stmt = $pdo->prepare("UPDATE auctions SET title = ?, description = ?, starting_price = ?, auction_start_date = ?, auction_end_date = ?, category_id = ? WHERE id = ?");
        $stmt->execute([$title, $description, $starting_price, $auction_start_date, $auction_end_date, $category_id, $auction_id]);

        // Redirect back to auctions page with success message
        header("Location: admin_index.php?status=updated");
        exit;
    }
} else {
    // If no auction ID is provided, redirect to dashboard
    header("Location: admin_index.php?error=no_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Auction</title>
</head>
<body>

<h2>Edit Auction</h2>
<form method="POST">
    <label for="title">Title:</label>
    <input type="text" name="title" value="<?= htmlspecialchars($auction['title']); ?>" required><br><br>

    <label for="description">Description:</label>
    <textarea name="description" required><?= htmlspecialchars($auction['description']); ?></textarea><br><br>

    <label for="starting_price">Starting Price:</label>
    <input type="number" name="starting_price" value="<?= $auction['starting_price']; ?>" step="0.01" required><br><br>

    <label for="auction_start_date">Auction Start Date:</label>
    <input type="date" name="auction_start_date" value="<?= $auction['auction_start_date']; ?>" required><br><br>

    <label for="auction_end_date">Auction End Date:</label>
    <input type="date" name="auction_end_date" value="<?= $auction['auction_end_date']; ?>" required><br><br>

    <label for="category_id">Category:</label>
    <select name="category_id" required>
        <?php
        // Fetch categories for the dropdown
        $categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($categories as $category) {
            echo '<option value="' . $category['category_id'] . '" ' . ($category['category_id'] == $auction['category_id'] ? 'selected' : '') . '>' . htmlspecialchars($category['name']) . '</option>';
        }
        ?>
    </select><br><br>

    <button type="submit">Update Auction</button>
</form>

</body>
</html>
