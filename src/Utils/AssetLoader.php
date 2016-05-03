<?php
namespace MartinSotirov\CorePress\Utils;

class AssetLoader
{
    private $filesystem;

    public function __construct($filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function loadAssets()
    {
        if ($this->filesystem->has('config/assets.php')) {
            $assets = include(get_stylesheet_directory() . '/config/assets.php');

            if (isset($assets['scripts'])) {
                foreach ($assets['scripts'] as $scriptHandle => $args) {
                    $args = wp_parse_args($args, [
                        'src'       => false,
                        'deps'      => [],
                        'ver'       => false,
                        'in_footer' => false
                    ]);
                    wp_enqueue_script($scriptHandle, $args['src'], $args['deps'], $args['ver'], $args['in_footer']);
                }
            }

            if (isset($assets['styles'])) {
                foreach ($assets['styles'] as $styleHandle => $args) {
                    $args = wp_parse_args($args, [
                        'src'   => false,
                        'deps'  => [],
                        'ver'   => false,
                        'media' => 'all'
                    ]);
                    wp_enqueue_style($styleHandle, $args['src'], $args['deps'], $args['ver'], $args['media']);
                }
            }
        }

        /**
         * Asset autoloader
         */
        //$files = $this->filesystem->listContents('assets', true);
        //foreach ($files as $file) {

            //if ($file['dirname'] === 'assets/css' && $file['extension'] === 'css') {
                //wp_enqueue_style(rtrim($file['basename'], '.css'), trailingslashit(get_stylesheet_directory_uri()) . $file['path']);
            //} elseif ($file['dirname'] === 'assets/js' && $file['extension'] === 'js') {
                //wp_enqueue_script(rtrim($file['basename'], '.js'), trailingslashit(get_stylesheet_directory_uri()) . $file['path'], [], '', true);
            //}
        //}
    }
}
