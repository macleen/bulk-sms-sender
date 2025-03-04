<?php


namespace App\Support;


class RequestInput {
    protected array $attributes;
    protected array $query_params;

    public function __construct( protected \WP_REST_Request $request ) {
        $this->attributes = $request->get_params() ?? [];
        $this->query_params = $request->get_query_params() ?? [];
    }

    public function method(): string{
        return $this->request->get_method( );
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

    public function all()
    {
        return $this->attributes;
    }

    public function __set($property, $value)
    {
        $this->attributes[$property] = $value;
    }

    public function __get($property)
    {
        throw_when(!isset($this->attributes[$property]), "{$property} does not exist on request input");

        return $this->attributes[$property];
    }

    public function __invoke($property) {
        return data_get($this->attributes, $property);
    }

    public function forget($property) {
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

    
}