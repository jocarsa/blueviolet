<?php
$anchura = 64;
// Create a blank 256x256 image
$image = imagecreatetruecolor($anchura, $anchura);

// Allocate colors
$backgroundColor = imagecolorallocate($image, 255, 255, 255); // White background
$roomColor = imagecolorallocate($image, 0, 0, 0); // Black rooms
$corridorColor = imagecolorallocate($image, 0, 0, 0); // Black corridors

// Fill the background
imagefill($image, 0, 0, $backgroundColor);

// Define room and corridor dimensions
$roomCount = rand(15, 30); // Number of rooms

$rooms = [];

// Generate random rooms
for ($i = 0; $i < $roomCount; $i++) {
    $roomX = rand(10, $anchura-10);
    $roomY = rand(10, $anchura-10);
    $roomWidth = rand($anchura/5, $anchura/20);
    $roomHeight = rand($anchura/5, $anchura/20);
    
    // Draw the room as a filled rectangle
    imagefilledrectangle(
        $image,
        $roomX, $roomY,
        $roomX + $roomWidth, $roomY + $roomHeight,
        $roomColor
    );
    
    // Save the center of the room for corridor connections
    $rooms[] = [
        'centerX' => $roomX + ($roomWidth / 2),
        'centerY' => $roomY + ($roomHeight / 2)
    ];
}

// Draw corridors connecting the rooms
$corridorThickness = 2; // Thickness of the corridor lines
for ($i = 1; $i < count($rooms); $i++) {
    $prevRoom = $rooms[$i - 1];
    $currRoom = $rooms[$i];
    
    // Draw a horizontal line
    imagesetthickness($image, $corridorThickness);
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

// Output the image
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);

