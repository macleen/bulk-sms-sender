<?php namespace App\Http\Request;

use App\Support\Tools;


class Request {
    public const NON_GET_REQUESTS = ['POST', 'PUT', 'PATCH'];
    protected array $attributes;
    protected array $query_params;

    public function __construct( protected \WP_REST_Request $request ) {
        $this->attributes = $request->get_params() ?? [];
        $this->query_params = $request->get_query_params() ?? [];
    }

    public function method(): string{
        return \strToUpper( $this->request->get_method( ));
    }

    public function headers(): array{
        return $this->request->get_headers( );
    }


    public function header( string $key ): string{
        return $this->request->get_header( $key );
    }

    public function user(  ): \WP_User {
        return wp_get_current_user(  );
    }


    public function file( ): array{
        //This is only used for multipart/form-data requests.
        return $this->request->get_file_params(  );
    }

    public function route( ): string{
        //This is only used for multipart/form-data requests.
        return $this->request->get_route(  );
    }

    public function origin( ): string{
        //This is only used for multipart/form-data requests.
        return get_http_origin( );
    }

    public function all(): array {
        return $this->attributes;
    }

    public function change_key_name( $old_key_name, $new_key_name ): self {

        if ( $new_key_name && isset( $this->attributes[$old_key_name] )) {
             $this->attributes[$new_key_name] = $this->attributes[$old_key_name];
             $this->forget( $old_key_name );
        }
        return $this;
    }         

    public function __set($property, $value): void {
        $this->attributes[$property] = $value;
    }

    public function __get($property): mixed {
        return $this->attributes[$property] ?? null;
    }

    public function __invoke($property): mixed {
        return data_get($this->attributes, $property);
    }

    public function forget($property): self {
        unset($this->attributes[$property]);

        return $this;
    }

    public function merge($array) {
        array_walk($array, fn ($value, $key) => data_set($this->attributes, $key, $value));
        return $this;
    }

    public function fill($array) {
        array_walk($array, fn ($value, $key) => data_fill($this->attributes, $key, $value));
        return $this;
    }

    public function ip() {
        return Tools::ip( );        
    }
    

    public function get_route_params(): array{
        return $this->request->get_url_params();
    }

    /**
     * getting the url back depends on where you are asking for it, there 3 types
     * REST API requests: Use $request->get_route()
     * Frontend or general requests: Use $_SERVER variables or $wp->request
     * Admin panel requests: Use admin_url()
     * $env = ADMIN|REST|null for frontend
     * @param string $env
     */

    public function url( ?string $env = null ){
        $env = strtoupper(( string ) $env );
        $rest_url = function( ){
                        $url = $this->request->get_route(); // Get the route of the REST request
                        return home_url( $url );
                    };
        $standard = function(){
                        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
                        $host = $_SERVER['HTTP_HOST'];
                        $request_uri = $_SERVER['REQUEST_URI'];
                        return "$scheme://$host$request_uri";
                    };

        return match( $env ) {
           'ADMIN'  => admin_url( add_query_arg( null, null )),
           'REST'   => $rest_url( ),
            default => $standard( ),
        };
    }

    public function url_contains( $needle ): bool {
        return strpos($this->url( ), $needle) !== false;
    }

    
    public function from_local_server( ) {
        $url = $this->url( );
        $url_path = parse_url($url);
        $host = $url_path['host'];
        return $host == "localhost" || $host == "127.0.0.1";
    } 
}