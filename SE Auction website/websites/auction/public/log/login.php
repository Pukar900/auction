<?php
session_start(); // Start the session
include_once '../db_connection.php';

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $errorMessage = "Please fill in all required fields.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['name']; // 'name' not 'username'

                $_SESSION['login_success'] = "Successfully logged in! Welcome back, {$user['name']}.";

                header("Location: ../index.php");
                exit;
            } else {
                $errorMessage = "Invalid email or password.";
            }
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
    <title>Login - Antique Art Auction</title>
    <link rel="stylesheet" href="login.css">
    
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
        <a href="./contact/contact.php">Contact</a>
    </nav>

    <div class="hero">
        Login To Your Account
    </div>

    <section class="login-section">
        <h2>Login</h2>

        <?php if ($errorMessage): ?>
            <div class="message-box error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <div class="register-link">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
        <p><a href="../Admin/admin_login.php">Login as Admin</a></p>

    </section>

    <footer>
        &copy; 2025 Antique Art Auction. All rights reserved.
    </footer>

</body>

</html>