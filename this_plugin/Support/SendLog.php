<?php namespace ThisPlugin\Support;


class SendLog {

    protected const LOG_FILE_PREFIX = 'log_';


    private static function get_header_template( ) {
        return \json_encode([
            'total_number_of_lines' => 0,
            'successful_messages'   => 0,
            'failed_messages'       => 0,
            'created_at'            => \date('H:i:s'),
            'updated_at'            => \date('H:i:s'),
        ]).__LF__;

    }


    public static function write(array $data, bool $success) {

        $logFileName = self::LOG_FILE_PREFIX . \date('d_m_Y') . '.txt';       // Define the log directory and file name based on the current date
        $logFilePath = storage_path( "private/send-log/$logFileName" );
    
        $data['success'] = $success;                                                  // Add the success status to the data array
        $json = \json_encode($data);                                           // Convert the data to JSON
            
        if ( !\file_exists($logFilePath)) {                                 // Check if the log file exists
              \file_put_contents($logFilePath, self::get_header_template()); // Create the log file with an initial header
        }
            
        $logContent = \file_get_contents($logFilePath);                     // Read the existing log file
        $logLines   = \explode(__LF__, $logContent);
    
        $header = json_decode($logLines[0], true);                          // Extract the header (first line)
            $header['total_number_of_lines'] += 1;                          // Update the header
        if ($success) {
            $header['successful_messages'] += 1;
        } else {
            $header['failed_messages'] += 1;
        }
        $header['updated_at'] = date('H:i:s');
    
        $logLines[0] = \json_encode($header);                               // Update the header in the log file
        $logLines[]  = $json;                                               // Append the new log entry
    
        \file_put_contents( $logFilePath, \implode(__LF__, $logLines));     // Write the updated content back to the log file
    }
 
    

    public static function get_log_by_date( string $date ): ?array {

        // Define the log directory and file name based on the requested date
        $logFileName = self::LOG_FILE_PREFIX . str_replace('-', '_', $date) . '.txt'; // Convert date format to match file naming
        $logFilePath = storage_path( "private/send-log/$logFileName" );
        
        if (!\file_exists($logFilePath)) {                          // Check if the log file exists
            return null;                                                      // Return null if the file does not exist
        }
        
        $logContent = \file_get_contents($logFilePath);             // Read the log file content
        $logLines   = \explode(__LF__, $logContent);
        $header     = \json_decode($logLines[0], true);    // Extract the header (first line)

        $data = [];                                                           // Extract the data (all lines except the first)  
        for ($i = 1; $i < count($logLines); $i++) {
            if (!empty($logLines[$i])) {
                $data[] = $logLines[$i];
            }
        }

        return [                                                              // Return the processed content
            'header' => $header,
            'raw_data'   => $data,
        ];
    }

     
    public static function get_available_log_files(): array {
        $logDirectory = storage_path("private/send-log/");
        $logFiles = \glob($logDirectory . self::LOG_FILE_PREFIX . '*.txt');
    
        // Initialize an array to hold file information
        $filesWithDates = [];
    
        // Iterate over each log file
        foreach ($logFiles as $logFile) {
            // Get the file's modification time
            $modificationTime = \filemtime($logFile);
            // Add the file and its modification time to the array
            $filesWithDates[] = [
                'path' => $logFile,
                'modification_time' => $modificationTime,
            ];
        }
    
        // Sort the array by modification time in descending order
        \usort($filesWithDates, function ($a, $b) {
            return $b['modification_time'] <=> $a['modification_time'];
        });
    
        // Initialize the result array
        $result = [];
    
        // Process each sorted log file
        foreach ($filesWithDates as $fileInfo) {
            $logFile = $fileInfo['path'];
            // Extract the date from the file name
            $fileName = \basename($logFile);
            $date = \str_replace(['log_', '.txt'], '', $fileName);
            $date = \str_replace('_', '-', $date); // Convert to date format (e.g., 17-04-2024)
    
            // Read the header (first line) of the log file
            $fileContent = \file_get_contents($logFile);
            $headerLine = \explode(__LF__, $fileContent)[0];
            $header = \json_decode($headerLine, true);
    
            // Add the date and header to the result array
            $result[] = [
                'date' => $date,
                'header' => $header,
            ];
        }
    
        return $result;
    }
    


    public static function delete_log_by_date( string $date ) {

        $res = ['ok' => false, 'message' => '', 'status' => 400];
        $logFileName = self::LOG_FILE_PREFIX . str_replace('-', '_', $date) . '.txt'; // Convert date format to match file naming
        $logFilePath = storage_path( "private/send-log/$logFileName" );
        
        if (\file_exists($logFilePath)) {
             if (unlink($logFilePath)) {
                 $res['ok'] = true;
            } else $res['message'] = "Error deleting the file.";
        } else {
            $res['status']  = 404;
            $res['message'] = "File does not exist.";
        }

        return $res;

    }
}