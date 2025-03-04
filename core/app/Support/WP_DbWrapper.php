<?php namespace App\Support;

use wpdb;
use Illuminate\Support\Collection;

abstract class WP_DbWrapper {
    protected static $table;
    protected wpdb $wpdb;
    protected static $primaryKey      = 'id';
    protected static array $fillables = [ ];    // for mass assignment
    protected static array $guarded    = [ ];    // prevent mass assignment issues
    protected array $attributes       = [ ];


    public function __construct( wpdb &$wpdb, $attributes = [ ]) {
    
        $this->wpdb = $wpdb;
        // Fill only fillable attributes
        foreach ( $attributes as $key => $value ) {
            if ( \in_array($key, static::$fillables )) {
                  $this->attributes[$key] = $value;
            }
        }
    }

    public static function getTable() {
        return self::$wpdb->prefix . static::$table;
    }

    public static function find( $id ) {

        $table = esc_sql(static::getTable());
        $primaryKey = $primaryKey = esc_sql(static::$primaryKey);

        // Generate a unique cache key
        $cache_key = "cache_find_{$table}_{$primaryKey}_{$id}";

        // Try getting cached data first
        $result = wp_cache_get($cache_key, config('app.plugin_unique_id'));
        if ( $result === false) { // If no cache exists, query the database
             $result = self::$wpdb->get_row( self::$wpdb->prepare("SELECT * FROM $table WHERE $primaryKey = %d LIMIT 1", $id), constant('ARRAY_A'));
            // Store the result in cache for future use (cache for 1 hour)
             wp_cache_set( $cache_key, $result, config('app.plugin_unique_id'), 3600 );
        }   


        return $result ? new static($result) : null;
    }


    public function __get($key) {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value) {
        if ( \in_array($key, static::$fillables)) {
              $this->attributes[$key] = is_string( $value ) ? sanitize_text_field( $value ): $value;
        }
    }

    public static function all(): Collection {

        $table   = static::getTable();
        $results = self::$wpdb->get_results("SELECT * FROM $table", constant('ARRAY_A'));

        return collect($results)->map(function ($row) {
            foreach ( static::$guarded as $guardedField ) {
                unset( $row[ $guardedField ]);
            }
            return new static( $row );
        });
    }

    
    public function save(): bool|static {

        $id    = $this->attributes[ static::$primaryKey ] ?? null;
        $table = static::getTable( );

        switch (( bool ) $id ) {

            case true : self::$wpdb->update( $table, $this->attributes, [static::$primaryKey => $this->attributes[static::$primaryKey]]);
                        return ( bool ) $id;

            default   : self::$wpdb->insert( $table, $this->attributes );
                        $id = $this->attributes[ static::$primaryKey ] = self::$wpdb->insert_id;
                        return $id ? self::find( $id ) : false;
        }
    }


    public function delete() {
        $table = static::getTable();
        $primaryKey = static::$primaryKey;

        if ( isset( $this->attributes[ $primaryKey ])) {
            self::$wpdb->delete( $table, [ $primaryKey => $this->attributes[ $primaryKey ]]);
        }
    }    
}