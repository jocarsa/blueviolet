<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('POSITIONS_FILE', __DIR__ . '/player_positions.json');

// Remove the game data file to reset the game
if (file_exists(POSITIONS_FILE)) {
    if (!unlink(POSITIONS_FILE)) {
        error_log("Failed to delete " . POSITIONS_FILE);
    } else {
        error_log(POSITIONS_FILE . " deleted successfully.");
    }
} else {
    error_log(POSITIONS_FILE . " does not exist.");
}

header('Content-Type: application/json');
echo json_encode(["status" => "reset"]);

