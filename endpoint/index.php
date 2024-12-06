<?php
// Check if the URL contains ?o=mapas
if (isset($_GET['o']) && $_GET['o'] === 'mapas') {
    $directory = 'mapas';
    $files = [];

    // Check if the "mapas" directory exists
    if (is_dir($directory)) {
        // Open the directory
        if ($handle = opendir($directory)) {
            // Loop through the files in the directory
            while (false !== ($entry = readdir($handle))) {
                // Only include .png files
                if ($entry !== '.' && $entry !== '..' && pathinfo($entry, PATHINFO_EXTENSION) === 'png') {
                    $files[] = $entry;
                }
            }
            // Close the directory
            closedir($handle);
        }
    }

    // Return the list of files as a JSON response
    header('Content-Type: application/json');
    echo json_encode($files);
} else {
    // Handle other cases or return an error if needed
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(["error" => "Invalid request"]);
}
?>

