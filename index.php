<?php
session_start();

$dayOfWeek = date('l');
$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

// Database: create adjectives and nouns tables and seed if needed
$dbPath = __DIR__ . '/../app.sqlite';
try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if adjectives table exists
    $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='adjectives'");
    if ($result->fetch() === false) {
        // Create table
        $pdo->exec("CREATE TABLE adjectives (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            value TEXT NOT NULL
        )");
        // Seed values
        $adjectives = ['black', 'blue', 'brown', 'crochet', 'cute', 'fluffy', 'fuzzy', 'glittery', 'glowing neon', 'gray', 'green', 'lime green', 'magenta', 'orange', 'pastel', 'pink', 'purple', 'rainbow', 'red', 'sleepy', 'soft', 'sparkly', 'stone', 'teal', 'white', 'wooden', 'yellow'];
        $stmt = $pdo->prepare('INSERT INTO adjectives (value) VALUES (:value)');
        foreach ($adjectives as $adj) {
            $stmt->execute([':value' => $adj]);
        }
    }

    // Check if nouns table exists
    $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='nouns'");
    if ($result->fetch() === false) {
        // Create table
        $pdo->exec("CREATE TABLE nouns (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            value TEXT NOT NULL
        )");
        // Seed values
        $nouns = ['beaver', 'bird', 'bunny rabbit', 'cat', 'chipmunk', 'cow', 'elephant', 'fox', 'frog', 'hamster', 'horse', 'kangaroo', 'koala', 'lemur', 'lion', 'monkey', 'moose', 'mouse', 'panda', 'penguin', 'pig', 'pika', 'puppy', 'quokka', 'raccoon', 'rat', 'sea otter', 'sheep', 'skunk', 'sloth', 'squirrel', 'teddy bear', 'tiger', 'turtle', 'wombat'];
        $stmt = $pdo->prepare('INSERT INTO nouns (value) VALUES (:value)');
        foreach ($nouns as $noun) {
            $stmt->execute([':value' => $noun]);
        }
    }

        // Check if verbs table exists
        $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='verbs'");
        if ($result->fetch() === false) {
            // Create table
            $pdo->exec("CREATE TABLE verbs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                value TEXT NOT NULL
            )");
            // Seed values
            $verbs = ['runs', 'jumps', 'waves hello', 'snuggles their blanket', 'swims', 'takes a bubble bath', 'wears a chef hat and stirs a baking bowl', 'hula hoops', 'builds with blocks', 'flies a kite', 'skateboards', 'rides their scooter', 'plays chess', 'writes with a pen and pad', 'plays guitar', 'plays the drums', 'plays the piano', 'roller skates', 'plays the saxophone', 'plays basketball', 'plays baseball', 'plays hockey', 'plays soccer', 'reads a book', 'wears pajamas', 'sleeps in bed', 'sings into a microphone', 'jumps rope', 'plays with a yo-yo', 'waters a plant with a watering can', 'sits by a campfire', 'naps in a hammock', 'plays golf', 'plays tennis', 'bakes cookies', 'does yoga'];
            $stmt = $pdo->prepare('INSERT INTO verbs (value) VALUES (:value)');
            foreach ($verbs as $verb) {
                $stmt->execute([':value' => $verb]);
            }
        }

        // Check if locations table exists
        $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='locations'");
        if ($result->fetch() === false) {
            // Create table
            $pdo->exec("CREATE TABLE locations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                value TEXT NOT NULL
            )");
            // Seed values
            $locations = ['at the beach', 'on a sunny island', 'in the backyard', 'inside a snow globe', 'inside a Christmas ornament', 'safely inside a hot air balloon up in the sky', 'on the moon', 'in front of a gnomes house', 'in a majestic waterfall', 'on a trampoline', 'inside an igloo', 'on a rainbow', 'underwater inside an aquarium', 'at the pumpkin patch', 'at the north pole', 'in a candy land', 'on a farm', 'in the frontyard', 'in a big grassy field', 'on top of a cloud', 'on a bridge', 'in a forest', 'at the top of a mountain', 'in a boat on a river', 'at the mall', 'on a baseball field', 'on a soccer field', 'on a basketball court', 'on a hockey rink', 'in a swimming pool', 'in a hot tub', 'in the kitchen'];
            $stmt = $pdo->prepare('INSERT INTO locations (value) VALUES (:value)');
            foreach ($locations as $loc) {
                $stmt->execute([':value' => $loc]);
            }
        }
} catch (PDOException $e) {
    error_log('SQLite error: ' . $e->getMessage());
    // Optionally, show a generic error to the user or ignore
}
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
        <?php
        // Show My Critters
        $myCritters = [];
        try {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username');
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $userId = $user['id'];
                $critters = $pdo->prepare('
                    SELECT a.value AS adjective, n.value AS noun, v.value AS verb, l.value AS location
                    FROM critters c
                    JOIN adjectives a ON c.adjective_id = a.id
                    JOIN nouns n ON c.noun_id = n.id
                    JOIN verbs v ON c.verb_id = v.id
                    JOIN locations l ON c.location_id = l.id
                    WHERE c.author_id = :author_id
                ');
                $critters->execute([':author_id' => $userId]);
                $myCritters = $critters->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            echo '<div style="color:red;">Error loading critters: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
        <?php if (!empty($myCritters)): ?>
            <h2>My Critters</h2>
            <ul>
            <?php foreach ($myCritters as $critter): ?>
                <li>The <?php echo htmlspecialchars($critter['adjective']); ?> <?php echo htmlspecialchars($critter['noun']); ?> <?php echo htmlspecialchars($critter['verb']); ?> <?php echo htmlspecialchars($critter['location']); ?></li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
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
