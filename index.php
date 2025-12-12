<?php
session_start();

$dayOfWeek = date('l');
$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Vibe Coding</title>
</head>
<body>
    <?php if ($isLoggedIn): ?>
        <h1>Welcome <?php echo htmlspecialchars($username); ?>, <?php echo $dayOfWeek; ?>!</h1>
    <?php else: ?>
        <h1>Welcome Guest, <?php echo $dayOfWeek; ?>!</h1>
    <?php endif; ?>
    
    <?php if (!$isLoggedIn): ?>
        <p>We're excited to have you here. Get started by creating an account today.</p>
        <a href="register.php">Sign Up</a>
        <a href="login.php">Log In</a>
    <?php else: ?>
        <a href="create-critter.php">Create New Critter</a>
        <a href="logout.php">Log Out</a>
    <?php endif; ?>
</body>
</html>
