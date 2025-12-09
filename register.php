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
    header('Location: /');
    exit();
}

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
        // Prepare DB path one level above public directory
        $dbPath = __DIR__ . '/../app.sqlite';

        try {
            // Connect to SQLite with PDO
            $pdo = new PDO('sqlite:' . $dbPath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Create users table if it doesn't exist
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");

            // Check for existing username
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
            $stmt->execute([':username' => $username]);
            if ($stmt->fetchColumn() > 0) {
                $errors['username'] = 'Username is already taken.';
            }

            // Check for existing email
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
            $stmt->execute([':email' => $email]);
            if ($stmt->fetchColumn() > 0) {
                $errors['email'] = 'An account with that email already exists.';
            }

            // If still no errors, insert the user
            if (empty($errors)) {
                // Sanitize email before storing
                $safeEmail = filter_var($email, FILTER_SANITIZE_EMAIL);

                // Hash the password
                $hashed = password_hash($password, PASSWORD_DEFAULT);

                $insert = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (:username, :email, :password)');
                $insert->execute([
                    ':username' => $username,
                    ':email' => $safeEmail,
                    ':password' => $hashed,
                ]);

                // Log the user in by storing username in session
                $_SESSION['username'] = $username;

                // Redirect to homepage
                header('Location: /');
                exit();
            }

        } catch (PDOException $e) {
            // Don't expose internal DB errors to the user. Log and show a generic message.
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
    <title>Register</title>
</head>
<body>
    <h1>Create Your Account</h1>
    
    <?php if (!empty($errors)): ?>
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
