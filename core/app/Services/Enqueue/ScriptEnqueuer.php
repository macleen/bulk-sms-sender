<?php namespace App\Services\Enqueue;

use App\Support\Tools;
use Illuminate\Support\Str;
use App\Services\Enqueue\Localizer;

class ScriptEnqueuer {


    private const PLACE_SCRIPTS_IN_FOOTER = true;
    protected string  $user_type      = '';
    protected ?string $css_slug       = null;
    protected ?string $js_slug        = null;
    protected ?string $user_css_path  = null;
    protected ?string $user_js_path   = null;
    protected ?string $admin_css_path = null;
    protected ?string $admin_js_path  = null;    
    


    public function __construct( protected Localizer $localizer ){ }
    
    public function init( ): self {
        $this->set_slugs( )
             ->set_paths( )
             ->set_user_type( );
        return $this;             
    }

   protected function set_paths(): self {
        $this->user_css_path  = assets_path('/user/css/');
        $this->user_js_path   = assets_path('/user/js/');
        $this->admin_css_path = assets_path('/admin/css/');
        $this->admin_js_path  = assets_path('/admin/js/');

        return $this;
    }

    
    protected function set_user_type( ): self {
        $this->user_type = is_admin() ? __USER_TYPE_ADMIN__ : __USER_TYPE_USER__;
        return $this;
    }

    protected function get_uri( string $asset_id, string $asset_type ): string {
        return Str::startsWith( $asset_id, 'http') ? $asset_id
           : __PLUGIN_URL__."assets/{$this->user_type}/{$asset_type}/$asset_id";
    }

    private function set_slugs(): self {
        $this->css_slug = config('app.prefix').'_'.__ENQUEUE_TYPE_STYLES__;
        $this->js_slug  = config('app.prefix').'_'.__ENQUEUE_TYPE_SCRIPTS__;
        return $this;
    }


     /**
     * The actual function that is called by WP
     */

    public function enqueue_assets_per_user_type() {

        $assets = config("enqueue.{$this->user_type}");
        if ($assets) {
            $this->enqueue_assets( $assets );
        }
    }

    protected function enqueue_assets( array $assets ) {
        foreach ($assets as $asset_type => $entries) {
            if (is_array($entries) && !empty($entries)) {
                match ($asset_type) {
                    __ENQUEUE_TYPE_STYLES__  => $this->enqueue_styles( $entries ),
                    __ENQUEUE_TYPE_SCRIPTS__ => $this->enqueue_scripts( $entries ),
                    default                  => null,
                };
            }
        }
    }

    protected function enqueue_styles( array $entries ) {
        foreach ($entries as $enqueue_data) {
            $file_owner = $enqueue_data['owner'] ?? null;

            switch ( $file_owner ) {
                case __WP__ : wp_enqueue_style( $enqueue_data['id' ]); break;
                default     : $file = $this->get_uri( $enqueue_data['id'], 'css');
                            $enqueue_data[__SCRIPT_HANDLE_LTR__] = Tools::generate_asset_handle( $enqueue_data['id' ], $this->css_slug );
                            wp_enqueue_style($enqueue_data[__SCRIPT_HANDLE_LTR__], $file, [], $enqueue_data['version'] ?? '1.0.0');
                            break;
            }
        }
    }

    protected function enqueue_scripts(  array $entries ) {

        $entries = $this->create_entries_handle( $entries );
        foreach ($entries as $enqueue_data) {

            $file_owner = $enqueue_data['owner'] ?? null;
            switch( $file_owner ) {
                case __WP__: wp_enqueue_script( $enqueue_data['id' ]); break;
                default    : $file = $this->get_uri( $enqueue_data['id'], 'js');
                             $meta = [ $enqueue_data[__SCRIPT_HANDLE_LTR__], $file, $enqueue_data['dependency'] ?? [], $enqueue_data['version'] ?? '1.0.0', self::PLACE_SCRIPTS_IN_FOOTER ];
                             wp_enqueue_script(...$meta);
                             // Apply localization with dynamic values
                             $this->localizer->localize_script($enqueue_data);
                             $this->load_as_es6_module_when_required( $enqueue_data );
                             break;
            }
        }

    }


    /**
     * The actual function that is calling WP action
     */

    public function enqueue( ): self {
        $hook = $this->user_type == __USER_TYPE_USER__ ? 'wp_enqueue_scripts' : 'admin_enqueue_scripts';
        add_action( $hook, [$this, 'enqueue_assets_per_user_type']);
        return $this;
    }

    private function load_as_es6_module_when_required( array $enqueue_data ) {

        if ( isset( $enqueue_data[ __ES6_MOD__] ) && $enqueue_data[ __ES6_MOD__ ]) {
            add_filter('script_loader_tag', function ($tag, $handle) use ( $enqueue_data ) {
                if ($handle === $enqueue_data[__SCRIPT_HANDLE_LTR__]) {
                    return \str_replace('src=', 'type="module" src=', $tag );
                }
                return $tag;
            }, 10, 2);
        }    
    }    


    protected function create_entries_handle(  array $entries ): array {

        $id_to_handle = [];
        foreach ($entries as $i => $enqueue_data) {
            $file_owner = $enqueue_data['owner'] ?? null;
            if ( $file_owner != __WP__ ) {
                 $entries[ $i ][__SCRIPT_HANDLE_LTR__] = Tools::generate_asset_handle( $enqueue_data['id' ], $this->js_slug );
                 $id_to_handle[$entries[ $i ]['id']] = $entries[ $i ][__SCRIPT_HANDLE_LTR__]; 
            }
        }

        foreach ($entries as $i => $enqueue_data) {
            $file_owner = $enqueue_data['owner'] ?? null;
            $entries[ $i]['dependency'] ??= [];
            if ( !empty( $entries[ $i ]['dependency']) && $file_owner != __WP__ ) {
                $dep = [];
                foreach( $entries[ $i ]['dependency'] as $dependency )
                   $dep[] = $id_to_handle[ $dependency ] ?? '';
                $entries[ $i ]['dependency'] = \array_filter( $dep, fn( $v) => !empty( $v ));   
            }
        }
        return $entries;
    }



}