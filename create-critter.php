<?php


session_start();
if (!isset($_SESSION['username'])) {
    header('Location: /');
    exit();
}

try {
    $dbPath = __DIR__ . '/../app.sqlite';
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch options for selects
    $adjectives = $pdo->query('SELECT id, value FROM adjectives ORDER BY value ASC')->fetchAll(PDO::FETCH_ASSOC);
    $nouns = $pdo->query('SELECT id, value FROM nouns ORDER BY value ASC')->fetchAll(PDO::FETCH_ASSOC);
    $verbs = $pdo->query('SELECT id, value FROM verbs ORDER BY value ASC')->fetchAll(PDO::FETCH_ASSOC);
    $locations = $pdo->query('SELECT id, value FROM locations ORDER BY value ASC')->fetchAll(PDO::FETCH_ASSOC);

    $errors = [];
    $success = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $adjective_id = isset($_POST['adjective_id']) ? intval($_POST['adjective_id']) : 0;
        $noun_id = isset($_POST['noun_id']) ? intval($_POST['noun_id']) : 0;
        $verb_id = isset($_POST['verb_id']) ? intval($_POST['verb_id']) : 0;
        $location_id = isset($_POST['location_id']) ? intval($_POST['location_id']) : 0;

        // Validate noun exists
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM nouns WHERE id = :id');
        $stmt->execute([':id' => $noun_id]);
        if ($stmt->fetchColumn() == 0) {
            $errors['noun_id'] = 'Please select a valid noun.';
        }
        // Validate others exist
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM adjectives WHERE id = :id');
        $stmt->execute([':id' => $adjective_id]);
        if ($stmt->fetchColumn() == 0) {
            $errors['adjective_id'] = 'Please select a valid adjective.';
        }
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM verbs WHERE id = :id');
        $stmt->execute([':id' => $verb_id]);
        if ($stmt->fetchColumn() == 0) {
            $errors['verb_id'] = 'Please select a valid verb.';
        }
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM locations WHERE id = :id');
        $stmt->execute([':id' => $location_id]);
        if ($stmt->fetchColumn() == 0) {
            $errors['location_id'] = 'Please select a valid location.';
        }

        if (empty($errors)) {
            // Get current user's id
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username');
            $stmt->execute([':username' => $_SESSION['username']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                $errors['author_id'] = 'Could not determine current user.';
            } else {
                $author_id = $user['id'];
                // Create critters table if not exists
                $pdo->exec('CREATE TABLE IF NOT EXISTS critters (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    noun_id INTEGER NOT NULL,
                    adjective_id INTEGER NOT NULL,
                    verb_id INTEGER NOT NULL,
                    location_id INTEGER NOT NULL,
                    author_id INTEGER NOT NULL,
                    FOREIGN KEY (noun_id) REFERENCES nouns(id) ON DELETE CASCADE,
                    FOREIGN KEY (adjective_id) REFERENCES adjectives(id) ON DELETE CASCADE,
                    FOREIGN KEY (verb_id) REFERENCES verbs(id) ON DELETE CASCADE,
                    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE,
                    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
                )');
                // Add indexes for performance
                $pdo->exec('CREATE INDEX IF NOT EXISTS idx_critters_noun_id ON critters(noun_id)');
                $pdo->exec('CREATE INDEX IF NOT EXISTS idx_critters_adjective_id ON critters(adjective_id)');
                $pdo->exec('CREATE INDEX IF NOT EXISTS idx_critters_verb_id ON critters(verb_id)');
                $pdo->exec('CREATE INDEX IF NOT EXISTS idx_critters_location_id ON critters(location_id)');
                $pdo->exec('CREATE INDEX IF NOT EXISTS idx_critters_author_id ON critters(author_id)');

                // Insert new critter
                $stmt = $pdo->prepare('INSERT INTO critters (noun_id, adjective_id, verb_id, location_id, author_id) VALUES (:noun_id, :adjective_id, :verb_id, :location_id, :author_id)');
                $stmt->execute([
                    ':noun_id' => $noun_id,
                    ':adjective_id' => $adjective_id,
                    ':verb_id' => $verb_id,
                    ':location_id' => $location_id,
                    ':author_id' => $author_id
                ]);
                $success = true;
                header('Location: /');
                exit();
            }
        }
    }
} catch (Exception $e) {
    echo '<div style="color:red;"><strong>Exception:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
    flush();
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
    <?php if ($success): ?>
        <div>Critter created successfully!</div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <ul>
        <?php foreach ($errors as $err): ?>
            <li><?php echo htmlspecialchars($err); ?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="POST" action="create-critter.php">
        <label for="adjective_id">Adjective:</label>
        <select name="adjective_id" id="adjective_id" required>
            <option value="">--Select--</option>
            <?php foreach ($adjectives as $adj): ?>
                <option value="<?php echo $adj['id']; ?>" <?php if (isset($_POST['adjective_id']) && $_POST['adjective_id'] == $adj['id']) echo 'selected'; ?>><?php echo htmlspecialchars($adj['value']); ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="noun_id">Noun:</label>
        <select name="noun_id" id="noun_id" required>
            <option value="">--Select--</option>
            <?php foreach ($nouns as $noun): ?>
                <option value="<?php echo $noun['id']; ?>" <?php if (isset($_POST['noun_id']) && $_POST['noun_id'] == $noun['id']) echo 'selected'; ?>><?php echo htmlspecialchars($noun['value']); ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="verb_id">Verb:</label>
        <select name="verb_id" id="verb_id" required>
            <option value="">--Select--</option>
            <?php foreach ($verbs as $verb): ?>
                <option value="<?php echo $verb['id']; ?>" <?php if (isset($_POST['verb_id']) && $_POST['verb_id'] == $verb['id']) echo 'selected'; ?>><?php echo htmlspecialchars($verb['value']); ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="location_id">Location:</label>
        <select name="location_id" id="location_id" required>
            <option value="">--Select--</option>
            <?php foreach ($locations as $loc): ?>
                <option value="<?php echo $loc['id']; ?>" <?php if (isset($_POST['location_id']) && $_POST['location_id'] == $loc['id']) echo 'selected'; ?>><?php echo htmlspecialchars($loc['value']); ?></option>
            <?php endforeach; ?>
        </select><br>

        <button type="submit">Create Critter</button>
    </form>
</body>
</html>
