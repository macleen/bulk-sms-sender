<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Boot\Services\EnvironmentVariablesManager;




function plugin_kernel(): Boot\PluginKernel { 
    return Boot\PluginKernel::get_instance( );
}



if (!function_exists('env')) {
    function env( string $key, mixed $default = false ): mixed {
        $value = getenv( $key );
        throw_when( !$value && !$default, "$key is not a defined ENV variable" );
        return $value ?: $default;
    }
}

if (!function_exists('env_update')) {
    function env_update( string|array $key, mixed $value = '' ): void {
        $data = \is_array( $key ) ? $key :  [$key => $value];
        plugin_kernel()->get( EnvironmentVariablesManager::class )->update( $data );
    }
}



if (!function_exists('admin_view')) {
    // function admin_view( string $template, $data = [], array $meta_data ): void {

    //     $view = container()->resolve( AdminView::class );
    //     echo $view->template( $template )
    //               ->data( $data )
    //               ->page_meta_data( $meta_data )
    //               ->render( );
    // }
}

if (!function_exists('base_path')) {
    function base_path($path = '') {
        return __DIR__."/../{$path}";
    }
}



function plugin_url($path = '') {
    return __PLUGIN_URL__."{$path}";
}

if (!function_exists('this_plugin_path')){
    function this_plugin_path($path = '') {
        return base_path("this_plugin/{$path}");
    }
}


if (!function_exists('assets_path')){
    function assets_path($path = '') {
        return base_path("assets/{$path}");
    }
}


if (!function_exists('database_path')){
    function database_path($path = '') {
        return base_path("database/{$path}");
    }
}

if (!function_exists('storage_path')){
    function storage_path($path = '') {
        return base_path("storage/{$path}");
    }
}

if (!function_exists('public_path')){
    function public_path($path = '') {
        return base_path("public/{$path}");
    }
}

if (!function_exists('resources_path')){
    function resources_path($path = '') {
        return base_path("resources/{$path}");
    }
}

if (!function_exists('routes_path')) {
    function routes_path($path = '') {
        return base_path("routes/{$path}");
    }
}



if (!function_exists('config_path')) {
    function config_path($path = '') {
        return base_path("config/{$path}");
    }
}

// print and die
if (!function_exists('pd')) { 
    function pd(mixed ...$vars): never {
        header('Content-Type: text/plain');
        print_r( $vars );
        die( );
    }
}

// dump and die
if (!function_exists('dd')) {
    function dd()  {
        array_map(function ($content) {
            echo "<pre>";
            print_r($content);
            echo "</pre>";
            echo "<hr>";
        }, func_get_args());

        die;
    }
}

if (!function_exists('throw_when')) {
    function throw_when(bool $fails, string $message, string $exception = \Exception::class) {
        if (!$fails) return;

        throw new $exception(esc_html($message));
        
    }
}

if (! function_exists('class_basename')) {
    function class_basename($class): string {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }
}


if (!function_exists('config')) {
    function config($path = null, $value = null)    {
        $config = plugin_kernel()->get('config') ?? [];

        if (is_null($value)) {
            return data_get($config, $path);
        }

        data_set($config, $path, $value);
        plugin_kernel()->bind('config', fn()=> $config);
    }
}

if (! function_exists('class_basename')) {
    function class_basename($class) {
        $class = \is_object($class) ? \get_class($class) : $class;
        return \basename(\str_replace('\\', '/', $class));
    }
}


if (! function_exists('data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed  $target
     * @param  string|array|int|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    function data_get($target, $key, $default = null) {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        while (! is_null($segment = array_shift($key))) {
            if ($segment === '*') {
                if ($target instanceof Collection) {
                    $target = $target->all();
                } elseif (! is_array($target)) {
                    return value($default);
                }

                $result = [];

                foreach ($target as $item) {
                    $result[] = data_get($item, $key);
                }

                return in_array('*', $key) ? Arr::collapse($result) : $result;
            }

            if (Arr::accessible($target) && Arr::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }
}

if (! function_exists('data_set')) {
    /**
     * Set an item on an array or object using dot notation.
     *
     * @param  mixed  $target
     * @param  string|array  $key
     * @param  mixed  $value
     * @param  bool  $overwrite
     * @return mixed
     */
    function data_set(&$target, $key, $value, $overwrite = true) {
        $segments = is_array($key) ? $key : explode('.', $key);

        if (($segment = array_shift($segments)) === '*') {
            if (! Arr::accessible($target)) {
                $target = [];
            }

            if ($segments) {
                foreach ($target as &$inner) {
                    data_set($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (Arr::accessible($target)) {
            if ($segments) {
                if (! Arr::exists($target, $segment)) {
                    $target[$segment] = [];
                }

                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || ! Arr::exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (! isset($target->{$segment})) {
                    $target->{$segment} = [];
                }

                data_set($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || ! isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];

            if ($segments) {
                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }

        return $target;
    }

    function is_selected( $current_value, $selection_value ): string {
        return $current_value == $selection_value ? ' selected ': '';
    }
    
 
    function writeTolog( mixed $data ) {
        $separator = str_pad('',80, '*').__CRLF__;
        $log_line = '';        
        $data_type = 'data-type: ';
        switch ( true ) {
            case is_string( $data )  : $dt = 'String'.__CRLF__;
                                              $log_line = $data.__CRLF__;
                                              break;
            case is_array( $data )   : $dt ='Array' ;
                                              $log_line = \json_encode( $data ).__CRLF__;
                                              break;
            case is_integer( $data ) : $dt ='Integer'.__CRLF__ ;
                                              $log_line = $data.__CRLF__;
                                              break;
            case is_object( $data )  : $dt ='Object'.__CRLF__ ;
                                              $log_line = \serialize( $data ).__CRLF__;
                                              break;
            default                         : $dt = 'Unknown.'.__CRLF__;
                                              $log_line = \serialize( $data ).__CRLF__;
        };
        error_log( "$separator $data_type $dt $log_line $separator" );
    }


}