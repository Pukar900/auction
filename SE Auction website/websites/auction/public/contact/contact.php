<?php
session_start(); // Start session at the top
include_once '../db_connection.php';

$successMessage = $_SESSION['success'] ?? "";
unset($_SESSION['success']);
$errorMessage = "";

// Only allow form processing if user is logged in
if (isset($_SESSION['user_id'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            $errorMessage = "Please fill in all required fields.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "Invalid email format.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO contact (name, email, subject, message) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $subject, $message]);
                $_SESSION['success'] = "Thank you for contacting us!";
                header("Location: contact.php");
                exit;
            } catch (PDOException $e) {
                $errorMessage = "Something went wrong. Please try again later.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact - Antique Art Auction</title>
    <link rel="stylesheet" href="contact.css">
</head>
<body>

<header>
    <h1>Antique Art Auction</h1>
    <p>Bid on timeless masterpieces</p>
</header>

<nav>
    <a href="../index.php">Home</a>
    <a href="../current_auctions.php">Current Auctions</a>
    <a href="../how_it_works.php">How It Works</a>
   
</nav>

<div class="hero">
    Get In Touch With Us
</div>

<section class="contact-section">
    <h2>Contact Us</h2>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="message-box error">
            Please <a href="../log/login.php">log in</a> to access the contact form.
        </div>
    <?php else: ?>
        <?php if ($successMessage): ?>
            <div class="message-box success"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php elseif ($errorMessage): ?>
            <div class="message-box error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>

            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" required>

            <label for="message">Your Message</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button type="submit">Send Message</button>
        </form>
    <?php endif; ?>
</section>

<footer>
    &copy; 2025 Antique Art Auction. All rights reserved.
</footer>

</body>
</html>
