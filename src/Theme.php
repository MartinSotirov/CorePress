<?php
namespace MartinSotirov\CorePress;

abstract class Theme
{
    protected $container;
    public $dir;
    public $uri;

    public function __construct($container)
    {
        $this->container = $container;
        $this->dir = get_stylesheet_directory();
        $this->uri = get_stylesheet_directory_uri();
    }

    /**
     * Start error reporting and subscribe theme setup handler
     */
    public function boot()
    {
        // If debug mode is enabled, boot the whoops error page handler
        if (WP_DEBUG === true && WHOOPS === true) {
            $this->bootWhoops();
        }

        // setup theme
        add_action('after_setup_theme', [$this, 'setup']);
    }

    /**
     * Setup theme
     */
    public function setup()
    {
        // subscribe subclass init method to the init hook
        if (method_exists($this, 'init')) {
            add_action('init', [$this, 'init']);
        }

        // Add rss feeds
        add_theme_support('automatic-feed-links');

        // Add post thumbnails
        add_theme_support('post-thumbnails');

        // Load shortcodes
        $shortcodeLoader = $this->get('shortcodeLoader');
        $shortcodeLoader->loadShortcodes();

        // Load scripts and styles
        $assetLoader = $this->container->get('assetLoader');
        add_action('wp_enqueue_scripts', [$assetLoader, 'loadAssets']);

        // Register menus
        $this->registerMenus();

        // Cleanup wp <head> garbage
        $this->cleanupHead();
    }

    /**
     * Syntax sugar for $this->container
     */
    public function get($service)
    {
        return $this->container->get($service);
    }

    /**
     * Boot the whoops error page handler
     */
    public function bootWhoops()
    {
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
    }

    /**
     * Register Theme Menus based on config file
     */
    public function registerMenus()
    {
        $filesystem = $this->get('filesystem');
        if ($filesystem->has('config/menus.php')) {
            add_theme_support('menus');
            register_nav_menus(include($this->dir . '/config/menus.php'));
        }
    }

    public function cleanupHead()
    {
        // category feeds
        remove_action('wp_head', 'feed_links_extra', 3);
        // post and comment feeds
        remove_action('wp_head', 'feed_links', 2);
        // EditURI link
        remove_action('wp_head', 'rsd_link');
        // shortlink
        remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
        // windows live writer
        remove_action('wp_head', 'wlwmanifest_link');
        // index link
        remove_action('wp_head', 'index_rel_link');
        // previous link
        remove_action('wp_head', 'parent_post_rel_link', 10, 0);
        // start link
        remove_action('wp_head', 'start_post_rel_link', 10, 0);
        // links for adjacent posts
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
        // WP version
        remove_action('wp_head', 'wp_generator');
        // remove emoji
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        // remove WP version from css
        add_filter('style_loader_src', [$this, 'hideWpVersion'], 9999);
        // remove Wp version from scripts
        add_filter('script_loader_src', [$this, 'hideWpVersion'], 9999);
        //// clean up markup around images
        //add_filter('the_content', 'rs_filter_ptags_on_images');
    }

    public function hideWpVersion($src)
    {
        if (strpos($src, 'ver=')) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }
}

