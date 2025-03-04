<?php namespace App\Routing;

use App\Support\Tools;
use Boot\PluginKernel;
use Illuminate\Support\Str;
use App\Http\Request\Request;
use App\Http\Middleware\Middleware;


class Route {

    public static string $group_prefix = '';
    public static array $middleware = [];
    public static array $registered_routes = [];  // Add this property to hold registered routes
    public string $prefix = '';

    public function __construct(private string $verb, private string $route, private $action) { }

    public static function __callStatic($verb, $parameters) {
        [$route, $action] = $parameters;
        self::validate($verb, $route, $action);
        
        $original_route = $route; // Store the original {param} route
        $converted_route = self::convert_placeholders($route);
    
        $newRoute = new self($verb, $converted_route, $action);
        $newRoute->prefix = self::$group_prefix;
        
        // Store both versions in an associative array
        self::$registered_routes[] = [
            'original'  => $original_route,
            'converted' => $converted_route,
            'instance'  => $newRoute,
        ];
    
        return $newRoute;
    }

    public static function group(string $group_prefix, array $middleware = [], ?callable $route_definitions = null): RouteGroup {
        // Merge parent middleware with the new group middleware
        $middleware = array_merge(self::$middleware, $middleware);
    
        $group = new RouteGroup($group_prefix, $middleware, $route_definitions);
        $group->setup();
        return $group;
    }
        

    public function middleware($middlewareClass): self {
        self::$middleware[] = $middlewareClass;
        return $this;
    }

         
    public static function register_all_routes(array $middleware = []) {
        
        add_action('rest_api_init', function () use ($middleware) {
            foreach (self::$registered_routes as $routeData) {
                $routeInstance = $routeData['instance'];

                // Convert placeholders: {param} -> (?P<param>[\w-]+)
                $WP_routeWithPrefix_format = self::convert_placeholders(
                                        \rtrim($routeInstance->prefix, '/') . '/' . \ltrim($routeData['original'], '/')
                                   );
                register_rest_route(config('routing.package_rest_api_name_space'), $WP_routeWithPrefix_format, [
                    'methods'  => $routeInstance->verb,
                    'callback' => function (\WP_REST_Request $request) use ($routeInstance, $middleware ) {
                        $request = new Request($request);
                        return Middleware::apply_middleware(
                            $middleware,
                            function() use ( $routeInstance, $request ) {
                                return self::handle_callback($routeInstance->action, $request);
                            },
                            $request,
                            $routeInstance->prefix
                                                        // \str_replace('/', '', $routeInstance->prefix)
                        );
                    },
                    'permission_callback' => '__return_true',
                ]);
                
            }
        });
    }
    
           

    public static function handle_callback( $action, Request $request ) {
     
        PluginKernel::get_instance()->instance( Request::class, $request );
        $parameters = \array_merge(\array_values($request->get_route_params()), [$request]);
        switch ( !!1 ) {
            case \is_callable($action):
                return $action(...$parameters);
            case \is_string($action):
                [$controller, $method] = self::resolve_via_controller($action);
                return $controller->$method(...$parameters);
            default:
                return new \WP_Error('invalid_callback', 'Invalid callback provided.', ['status' => 500]);
        }
    }

    public static function resolve_via_controller(string $action): array {
        $class      = Str::before($action, '@');
        $method     = Str::after($action, '@');
        $static     = config('routing.controllers.namespace') . $class;
        $controller = PluginKernel::get_instance()->make( $static );
        return [$controller, $method];
    }

    private static function convert_placeholders(string $route): string {
        return preg_replace('/\{([\w-]+)\}/', '(?P<$1>[\w-]+)', $route);
    }
    

    private static function validate(string $verb, string $route, callable|string $action): bool|\WP_Error {
        $e            = "Unresolvable route callback/controller action: " . \json_encode(compact('route', 'action', 'verb'));
        $fails        = !( \is_callable($action) || ( \is_string($action) && Str::is('*@*', $action)));
        return $fails ? new \WP_Error(500, $e) : true;
    }


}