<?php namespace App\Services\MenuBuilder;

use App\Support\Tools;
use ThisPlugin\Boot\Process;


class AdminMenuBuilder {
    protected const TOPIC            = '_menus';
    protected const STD_CAPABILITY   = 'manage_options';
    protected const GENERIC_ICON     = 'dashicons-admin-generic';
     
    protected array $menus           = [ ];
    protected string $app_prefix     = '';
    protected array $submenu_tracker = [ ];


    public function __construct( protected Process $process ) {
        
        $this->submenu_tracker = [];
        $this->menus           = config('menus');
        $this->app_prefix      = config('app.prefix').self::TOPIC;
    }

    public function register_admin_menus(): void {
        add_action('admin_menu', [$this, 'register_menus']);
    }

    public function register_menus(): void {
        foreach ($this->menus as $menu) {
            $this->add_menu_page($menu);
        }
    }


    protected function get_capability( array $menu ): string {
        return $menu['capability'] ?? self::STD_CAPABILITY;
    }


    protected function add_menu_page(array $menu): void {

        $parent_slug = Tools::generate_wp_slug( $menu['menu_title'], $this->app_prefix );
        add_menu_page(
            $menu['page_title'],
            $menu['menu_title'],
            $this->get_capability( $menu ),
             $parent_slug,
              $this->get_menu_callback( $menu['callback'] ?? null ),
              $menu['icon_url'] ?? self::GENERIC_ICON,
              $menu['position'] ?? null
        );
        if (!empty($menu['submenus'])) {
            
            foreach ($menu['submenus'] as $submenu) {
                $this->add_submenu_page($parent_slug, $submenu);
            }
        }
    }

    protected function add_submenu_page(string $parent_slug, array $submenu): void {

        $submenu_slug = $this->get_menu_slug(  $submenu['menu_title' ]);

        // Allow first submenu to reuse the parent slug
        if (!isset($this->submenu_tracker[ $parent_slug ])) {
            $submenu_slug = $parent_slug;
            $this->submenu_tracker[ $parent_slug ] = true;
        }
        add_submenu_page(
            $parent_slug,
             $submenu['page_title'],
             $submenu['menu_title'],
             $this->get_capability( $submenu ),
              $submenu_slug,
               $this->get_menu_callback( $submenu['callback'] ?? null )
        );

    }

    private function get_menu_callback(?string $callback = null): mixed {

        return ( $callback && \method_exists( $this->process, $callback )) 
             ? [ $this->process, $callback ] 
             : '__return_false'; // Avoid errors by returning a default empty function
    }


    public function get_menu_slug( $menu_title ) {
        return Tools::generate_wp_slug($menu_title, $this->app_prefix);
    }

    public function get_menu_link( $menu_title ) {
        return admin_url('admin.php?page='.Tools::generate_wp_slug($menu_title, $this->app_prefix));
    }


    
}