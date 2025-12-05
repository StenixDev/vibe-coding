<?php
$dayOfWeek = date('l');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Vibe Coding</title>
</head>
<body>
    <h1>Welcome to Vibe Coding, <?php echo $dayOfWeek; ?>!</h1>
    <p>We're excited to have you here. Get started by creating an account today.</p>
    <a href="register.php">Sign Up</a>
</body>
</html>
