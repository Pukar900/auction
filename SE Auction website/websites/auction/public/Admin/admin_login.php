<?php
session_start();
include_once '../db_connection.php';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $errorMessage = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['is_admin'] = true;
            $_SESSION['login_success'] = "Welcome, Admin {$admin['name']}!";
            header("Location: admin_index.php");
            exit;
        } else {
            $errorMessage = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Antique Art Auction</title>
    <link rel="stylesheet" href="login_admin.css">
</head>
<body>

<header>
    <h1>Admin Login</h1>
    <p>Manage Auctions and Admin Features</p>
</header>

<section class="login-section">
    <h2>Login</h2>

    <?php if ($errorMessage): ?>
        <div class="message-box error"><?= htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="email">Admin Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>

        <label for="password">Admin Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>

    <p><a href="../index.php">← Back to Homepage</a></p>
</section>

</body>
</html>
