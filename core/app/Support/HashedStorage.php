<?php namespace App\Support;

use Exception;
use RuntimeException;
use App\Support\Tools;
use ThisPlugin\Support\SmsClickTracker;
use ThisPlugin\DataHandlers\Payload\Payload;

class HashedStorage {
    protected const LOG_FILE_PREFIX = 'log_';
    protected const LOG_DIRECTORY = 'private/hash-base';
    protected const MAP_FILE = 'map.json';
    protected string $logDirectory;
    protected string $mapFilePath;

    public function __construct() {
        $this->logDirectory = rtrim(storage_path(self::LOG_DIRECTORY), '/') . '/';
        $this->mapFilePath = $this->logDirectory . self::MAP_FILE;

        // Ensure the log directory exists
        if (!is_dir($this->logDirectory)) {
            mkdir($this->logDirectory, 0777, true);
        }
    }

    public function write(array $data): string {
        $logFileName = self::LOG_FILE_PREFIX . date('d_m_Y') . '.bin';
        $logFilePath = "{$this->logDirectory}{$logFileName}";

        // Initialize the log file if it doesn't exist
        if (!file_exists($logFilePath)) {
            $this->writeFile($logFilePath, gzcompress(''));  // Empty file created
            $this->updateMap($logFilePath);
        }

        // Read the log file
        $logContent = gzuncompress($this->readFile($logFilePath));

        // Split the log content into lines (preserve empty lines)
        $logLines = explode(PHP_EOL, $logContent);

        // Determine the next line number
        $lineNumber = count($logLines) + 1;

        // Add the new entry
        $logLines[] = json_encode($data);

        // Write the updated content back to the file
        $this->writeFile($logFilePath, gzcompress(implode(PHP_EOL, $logLines)));

        // Generate and return the reference
        $map = $this->readMap();
        $reference = array_search($logFilePath, $map, true);
        return "$reference-$lineNumber";
    }

    public function has(string $reference): bool {
        [$fileRef, $lineNumber] = explode('-', $reference);

        $map = $this->readMap();

        if (!isset($map[$fileRef])) {
            return false;
        }

        $logFilePath = $map[$fileRef];

        if (!file_exists($logFilePath)) {
            return false;
        }

        $logContent = gzuncompress($this->readFile($logFilePath));
        $logLines = explode(PHP_EOL, $logContent);

        return isset($logLines[$lineNumber - 1]);
    }

    public function getDataByReference(string $reference): ?array {
        [$fileRef, $lineNumber] = explode('-', $reference);
        $logLines = $this->getFileContent($fileRef);

        if (!$logLines || !isset($logLines[$lineNumber - 1])) {
            return null;
        }

        return json_decode($logLines[$lineNumber - 1], true);
    }

    public function updateReference(string $reference, array $updatedData): array {

        $res = ['ok' => true, 'message' => '', 'status' => 200, 'data' => [ ]];
        [$fileRef, $lineNumber] = explode('-', $reference);
        $map = $this->readMap();

        if (!isset($map[$fileRef])) {
            return ['ok' => false, 'message' => "File reference not found in map: $fileRef", 'status' => 404, 'data' => [ ]];
        }

        $logFilePath = $map[$fileRef];
        try {
            $logContent = gzuncompress($this->readFile($logFilePath));          // Read the log file
            $logLines = explode(PHP_EOL, $logContent);                   // Split the log content into lines (preserve empty lines)
            
            if (!isset($logLines[$lineNumber])) {                                            // Ensure the specified line number exists in the log file
                return ['ok' => false, 'message' => "Hash Index $lineNumber not found in dump file", 'status' => 404, 'data' => [ ]];
            }
          
            $currentData = json_decode($logLines[$lineNumber], true);     // Decode the existing data
            if ($currentData === null) {                                                     // If the line is empty or invalid JSON, log an error and return false
                return ['ok' => false, 'message' => "Invalid JSON data at index $lineNumber", 'status' => 400, 'data' => [ ]];
            }
           
            $mergedData = array_merge($currentData, $updatedData);                   // Merge the existing data with the updated data
            $logLines[$lineNumber] = json_encode($mergedData);                        // Update the specific entry in the array
            
            $updatedContent = implode(PHP_EOL, $logLines);                 // Write the updated content back to the file
            $this->writeFile($logFilePath, gzcompress($updatedContent));

            return $res;
        } catch (Exception $e) {
            return ['ok' => false, 'message' => "Error updating reference: $reference", 'status' => 400, 'data' => [ ]];
        }
    }

    public function deleteFileByReference(string $fileReference): bool {
        $map = $this->readMap();

        if (!isset($map[$fileReference])) {
            return false;
        }

        $logFilePath = $map[$fileReference];
        if (file_exists($logFilePath) && unlink($logFilePath)) {
            unset($map[$fileReference]);
            $this->writeFile($this->mapFilePath, json_encode($map));
            return true;
        }

        return false;
    }

