<?php
// Script to send requested KMZ file and delete old files
// whilst allowing for Google's timeouts and request sequences

// Clean the post variable and send the requested file
$kmz_file = preg_replace('/[^a-zA-Z0-9]/','',$_GET['kmz'] ?? '');
$kmz_file_folder = __DIR__ . '/kmltemp/';
$kmz_file_path = $kmz_file_folder . $kmz_file;

if ($kmz_file != "") {
    header('Content-type: application/zip');
    header('Content-Disposition: attachment; filename="' . $kmz_file . '"');

    ignore_user_abort(true);

    $context = stream_context_create();
    $file = fopen($kmz_file_path, 'r', FALSE, $context);
    while(!feof($file)) {
        echo stream_get_contents($file, 2014);
    }
    fclose($file);
    flush();
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