<?php
// public/judge.php
//
// this file serves as the judge portal. judges can view a list of users
// and assign points to them. it handles the display of users, the score
// submission form, and processes the submitted scores.

// includes the database connection
require_once "../src/db_connect.php";

$jsMessage = "";
$jsMessageType = "";

// handle form submission for scoring a user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_score'])) {
    $user_id = (int) $_POST['user_id'];
    $judge_id = (int) $_POST['judge_id'];
    $points = (int) $_POST['points'];

    if ($points < 1 || $points > 100) {
        $jsMessage = 'Points must be between 1 and 100.';
        $jsMessageType = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO scores (user_id, judge_id, points) VALUES (:user_id, :judge_id, :points)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':judge_id', $judge_id);
            $stmt->bindParam(':points', $points);


            if ($stmt->execute()) {
                $jsMessage = 'Score of ' . htmlspecialchars($points) . ' points submitted for User ID ' . htmlspecialchars($user_id) . ' by Judge ID ' . htmlspecialchars($judge_id) . ' successfully!';
                $jsMessageType = 'success';
            } else {
                $jsMessage = 'Failed to submit score. Please try again.';
                $jsMessageType = 'error';
            }
        } catch (PDOException $e) {
            error_log("Database Error while submitting score in judge.php: " . $e->getMessage());
            $jsMessage = 'An unexpected database error occurred during score submission.';
            $jsMessageType = 'error';
        }
    }
} else {
    $jsMessage = 'Please select a judge, a user, and enter points.';
    $jsMessageType = 'error';
}

$users = [];
try {
    $stmt = $pdo->query("SELECT id, username, display_name FROM users ORDER BY display_name ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database Error fetching users in judge.php: " . $e->getMessage());
    if (empty($jsMessage)) { // Only set if no other message is pending
        $jsMessage = 'Could not retrieve users for scoring.';
        $jsMessageType = 'error';
    }
}

$judges = [];
try {
    $stmt = $pdo->query("SELECT id, username, display_name FROM judges ORDER BY display_name ASC");
    $judges = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database Error fetching judges in judge.php: " . $e->getMessage());
    if (empty($jsMessage)) { // Only set if no other message is pending
        $jsMessage = 'Could not retrieve judges for scoring.';
        $jsMessageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judge Portal</title>
    <link rel="stylesheet" href="styles/reset.css">
    <link rel="stylesheet" href="styles/global.css">
</head>

<body>
    <div id="toast-container"></div>

    <div class="container">
        <h1>Judge Portal</h1>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="admin.php">Admin Panel</a>
            <a href="scoreboard.php">Public Scoreboard</a>
        </nav>


        <h2>Assign Points to a User</h2>
        <form method="post">
            <div>
                <label for="judge_id">Select Judge:</label>
                <select id="judge_id" name="judge_id" required>
                    <option value="">-- Select a Judge --</option>
                    <?php if (!empty($judges)): ?>
                        <?php foreach ($judges as $judge): ?>
                            <option value="<?php echo htmlspecialchars($judge['id']); ?>">
                                <?php echo htmlspecialchars($judge['display_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No judges found. Please add judges through the admin panel</option>
                    <?php endif; ?>
                </select>
            </div>

            <div>
                <label for="user_id">Select User:</label>
                <select id="user_id" name="user_id" required>
                    <option value="">-- Select a User --</option>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo htmlspecialchars($user['id']); ?>">
                                <?php echo htmlspecialchars($user['display_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No Users found. Please make sure there are users in the database</option>
                    <?php endif; ?>
                </select>
            </div>

            <div>
                <label for="points">Points (1 - 100)</label>
                <input type="number" name="points" id="points" min="1" max="100" required>
            </div>

            <button type="submit" name="submit_score">Submit Score</button>
        </form>

        <h2>All Participating Users</h2>
        <?php if (!empty($users)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Display Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['display_name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No users found. Please maker sure there are users in the database.</p>
        <?php endif; ?>
    </div>

    <script src="scripts/ui_utils.js"></script>
    <script>
        // javadcript to display toast messages if they exist from php
        document.addEventListener('DOMContentLoaded', () => {
            const message = <?php echo json_encode($jsMessage); ?>;
            const messageType = <?php echo json_encode($jsMessageType); ?>;

            if (message && messageType) {
                showToast(message, messageType);
            }

            // client-side validation for the score form using constraint validation api
            const scoreForm = document.getElementById('scoreForm');
            const pointsInput = document.getElementById('points');

            scoreForm.addEventListener('submit', (event) => {
                if (!scoreForm.checkValidity()) {
                    event.preventDefault();

                    if (pointsInput.validity.rangeUnderflow || pointsInput.validity.rangeOverflow || pointsInput.validity.valueMissing) {
                        showToast(pointsInput.validationMessage, 'error', 0); // show permanent error toast
                    } else if (!pointsInput.validity.valid) {
                        showToast('Please ensure all required fields are filled correctly.', 'error', 0);
                    }
                }
            });
        });
    </script>
</body>

</html>