    public function deleteFileByPath(string $path): array {
        $res = ['ok' => true, 'message' => '', 'status' => 200 ];
        $map = $this->readMap();

        // Find the reference associated with the given path
        $fileReference = array_search($path, $map, true);

        if (!$fileReference) {
            return ['ok' => false, 'message' => 'File reference not found', 'status' => 404 ];
        }

        // Check if the file exists and delete it
        if (file_exists($path) && unlink($path)) {
            unset($map[$fileReference]);
            $this->writeFile($this->mapFilePath, json_encode($map));
            return $res;
        }

        return ['ok' => false, 'message' => 'File not found', 'status' => 404 ];
    }

    public function listAllFiles(): array {
        $files = glob($this->logDirectory . self::LOG_FILE_PREFIX . '*.bin');
        $fileDetails = [];
    
        // Sort files by modification time in descending order
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
    
        foreach ($files as $filePath) {
            $fileNameWithExtension = basename($filePath);
            $fileName = substr($fileNameWithExtension, strlen(self::LOG_FILE_PREFIX), -4); // Removing the prefix and extension
    
            // Get file size
            $fileSize = filesize($filePath);
    
            // Get last modified time
            $fileModifiedTime = date('H:i:s', filemtime($filePath));
    
            // Add detailed information to the result
            $fileDetails[] = [
                'file' => str_replace('_','-',$fileName),
                'path' => $filePath,
                'size' => "$fileSize bytes",
                'updated_at' => $fileModifiedTime,
            ];
        }
    
        return $fileDetails;
    }
    

    public function getFileContent(string $fileReference): ?array {
        $map = $this->readMap();

        if (!isset($map[$fileReference])) {
            return null;
        }

        $logFilePath = $map[$fileReference];
        $logContent = gzuncompress($this->readFile($logFilePath));
        return explode(PHP_EOL, $logContent);
    }

    public function getFileContentByPath(string $path): ?array {
        $res = ['ok' => false, 'message' => '', 'status' => 404, 'data' => ''];

        // Check if the file exists at the given path
        if (!file_exists($path)) {
            $res['message'] = 'File not found';
            return $res;
        }

        // Get the file content and decompress if it's gzipped
        $logContent = gzuncompress($this->readFile($path));

        // Debugging: Check if decompressed content is empty
        if (empty($logContent)) {
            $res['message'] = 'File content is empty or could not be decompressed';
            return $res;
        }

        // Split the content into lines
        $lines = explode(PHP_EOL, $logContent);

        // Initialize an empty result array
        $result = [];

        // Read the map.json file to get the key (hash) for this file
        $map = $this->readMap();  // Assuming this reads the map.json and returns an array

        // Find the matching key in the map for the current file
        $key = null;
        foreach ($map as $fileReference => $filePath) {
            // Debugging: Check if the map paths are matching the file path
            if ($filePath === $path) {
                $key = $fileReference;  // This is the key that maps to the current file
                break;
            }
        }

        // If no matching key is found, log and return error message
        if ($key === null) {
            $res['message'] = 'Hash Key not found';
            return $res;  // Return error if the key is not found in the map
        }

        // Check if there are any lines to process
        foreach ($lines as $index => $line) {
            // Skip empty lines
            if (empty($line)) {
                continue;
            }

            // Assign the line's content to the result array with the unique key
            $json = json_decode($line, true);
            $json['index'] = "$key-$index";
            $result[] = $json;
        }

        // Return the result in the desired format
        $res['ok'] = true;
        $res['status'] = 200;
        $res['data'] = $result;
        return $res;
    }

    protected function updateMap(string $logFilePath): void {
        $map = $this->readMap();
        $reference = 'a' . base_convert(crc32($logFilePath), 10, 36);
        $map[$reference] = $logFilePath;
        $this->writeFile($this->mapFilePath, json_encode($map));
    }

    protected function readMap(): array {
        if (!file_exists($this->mapFilePath)) {
            return [];
        }

        return json_decode($this->readFile($this->mapFilePath), true) ?? [];
    }

    protected function readFile(string $path): string {
        $content = file_get_contents($path);
        if ($content === false) {
            throw new RuntimeException("Failed to read file: $path");
        }
        return $content;
    }

    protected function writeFile(string $path, string $data): void {
        if (file_put_contents($path, $data) === false) {
            throw new RuntimeException("Failed to write file: $path");
        }
    }

    public static function update_meta_data(string $index): array {
        $res = ['ok' => true, 'message' => '', 'status' => 200, 'data' => ''];
        $pk = \Boot\PluginKernel::get_instance();
        $self = $pk->has(self::class) ? $pk->get(self::class) : null;

        if ($self) {
            if ($self->has($index)) {
                $res = $self->updateReference($index, [Payload::HASHED_LOG_ENTRY_KEY_2 => SmsClickTracker::capture_click_data()]);
                if ($res['ok']) {
                    $res['data'] = Tools::flatten_array_withKeys($self->getDataByReference($index));
                }
            } else {
                $res = [
                    'ok' => false,
                    'message' => "Hashed-dump at index: $index not found",
                    'status' => 404,
                ];
            }
        } else {
            $res = [
                'ok' => false,
                'message' => 'HashedStorage engine is not loaded',
                'status' => 404,
            ];
        }

        return $res;
    }
}