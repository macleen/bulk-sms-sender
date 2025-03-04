<?php namespace Boot\Bootstrappers;


use App\Support\Tools;
use App\Interfaces\MiddlewareInterface;
use Boot\Bootstrappers\Bootstrapper;

class LoadHttpMiddleware extends Bootstrapper {

    protected const BASE_NAMESPACE = 'ThisPlugin\\Http\\Middleware';
    protected const RELATIVE_MDW_PATH = 'Http/Middleware';

    public function boot(): void {
        $this->plugin_kernel->bind('middleware', fn() => [
            'global' => $this->scanMiddlewareFolder('Global'),
            'api'    => $this->scanMiddlewareFolder('Api'),
            'web'    => $this->scanMiddlewareFolder('Web'),
            'admin'  => $this->scanMiddlewareFolder('Admin'),
        ]);
        
    }

    /**
     * Scan a folder for PHP classes that implement the MiddlewareInterface
     * @param string $folderPath
     * @return array
     */
    private function scanMiddlewareFolder(string $path): array {
        $middlewareClasses = [];
    
        // Find all PHP files in the given folder
        $files = \glob(this_plugin_path(self::RELATIVE_MDW_PATH."/{$path}") . '/*.php');
        if ( !empty( $files )) {
              foreach ($files as $file) {
                // Include the file to make the class available
                require_once $file;
        
                // Get the class name from the file
                $className = $this->getClassNameFromFile( $file );
                // Ensure the class exists before trying to instantiate it
                if ($className && \class_exists($className)) {
                    if (!\is_subclass_of($className, MiddlewareInterface::class)) {
                        continue;
                    }
                    // Register the middleware in the service container
                    $middlewareClasses[] = $this->plugin_kernel->instance( $className, new $className );
                } else Tools::writeTolog("$className does NOT exist!", 'Error');
              }
         }
        return $middlewareClasses;
    }
    

   
    /**
     * Get the fully qualified class name from the file path.
     * Assumes that the folder structure under Middleware matches the namespace.
     *
     * @param string $filePath The absolute file path of the middleware class.
     * @param string $baseNamespace The base namespace (e.g., "App\Http\Middleware").
     * @return string|null The fully qualified class name, or null if it cannot be determined.
     */
    private function getClassNameFromFile(string $filePath, string $baseNamespace = self::BASE_NAMESPACE ): ?string {
        // Normalize directory separators for consistency
        $filePath = \str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
        
        // Extract the middleware directory path
        $middlewarePath = \str_replace(['/', '\\'], DIRECTORY_SEPARATOR, this_plugin_path( self::RELATIVE_MDW_PATH ));

        // Ensure the file path starts with the expected middleware path
        if (!str_starts_with($filePath, $middlewarePath)) {
            return null;
        }

        // Get the relative path from the middleware directory
        $relativePath = substr($filePath, strlen($middlewarePath) + 1); // +1 to remove the trailing slash

        // Remove the file extension (.php)
        $relativePath = \pathinfo($relativePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($relativePath, PATHINFO_FILENAME);

        // Convert directory separators to namespace separators
        $className = \str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);

        // Construct the full class name
        return \rtrim($baseNamespace, '\\') . '\\' . $className;
    }

}