<form method="POST" action="register.php">
    <div class="form-group">
        <label for="username">Username</label>
        <input 
            type="text" 
            id="username" 
            name="username" 
            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
            class="<?php echo isset($errors['username']) ? 'error-field' : ''; ?>"
        >
        <?php if (isset($errors['username'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($errors['username']); ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
            class="<?php echo isset($errors['email']) ? 'error-field' : ''; ?>"
        >
        <?php if (isset($errors['email'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($errors['email']); ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input 
            type="password" 
            id="password" 
            name="password"
            class="<?php echo isset($errors['password']) ? 'error-field' : ''; ?>"
        >
        <?php if (isset($errors['password'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($errors['password']); ?></div>
        <?php endif; ?>
    </div>

    <button type="submit">Register</button>
</form>