<?php
namespace App\Support;

use ZipArchive;

class Zipper {

    protected ZipArchive $zip;



    public function __construct( ) {
        $this->zip = new ZipArchive( );
    }



    // Compress a folder and its subfolders into a ZIP file
    public function compress( $sourceDir ) {
        // Check if the source directory exists
        if (!is_dir($sourceDir)) {
            return ['ok' => false, 'message' => 'Source directory does not exist.'];
        }
    
        // Define the output ZIP file path (same name as the folder)
        $zipFile = rtrim($sourceDir, DIRECTORY_SEPARATOR) . ".zip";
    
        // Open the ZIP file for writing
        if ($this->zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return ['ok' => false, 'message' => 'Failed to create ZIP file.'];
        }
    
        // Recursively add files from the source directory
        $this->addFolderToZip($sourceDir, $sourceDir);
    
        // Close the ZIP file
        $this->zip->close();
    
        return ['ok' => true, 'message' => 'Folder successfully compressed into ZIP.'];
    }
    



    // Add a folder and its subfolders to the ZIP file
    private function addFolderToZip( $folder, $sourceDir): void {
        // Create an iterator for the folder
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folder),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        // Add files to the ZIP archive
        foreach ($files as $file) {
            // Skip directories (they will be handled automatically by the ZIP)
            if ($file->isDir()) {
                continue;
            }

            // Get the relative path of the file (relative to the source directory)
            $relativePath = substr($file->getRealPath(), strlen($sourceDir) + 1);

            // Add the file to the ZIP archive
            $this->zip->addFile($file->getRealPath(), $relativePath);
        }
    }





    // Decompress a ZIP file to the original folder structure
    public function decompress($zipFile, $extractTo): array {
        // Check if the ZIP file exists
        if (!file_exists($zipFile)) {
            return ['ok' => false, 'message' => 'ZIP file does not exist.'];
        }

        // Create a new ZipArchive instance

        // Open the ZIP file for reading
        if ($this->zip->open($zipFile) !== true) {
            return ['ok' => false, 'message' => 'Failed to open ZIP file.'];
        }

        // Extract the contents to the target directory
        $this->zip->extractTo($extractTo);
        $this->zip->close();

        return ['ok' => true, 'message' => 'ZIP file successfully decompressed.'];
    }
}