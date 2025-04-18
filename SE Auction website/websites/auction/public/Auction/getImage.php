<?php
include '../db_connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare the query to fetch the image from the database
    $query = "SELECT image FROM auctions WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $auction = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($auction && $auction['image']) {
        // Set the appropriate header for the image content
        header("Content-Type: image/jpeg"); // Set the image MIME type (adjust as necessary)
        echo $auction['image']; // Output the binary image data
    } else {
        // Handle cases where image does not exist
        echo "Image not found.";
    }
} else {
    echo "Invalid request.";
}
?>
