	<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	// Use a file to save player positions as a simple data store
	define('POSITIONS_FILE', 'player_positions.json');

	// Set default timezone for consistent timestamps
	date_default_timezone_set('UTC');

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		 // Get the posted data
		 $data = json_decode(file_get_contents('php://input'), true);
		 
		 if (isset($data['id']) && isset($data['x']) && isset($data['y']) && isset($data['color'])) {
		     // Read current player positions from the file
		     $playerPositions = getPlayerPositions();

		     // Update the player position for the given player id
		     $playerId = $data['id'];
		     $playerPositions[$playerId] = [
		         'id' => $playerId,
		         'x' => $data['x'],
		         'y' => $data['y'],
		         'color' => $data['color'],
		         'timestamp' => time(),
		         'username' => $data['username']
		     ];

		     // Save updated player positions
		     file_put_contents(POSITIONS_FILE, json_encode($playerPositions));
		 }
	} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
		 // Retrieve player positions, only return positions updated in the last second
		 $currentTimestamp = time();
		 $playerPositions = getPlayerPositions();
		 $recentPositions = [];

		 foreach ($playerPositions as $playerId => $playerData) {
		     if ($currentTimestamp - $playerData['timestamp'] <= 1) {
		         $recentPositions[$playerId] = $playerData;
		     }
		 }

		 header('Content-Type: application/json');
		 echo json_encode([ 'players' => array_values($recentPositions) ]);
	}

	// Get player positions from file
	function getPlayerPositions() {
		 if (file_exists(POSITIONS_FILE)) {
		     $data = file_get_contents(POSITIONS_FILE);
		     return json_decode($data, true) ?: [];
		 }
		 return [];
	}
	?>

