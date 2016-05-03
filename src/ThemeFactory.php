<?php
namespace MartinSotirov\CorePress;
require_once(dirname(__FILE__) . '/../vendor/autoload.php');

/**
 * Instanciates a theme object
 */
class ThemeFactory
{
    /**
     * @param  string $themeClass A namespaced class name
     * @return Theme              A concrete implementation of the abstract Theme class
     */
    public static function create($themeClass)
    {
        $container = new \League\Container\Container;

        $container->share('filesystem', function() {
            $adapter = new \League\Flysystem\Adapter\Local(get_stylesheet_directory());
            return new \League\Flysystem\Filesystem($adapter);
        });

        $container->add('assetLoader', function() use ($container) {
            $filesystem = $container->get('filesystem');
            return new Utils\AssetLoader($filesystem);
        });

        $container->add('shortcodeLoader', function() use ($container, $themeClass) {
            return new Utils\ShortcodeLoader($container, $themeClass);
        });

        return new $themeClass($container);
    }
}
