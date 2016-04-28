<?php
namespace MartinSotirov\CorePress\Utils;

class AssetLoader
{
    private $files = [];

    public function __construct($files)
    {
        $this->files = $files;
    }

    public function enqueueScriptsAndStyles()
    {
        foreach ($this->files as $file) {
            echo '<pre>' . print_r($file, 1) . '</pre>';

            if ($file['dirname'] === 'assets/css' && $file['extension'] === 'css') {
                wp_enqueue_style(rtrim($file['basename'], '.css'), $this->uri . '/' . $file['path']);
            } elseif ($file['dirname'] === 'assets/js' && $file['extension'] === 'js') {
                wp_enqueue_script(rtrim($file['basename'], '.js'), $this->uri . '/' . $file['path'], [], '', true);
            }
        }
    }
}
