<?php
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Validate username
    if (empty($username)) {
        $errors['username'] = 'Username is required.';
    } elseif (strlen($username) < 3) {
        $errors['username'] = 'Username must be at least 3 characters long.';
    } elseif (strlen($username) > 12) {
        $errors['username'] = 'Username must be no longer than 12 characters.';
    } elseif (!preg_match('/^[a-z0-9]+$/i', $username)) {
        $errors['username'] = 'Username can only contain letters (a-z) and numbers (0-9).';
    }
    
    // Validate email
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    
    // Validate password
    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 12) {
        $errors['password'] = 'Password must be at least 12 characters long.';
    }
    
    // If no errors, show success message
    if (empty($errors)) {
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Create Your Account</h1>
    
    <?php if ($success): ?>
        <div>
            <h2>Thank You!</h2>
            <p>Your account has been created successfully. Welcome to Vibe Coding!</p>
        </div>
    <?php elseif (!empty($errors)): ?>
        <div>
            <h2>Registration Error</h2>
            <p>Please fix the following issues:</p>
            <ul>
                <?php foreach ($errors as $field => $message): ?>
                    <li><?php echo htmlspecialchars($message); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php include 'register_form.php'; ?>
    <?php else: ?>
        <?php include 'register_form.php'; ?>
    <?php endif; ?>
</body>
</html>
