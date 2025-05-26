<?php
// public/get_scores.php
// this file is an api endpoint that returns the current scoreboard data as json.
// it is called by the scoreboard.php page to dynamically update the display.

// include the database connection
require_once "../src/db_connect.php";

header("Content-Type: application/json");

$scoreboard_data = [];

try {
    $stmt = $pdo->query("
        SELECT 
            u.id,
            u.display_name,
            COALESCE(SUM(s.points), 0) AS total_points
        FROM
            users u
        LEFT JOIN
            scores s ON u.id = s.user_id
        GROUP BY
            u.id, u.display_name
        ORDER BY
            total_points DESC, u.display_name ASC;
    ");

    $scoreboard_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true,'data'=> $scoreboard_data]);
} catch (PDOException $e) {
    error_log("Database Error fetching scoreboard data in get_scores.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to load scoreboard data: ' . $e->getMessage()]);
}
?>