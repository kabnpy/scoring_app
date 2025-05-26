<?php
// public/index.php
//
// this file serves as the landing page that provides navigation links to:
// admin panel, judge portal, public scoreboard
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoring Application</title>
    <link rel="stylesheet" href="styles/reset.css">
    <link rel="stylesheet" href="styles/global.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to the Scoring Application</h1>
        <p>Use the links below to navigate through the application</p>

        <nav class="nav-links">
            <a href="admin.php">Admin Panel</a>
            <a href="judge.php">Judge Portal</a>
            <a href="scoreboard.php">Public Scoreboard</a>
        </nav>
    </div>
</body>
</html>