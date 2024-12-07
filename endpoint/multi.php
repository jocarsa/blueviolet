<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Use a file to save player positions and winner info
define('POSITIONS_FILE', 'player_positions.json');

// Set default timezone
date_default_timezone_set('UTC');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get posted data
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id']) && isset($data['x']) && isset($data['y']) && isset($data['color']) && isset($data['username'])) {
        $playerId = $data['id'];
        $x = $data['x'];
        $y = $data['y'];
        $color = $data['color'];
        $username = $data['username'];
        $achievedGoals = isset($data['achievedGoals']) ? (int)$data['achievedGoals'] : 0;
        $totalGoals = isset($data['totalGoals']) ? (int)$data['totalGoals'] : 0;

        $gameData = getGameData();

        // Update player data
        $gameData['players'][$playerId] = [
            'id' => $playerId,
            'x' => $x,
            'y' => $y,
            'color' => $color,
            'username' => $username,
            'achievedGoals' => $achievedGoals,
            'totalGoals' => $totalGoals,
            'timestamp' => time()
        ];

        // Check for winner if not already set
        if ($gameData['winnerId'] === null && $totalGoals > 0 && $achievedGoals === $totalGoals) {
            // This player achieved all goals. If no winner yet, declare this player the winner.
            $gameData['winnerId'] = $playerId;
        }

        file_put_contents(POSITIONS_FILE, json_encode($gameData));
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $currentTimestamp = time();
    $gameData = getGameData();

    $recentPositions = [];
    foreach ($gameData['players'] as $playerId => $playerData) {
        if ($currentTimestamp - $playerData['timestamp'] <= 1) {
            $recentPositions[] = $playerData;
        }
    }

    header('Content-Type: application/json');
    echo json_encode([
        'players' => $recentPositions,
        'winnerId' => $gameData['winnerId']
    ]);
}

// Function to get game data with winner info and players
function getGameData() {
    if (file_exists(POSITIONS_FILE)) {
        $data = json_decode(file_get_contents(POSITIONS_FILE), true);
        if (!is_array($data)) {
            $data = ['winnerId'=>null, 'players'=>[]];
        } else {
            if (!isset($data['winnerId'])) {
                $data['winnerId'] = null;
            }
            if (!isset($data['players'])) {
                $data['players'] = [];
            }
        }
    } else {
        $data = ['winnerId'=>null, 'players'=>[]];
    }
    return $data;
}
?>

