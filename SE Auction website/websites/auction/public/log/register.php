<?php
include_once '../db_connection.php';

$errorMessage = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirmPassword'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        $errorMessage = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    } elseif ($password !== $confirmPassword) {
        $errorMessage = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $errorMessage = "Password must be at least 6 characters.";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into the database
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashedPassword]);
            $successMessage = "Registration successful! You can now <a href='login.php'>login</a>.";
        } catch (PDOException $e) {
            $errorMessage = "Something went wrong. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Antique Art Auction</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>

<header>
    <h1>Antique Art Auction</h1>
    <p>Bid on timeless masterpieces</p>
</header>

<nav>
    <a href="../index.php">Home</a>
    <a href="../">Current Auctions</a>
    <a href="../">How It Works</a>
    <a href="../contact/contact.php">Contact</a>
</nav>

<div class="hero">
    Register for Your Account
</div>

<section class="register-section">
    <h2>Register</h2>

    <?php if ($errorMessage): ?>
        <div class="message-box error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <?php if ($successMessage): ?>
        <div class="message-box success"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($name ?? ''); ?>">

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <label for="confirmPassword">Confirm Password</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required>

        <button type="submit">Register</button>
    </form>

    <div class="login-link">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</section>

<footer>
    &copy; 2025 Antique Art Auction. All rights reserved.
</footer>

</body>
</html>
