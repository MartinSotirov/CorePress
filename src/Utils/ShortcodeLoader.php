<?php
namespace MartinSotirov\CorePress\Utils;

class ShortcodeLoader
{
    private $container;
    private $themeClass;

    public function __construct($container, $themeClass)
    {
        $this->container = $container;
        $this->themeClass = $themeClass;
    }

    public function loadShortcodes()
    {
        $filesystem = $this->container->get('filesystem');

        if ($filesystem->has('inc/Shortcodes')) {

            $reflection = new \ReflectionClass($this->themeClass);
            $namespaceName = $reflection->getNamespaceName();

            foreach ($filesystem->listContents('inc/Shortcodes') as $file) {

                if ($file['extension'] === 'php') {

                    $fileName = rtrim($file['basename'], '.php');
                    $namespacedClassName = '\\' . $namespaceName . '\\Shortcodes\\' . $fileName;

                    // get shortcode tag or make one from the class name
                    if (defined($namespacedClassName . '::tag')) {
                        $tag = $namespacedClassName::tag;
                    } else {
                        $tag = mb_strtolower($fileName);
                    }

                    // register shortcode
                    add_shortcode($tag, [$namespacedClassName, 'render']);
                }

            }
        }

    }

}

