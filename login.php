<?php
// Set secure session cookie parameters before starting session
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Strict',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? true : false,
]);

session_start();

// If already logged in, redirect to index
if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Validate that fields are not empty
    if (empty($username)) {
        $errors['username'] = 'Username is required.';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    }
    
    // If fields are not empty, check against database
    if (empty($errors)) {
        $dbPath = __DIR__ . '/../app.sqlite';
        
        try {
            // Connect to SQLite with PDO
            $pdo = new PDO('sqlite:' . $dbPath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Query for user by username
            $stmt = $pdo->prepare('SELECT password FROM users WHERE username = :username');
            $stmt->execute([':username' => $username]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Check if user exists and password matches
            if ($result && password_verify($password, $result['password'])) {
                // Start the session and store username
                $_SESSION['username'] = $username;
                $success = true;
                // Redirect to index after successful login
                header('Location: index.php');
                exit();
            } else {
                $errors['login'] = 'Invalid username or password.';
            }
            
        } catch (PDOException $e) {
            // Log error and show generic message
            error_log('SQLite error: ' . $e->getMessage());
            $errors['db'] = 'An internal error occurred. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
</head>
<body>
    <h1>Log In</h1>
    
    <?php if (!empty($errors)): ?>
        <div>
            <h2>Login Error</h2>
            <ul>
                <?php foreach ($errors as $field => $message): ?>
                    <li><?php echo htmlspecialchars($message); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php include 'login_form.php'; ?>
    <?php else: ?>
        <?php include 'login_form.php'; ?>
    <?php endif; ?>
</body>
</html>
