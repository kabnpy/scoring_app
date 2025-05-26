<?php
// public/admin.php
// this file handles adding new judges and displaying existing ones

// includes the database connection
require_once "../src/db_connect.php";

$jsMessage = "";
$jsMessageType = "";

// handle form submission for adding a new judge
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_judge'])) {
    $username = trim($_POST['username']);
    $display_name = trim($_POST['display_name']);

    if (empty($username) || empty($display_name)) {
        $jsMessage = 'Username and Display Name cannot be empty.';
        $jsMessageType = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO judges (username, display_name) VALUES (:username, :display_name)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':display_name', $display_name);

            if ($stmt->execute()) {
                $jsMessage = 'Judge "' . htmlspecialchars($display_name) . '" added successfully!';
                $jsMessageType = 'success';
            } else {
                $jsMessage = 'Failed to add judge. Please try again.';
                $jsMessageType = 'error';
            }
        } catch (PDOException $e) {
            // Check for duplicate entry error (SQLSTATE 23000, MySQL error code 1062)
            if ($e->getCode() == '23000' && strpos($e->getMessage(), '1062 Duplicate entry') !== false) {
                $jsMessage = 'Error: Judge username "' . htmlspecialchars($username) . '" already exists.';
                $jsMessageType = 'error';
            } else {
                error_log("Database Error while adding a judge in judge.php: " . $e->getMessage());
                $jsMessage = 'An unexpected database error occurred while adding judge.';
                $jsMessageType = 'error';
            }
        }
    }

}

$judges = [];
try {
    $stmt = $pdo->query("SELECT id, username, display_name FROM judges order by display_name ASC");
    $judges = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database Error fetching judges in admin.php: " . $e->getMessage());
    if (empty($jsMessage)) { // only set if no other message is pending
        $jsMessage = 'Failed to load existing judges: ' . $e->getMessage();
        $jsMessageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Judge Management</title>
    <link rel="stylesheet" href="styles/reset.css">
    <link rel="stylesheet" href="styles/global.css">
</head>

<body>
    <div id="toast-container"></div>

    <div class="container">
        <h1>Admin Panel - Judge Management</h1>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="judge.php">Judge Portal</a>
            <a href="scoreboard.php">Public Scoreboard</a>
        </nav>


        <h2>Add New Judge</h2>
        <form method="post">
            <div>
                <label for="username">Judge Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="display_name">Judge Display Name:</label>
                <input type="text" id="display_name" name="display_name" required>
            </div>
            <button type="submit" name="add_judge">Add Judge</button>
        </form>

        <h2>Existing Judges</h2>
        <?php if (!empty($judges)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Display Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($judges as $judge): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($judge['id']); ?></td>
                            <td><?php echo htmlspecialchars($judge['username']); ?></td>
                            <td><?php echo htmlspecialchars($judge['display_name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No judges added yet.</p>
        <?php endif; ?>
    </div>

    <script src="scripts/ui_utils.js"></script>
    <script>
        // javascript to display toast messages if they exist from php
        document.addEventListener('DOMContentLoaded', () => {
            const message = <?php echo json_encode($jsMessage); ?>;
            const messageType = <?php echo json_encode($jsMessageType); ?>;

            if (message && messageType) {
                showToast(message, messageType);
            }
        });
    </script>
</body>

</html>