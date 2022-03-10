<?php
// Script to send requested KMZ file and delete old files
// whilst allowing for Google's timeouts and request sequences

// Clean the post variable and send the requested file
$kmz_file = preg_replace('/[^a-zA-Z0-9]/','',$_GET['kmz'] ?? '');
$kmz_file_folder = __DIR__ . '/kmltemp/';
$kmz_file_path = $kmz_file_folder . $kmz_file;

if (($kmz_file != "") && (file_exists($kmz_file_path))) {
    header('Content-type: application/zip');
    header('Content-Disposition: attachment; filename="' . $kmz_file . '"');

    $file = fopen($kmz_file_path, 'r');
    echo fread($file, filesize($kmz_file_path));
    fclose($file);
}

// Before finishing, clean up old files
$purgetime = 5 * 60;  // 5 minutes

if (file_exists($kmz_file_folder)) {
    foreach (new DirectoryIterator($kmz_file_folder) as $fileInfo) {
        if ($fileInfo->isDot()) {
            continue;
        }
        if (($fileInfo->isFile()) && (time() - $fileInfo->getCTime() >= $purgetime)) {
            unlink($fileInfo->getRealPath());
        }
    }
}