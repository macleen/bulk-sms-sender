<?php

namespace App\Support;

use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;


class Tools {


    public static function ip( ): string {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // When behind a proxy or load balancer
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            // Fallback to REMOTE_ADDR if X-Forwarded-For is not set
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = 'unknown';
        }
        
        // Handle the case where multiple IPs are in X-Forwarded-For
        if (strpos($ip, ',') !== false) {
            $ip = explode(',', $ip)[0];
        }
    
        return $ip;
    }

    public static function trim_trailing_slash( string $path ): string {

        // Check if the path has a trailing slash and isn't the root path
        if ( $path !== '/' && \substr($path, -1) === '/')
             $path = \rtrim($path, '/');

        return $path;
    }            


    public static function get_sorted_folder_files( $path, $post_fix = NULL, $sort = 'DESC' ) {

        $output = [];
        $temp   = [];
        $sort   = in_array( $sort, ['ASC','DESC']) ? $sort : 'DESC';
        
        foreach (new \DirectoryIterator( $path ) as $fileInfo) {
             if ( $fileInfo->isDot() || !$fileInfo->isFile()) continue;
                  $temp[] = array( gmdate('d-m-Y H:m:s',$fileInfo->getMTime()), $fileInfo->getFilename() );
        }

        usort ( $temp, function( $a, $b ) use ($sort) {
        return ( $sort == 'DESC' ? ( int ) ($a[0] < $b[0]) : ( int ) ($a[0] > $b[0]));
        });

        foreach (array_values( $temp ) as $data ) {
             $count_it = empty( $post_fix );
             $new_file_name = $data[1];

             $pos = strpos($data[1], $post_fix.'.php');
             if ( $pos ) {
                  $count_it      = substr( $data[1], $pos ) == $post_fix.'.php';
                  $file_name     = $count_it ? substr( $data[1], 0, $pos) : NULL;
                  $new_file_name = $file_name && ($file_name != 'Base') ? $file_name : NULL;
             }
             
             if ( $count_it && $new_file_name ) $output[] = $new_file_name;
             
        }
        
        return $output;
        
   }


    public static function is_associative_array( array $array ): bool {
        foreach (array_keys($array) as $key) {
            if (!\is_int($key )) {
                return true; // Found a non-integer key
            }
        }
        return false; // All keys are integers
    }

    public static function is_numeric_array( array $array ): bool {
        return \array_keys($array) === range(0, \count($array ) - 1 );
    }


    public static function get_array_slice_after_key( array $array, string $key ): array {

        $key_index = \array_search( $key, \array_keys( $array ));
        // Slice the array from that position
        if ( $key_index !== false) {
             $array = \array_slice( $array, $key_index+1, null, true );
        }
        return $array;

    }


    /**
     * Generate a reusable slug for WordPress projects.
     *
     * @param string $string The input string to convert into a slug.
     * @param string $prefix Optional prefix (e.g., plugin name) to ensure uniqueness.
     * @param int|null $max_length Maximum length of the slug (default: 50).
     * @return string The generated slug.
     */
    public static function generate_wp_slug( string $string, ?string $prefix = null, $max_length = 50 ): string {
        // Convert to lowercase
        $slug     = \strtolower($string);
        $prefix ??=  config('app.plugin_unique_id');
        // Replace spaces and special characters with dashes
        $slug = \preg_replace('/[^a-z0-9]+/', '-', $slug);

        // Remove extra dashes from the beginning and end
        $slug = \trim($slug, '-');

        // Apply prefix if provided
        $slug = !empty($prefix) ? "{$prefix}-{$slug}" : $slug;
        
        // Limit slug length to avoid database issues and return it
        return sanitize_title(
                $max_length > 0
                    ? \substr( $slug, 0, $max_length )
                    : $slug
               );
    }

    public static function generate_asset_handle( $string, $prefix = '', $max_length = 50 ): string {
        return self::generate_wp_slug( $string, 'handle_'.$prefix, $max_length );
    }

    public static function generate_db_cache_group( $prefix = '', $max_length = 50 ): string {
        return self::generate_wp_slug( 'cache',$prefix, $max_length );
    }


    public static function writeTolog( mixed $data, string $label = '' ) {
        $separator = str_pad('',80, '*').__CRLF__;
        $log_line = $label.': '.__CRLF__;
        $data_type = 'data-type: ';
        switch ( true ) {
            case $data === null             : $dt = 'NULL'.__CRLF__;
                                              $log_line .= 'A null value'.__CRLF__;
                                              break;
            case is_string( $data )  : $dt = 'String'.__CRLF__;
                                              $log_line .= $data.__CRLF__;
                                              break;
            case is_array( $data )   : $dt ='Array'.__CRLF__;
                                              $log_line .= \json_encode( $data, JSON_PRETTY_PRINT ).__CRLF__;
                                              break;
            case is_integer( $data ) : $dt ='Integer'.__CRLF__ ;
                                              $log_line .= $data.__CRLF__;
                                              break;
            case is_bool( $data )    : $dt ='Boolean'.__CRLF__ ;
                                              $log_line .= ( $data ? 'true' : 'false').__CRLF__;
                                              break;
            case is_object( $data )  : $dt ='Object'.__CRLF__ ;
                                              //$log_line .= \json_encode( get_object_vars( $data ), JSON_PRETTY_PRINT ).__CRLF__;
                                              $log_line .= print_r($data, true).__CRLF__; // Dump object properties
                                              break;
            default                         : $dt = 'Unknown.'.__CRLF__;
                                              $log_line .= \json_encode( $data, JSON_PRETTY_PRINT ).__CRLF__;
        };
        file_put_contents(__PLUGIN_PATH__.'___LOG___.txt', __CRLF__."$separator $data_type $dt Content: $log_line $separator".__CRLF__, FILE_APPEND );
        // error_log( __CRLF__."$separator $data_type $dt Content: $log_line $separator".__CRLF__ );
    }


    public static function get_array_element( $element, array $array, $default = null ) {
        return array_key_exists( $element, $array ) ? $array[ $element ] : $default;
    }


    public static function copy_recursive($source, $destination) : array {
        $res = ['ok' => false, 'message' => ''];
        try {
            // Ensure the source exists
            if (!\file_exists($source)) {
                $res['message'] = 'Can not overwrite an existing folder';
                return $res;
            }
        
            // Open the source directory
            $dir = \opendir($source);
        
            // Loop through the contents of the directory
            while (false !== ($file = readdir($dir))) {
                if ($file !== '.' && $file !== '..') {
                    $srcPath = $source . DIRECTORY_SEPARATOR . $file;
                    $destPath = $destination . DIRECTORY_SEPARATOR . $file;
        
                    if (\is_dir($srcPath)) {
                        // Only create the folder if it does NOT exist
                        if (!\file_exists($destPath)) {
                            \mkdir($destPath, 0755, true);
                        }
                        // Recursively copy contents
                        $res = self::copy_recursive($srcPath, $destPath);
                    } else {
                        // Copy files, overwrite if they exist
                        \copy($srcPath, $destPath);
                    }
                }
            }
        
            \closedir($dir);
            $res['ok'] = true;

        } catch( \Exception $e ) {
            $res['message'] = $e->getMessage();
        }
        return $res;
        
    }

    
    public static function move_files($source, $destination): array {

        $res = ['ok' => false, 'message' => ''];

        // Check if source exists
        if (!is_dir($source)) {
            $res['message'] = "Source folder doesn't exist.";
            return $res;
        }
    
        // Create destination folder if it doesn't exist
        if (!is_dir($destination)) {
             if ( !mkdir($destination, 0777, true)){
                   $res['message'] = "COuld not create directory $destination";
                   return $res;
             }
        }
    
        // Iterate through the source folder
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
    
        foreach ($iterator as $fileinfo) {
            $sourcePath = $fileinfo->getRealPath();
            $relativePath = substr($sourcePath, strlen($source));
            $destPath = $destination . $relativePath;
    
            // If it's a file, move it (overwrite if exists)
            if ($fileinfo->isFile()) {
                // Create the folder in the destination if it doesn't exist
                $destFolder = dirname($destPath);
                if (!is_dir($destFolder)) {
                     if ( !mkdir($destFolder, 0777, true)) {
                        $res['message'] = "COuld not create destination directory $destFolder";
                        return $res;
                     }
                }
    
                // Move the file (overwrite if exists)
                if (!rename($sourcePath, $destPath)) {
                    $res['message'] = "Failed to move $sourcePath to $destPath";
                    return $res;
                }
            } 
            // If it's a directory, only create it if it doesn't exist
            elseif ($fileinfo->isDir() && !is_dir($destPath)) {
                mkdir($destPath, 0777, true);
            }
        }
        $res['ok'] = true;
        return $res;
    }
        
    
    

    public static function clean_up_directory(string $directory): void {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
    }



    public static function normalize_path( string $path, string $trim = '' ) {

        $trim = strtoupper( $trim );
        $path = \str_replace(['/','\\'], DIRECTORY_SEPARATOR, $path );

        return match( $trim ) {
            'LEFT'  => \ltrim( $path , DIRECTORY_SEPARATOR ),
            'RIGHT' => \rtrim( $path , DIRECTORY_SEPARATOR ),
            'BOTH'  => \trim ( $path , DIRECTORY_SEPARATOR ),
            default => $path,
        };
    }


    public static function delete_directory($dir): bool {
        if (!file_exists($dir)) {
            return false;
        }
    
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $filePath = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($filePath) ? self::delete_directory($filePath) : unlink($filePath);
        }
    
        return rmdir($dir);
    }


    public static function explode_values_by( array $array, string $char = ' ', bool $with_trimming = true): array {
        $res = [];
        $array = \array_filter( $array, fn( $v ) => !empty( $v ));

        foreach( $array as $value ){
            [ $k, $v] = \explode( $char, $value, 2 );
            $res[ $k ] = $with_trimming ? \trim($v, " '\t\n\r\0\x0B\"") : $v;
        }
        return $res;
    }


    public static function get_classes_from_folder( string $folder, string $namespace) {

        $classes = [];        
        $files   = \glob("$folder/*.php");             // Scan the folder for PHP files

        foreach ($files as $file) {
            
            $className = basename($file, '.php'); // Get the class name from the file                
            $fullClassName = "$namespace$className";            // Check if the class is in the 'Greeter' namespace
            
            if (class_exists($fullClassName)) {
                $classes[] = [
                               'file_name' => "$folder/$className.php",
                               'fqn'       => $fullClassName,  // namespaced
                             ];
            }
        }
        
        return $classes;
    }

    public static function click_info( ) {
        return [
                'ip' => self::ip( ),
                'time' => date('H:i:s'),
                'date' => date('d-m-Y'),
                'user-agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'NA',
                'prefered-language'=> $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'NA',
                
        ];
    }

    public static function flatten_array(array $array): array {
        $result = [];
        array_walk_recursive($array, function($value) use (&$result) {
            $result[] = $value;
        });
        return $result;
    }
    
    public static function flatten_array_withKeys(array $array, $prefix = ''): array {
        $result = [];
        foreach ($array as $key => $value) {
            $newKey = $prefix . $key;
            if (is_array($value)) {
                $result += self::flatten_array_withKeys($value, $newKey . '.');
            } else {
                $result[$newKey] = $value;
            }
        }
        return $result;
    }

    public static function submit_form_with_redirect($url, $data) {
        // phpcs:ignore Squiz.PHP.Heredoc.NotAllowed
        $formHtml = <<<HTML
        <form id="redirectForm" action="{$url}" method="POST" style="display: none;">
        HTML;
        // phpcs:ignore Squiz.PHP.Heredoc.NotAllowed    
        foreach ($data as $key => $value) {
            $formHtml .= <<<HTML
            <input type="hidden" name="{$key}" value="{$value}">
            HTML;
        }
        // phpcs:ignore Squiz.PHP.Heredoc.NotAllowed    
        $formHtml .= <<<HTML
        </form>
        <script>
            document.getElementById('redirectForm').submit();
        </script>
        HTML;    
        echo $formHtml;
    }
    
    
}