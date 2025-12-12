<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: /');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Critter</title>
</head>
<body>
    <h1>Create Your New Critter</h1>
</body>
</html>
