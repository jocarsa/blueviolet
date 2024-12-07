<?php
$anchura = 256;
// Create a blank 256x256 image
$image = imagecreatetruecolor($anchura, $anchura);

// Allocate colors
$backgroundColor = imagecolorallocate($image, 255, 255, 255); // White background
$roomColor = imagecolorallocate($image, 0, 0, 0); // Black rooms
$corridorColor = imagecolorallocate($image, 0, 0, 0); // Black corridors

// Fill the background
imagefill($image, 0, 0, $backgroundColor);

// Define room and corridor dimensions
$roomCount = rand(15, 30); // Number of random rooms

$rooms = [];

// Generate random rooms
for ($i = 0; $i < $roomCount; $i++) {
    $roomX = rand(10, $anchura - 10);
    $roomY = rand(10, $anchura - 10);
    $roomWidth = rand($anchura / 20, $anchura / 5);
    $roomHeight = rand($anchura / 20, $anchura / 5);

    // Ensure the room fits within the bounds of the image
    if ($roomX + $roomWidth >= $anchura) {
        $roomWidth = $anchura - $roomX - 1;
    }
    if ($roomY + $roomHeight >= $anchura) {
        $roomHeight = $anchura - $roomY - 1;
    }
    
    // Draw the random room
    imagefilledrectangle(
        $image,
        $roomX, $roomY,
        $roomX + $roomWidth, $roomY + $roomHeight,
        $roomColor
    );
    
    $rooms[] = [
        'x' => $roomX,
        'y' => $roomY,
        'width' => $roomWidth,
        'height' => $roomHeight,
        'centerX' => $roomX + ($roomWidth / 2),
        'centerY' => $roomY + ($roomHeight / 2)
    ];
}

// Now, add one additional room right in the middle of the map
$centerRoomWidth = $anchura / 10;
$centerRoomHeight = $anchura / 10;
$centerRoomX = ($anchura / 2) - ($centerRoomWidth / 2);
$centerRoomY = ($anchura / 2) - ($centerRoomHeight / 2);

// Ensure integer values for drawing
$centerRoomX = (int)$centerRoomX;
$centerRoomY = (int)$centerRoomY;
$centerRoomWidth = (int)$centerRoomWidth;
$centerRoomHeight = (int)$centerRoomHeight;

imagefilledrectangle(
    $image,
    $centerRoomX, $centerRoomY,
    $centerRoomX + $centerRoomWidth, $centerRoomY + $centerRoomHeight,
    $roomColor
);

// Add this central room to the rooms array
$rooms[] = [
    'x' => $centerRoomX,
    'y' => $centerRoomY,
    'width' => $centerRoomWidth,
    'height' => $centerRoomHeight,
    'centerX' => $centerRoomX + ($centerRoomWidth / 2),
    'centerY' => $centerRoomY + ($centerRoomHeight / 2)
];

// Draw corridors connecting the rooms, including the new center room
$corridorThickness = 3; // Thickness of the corridor lines
imagesetthickness($image, $corridorThickness);

// Sort rooms by their center, for a predictable corridor path (optional)
usort($rooms, function($a, $b) {
    return $a['centerX'] + $a['centerY'] <=> $b['centerX'] + $b['centerY'];
});

for ($i = 1; $i < count($rooms); $i++) {
    $prevRoom = $rooms[$i - 1];
    $currRoom = $rooms[$i];
    
    // Draw a horizontal line
    imageline(
        $image,
        $prevRoom['centerX'], $prevRoom['centerY'],
        $currRoom['centerX'], $prevRoom['centerY'],
        $corridorColor
    );
    
    // Draw a vertical line
    imageline(
        $image,
        $currRoom['centerX'], $prevRoom['centerY'],
        $currRoom['centerX'], $currRoom['centerY'],
        $corridorColor
    );
}

// Now, allocate the additional colors for single pixels
$redColor    = imagecolorallocate($image, 255, 0, 0);
$greenColor  = imagecolorallocate($image, 0, 255, 0);
$blueColor   = imagecolorallocate($image, 0, 0, 255);
$yellowColor = imagecolorallocate($image, 255, 255, 0);
$magentaColor= imagecolorallocate($image, 255, 0, 255);
$cyanColor   = imagecolorallocate($image, 0, 255, 255);

$colors = [$redColor, $greenColor, $blueColor, $yellowColor, $magentaColor, $cyanColor];

function getRandomWallPoint($room) {
    $whichWall = rand(1,4);
    $x = 0; 
    $y = 0;
    switch ($whichWall) {
        case 1: // top wall
            $x = rand($room['x'], $room['x'] + $room['width']);
            $y = $room['y'];
            break;
        case 2: // bottom wall
            $x = rand($room['x'], $room['x'] + $room['width']);
            $y = $room['y'] + $room['height'];
            break;
        case 3: // left wall
            $x = $room['x'];
            $y = rand($room['y'], $room['y'] + $room['height']);
            break;
        case 4: // right wall
            $x = $room['x'] + $room['width'];
            $y = rand($room['y'], $room['y'] + $room['height']);
            break;
    }
    return [$x, $y];
}

// Place one colored pixel for each of the colors
foreach ($colors as $clr) {
    if (count($rooms) > 0) {
        $randRoom = $rooms[array_rand($rooms)];
        list($px, $py) = getRandomWallPoint($randRoom);
        imagesetpixel($image, $px, $py, $clr);
    }
}

// Create the "mapas" directory if it doesn't exist
$directory = 'mapas';
if (!is_dir($directory)) {
    mkdir($directory, 0755, true);
}

// Save the image to the "mapas" folder with the filename as the epoch time
$filename = $directory . '/' . time() . '.png';
imagepng($image, $filename);

// Clean up
imagedestroy($image);

// Output the filename for confirmation
echo "Image saved as: " . $filename;
?>

