<?php namespace Boot\Services;

use Dotenv\Dotenv;
use App\Support\Tools;
use Dotenv\Exception\InvalidPathException;

class EnvironmentVariablesManager {

    public const ENV_FILE_NAME = '_.env';
    private const UNQUOTED     = ['true', 'false'];

    public function load(): void {   
        if (\file_exists(base_path(self::ENV_FILE_NAME))) {            
            try {
                $env = Dotenv::createImmutable(base_path(), self::ENV_FILE_NAME);
                $env->load();
            } catch (InvalidPathException) {
                // Handle exception if needed
            }
        }    
    }

    private function load_current_values(): array {
        $env_file_path = base_path(self::ENV_FILE_NAME);
        
        if (!file_exists($env_file_path)) {
            return [];
        }
    
        $file_contents = \file($env_file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
        // Split the contents by lines and return each line as an entry
        return Tools::explode_values_by(  $file_contents, '=');
    }

    private function update_environment_variables(array $entries = []): self {

        $current_env_parms = $this->load_current_values();
        $new_env_content   = [];        
        $env_content       = \array_merge( $current_env_parms, $entries );

        foreach( $env_content as $k => $v ){
            $k = \trim($k, " \t\n\r\0\x0B\"");
            $v = \trim($v, " \t\n\r\0\x0B\""); // Trim quotes and spaces
            $v = \in_array( $v, self::UNQUOTED ) || is_numeric($v) ? $v : "\"$v\"";
            $new_env_content[] = "$k=$v";
        }
        
        // Uncomment the following to save the file
        \file_put_contents(
            base_path(self::ENV_FILE_NAME), 
            \implode(PHP_EOL, $new_env_content)
        );
        
        return $this;
    }

    private function clear_env(): self {
        foreach ($_ENV as $key => $_) {
            putenv($key); // Remove from environment variables
            unset($_ENV[$key], $_SERVER[$key]); // Unset from superglobals
        }
        return $this;
    }

    public function update(array $entries = []) {
        if (!empty($entries)) {
            $this->update_environment_variables($entries)
                ->clear_env()
                ->load();
        }           
    }
}