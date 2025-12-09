<form method="POST" action="login.php">
    <div>
        <label for="username">Username</label>
        <input 
            type="text" 
            id="username" 
            name="username" 
            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
        >
        <?php if (isset($errors['username'])): ?>
            <div><?php echo htmlspecialchars($errors['username']); ?></div>
        <?php endif; ?>
    </div>

    <div>
        <label for="password">Password</label>
        <input 
            type="password" 
            id="password" 
            name="password"
        >
        <?php if (isset($errors['password'])): ?>
            <div><?php echo htmlspecialchars($errors['password']); ?></div>
        <?php endif; ?>
    </div>

    <button type="submit">Log In</button>
</form>
