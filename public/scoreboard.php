<?php
// public/scoreboard.php
//
// this file displays the public scoreboard.
// it fetches user scores and displays them in a table, sorted by total points.
// the scoreboard dynamically updates fetching new data from get_scores.php.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Scoreboard</title>
    <link rel="stylesheet" href="styles/reset.css">
    <link rel="stylesheet" href="styles/global.css">
    <style>
        /* Specific styling for the scoreboard to ensure it looks good */
        #scoreboard-table {
            margin-top: 30px;
            min-height: 200px;
            /* Ensure some height even if no scores */
        }

        #loading-indicator {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Public Scoreboard</h1>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="admin.php">Admin Panel</a>
            <a href="judge.php">Judge Portal</a>
        </nav>

        <p style="text-align: center; margin-top: 20px;">Scores update automatically every 5 seconds.</p>

        <div class="loading-indicator">Loading scores...</div>
        <table id="scoreboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>User</th>
                    <th>Total Points</th>
                </tr>
            </thead>
            <tbody id="scoreboard-body">
                <!-- Scores will be dynamically inserted here -->
            </tbody>
        </table>
    </div>

    <script src="scripts/scoreboard.js"></script>
</body>

</html>