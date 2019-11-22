<?php
/**
 * Application Framework
 *
 * @category   Application
 * @package    AppFramework\Core
 * @author     Vova Zubko <vozubko@gmail.com>
 * @copyright  2019 Vova Zubko
 * @version    1.0
 * @since      File available since Release 1.0
 * @file       src/framework/Core/Loader.php
 * @date       10/20/2019
 * @link       https://vauko.com
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GPLv2
 */

declare(strict_types=1);

namespace AppFramework\Core;


use AppFramework\Exception\AppException;

/**
 * Class Loader
 * @package PluginApplication\Core
 */
class Loader extends Common
{
    /**
     * Register maintenance hooks
     */
    public function registerMaintenance(): void
    {
        $maintenance = $this->appNameSpace . '\\Core\\Maintenance';

        if (class_exists($maintenance)) {
            register_activation_hook($this->appPath . '/plugin.php', [$maintenance, 'activate']);
            register_deactivation_hook($this->appPath . '/plugin.php', [$maintenance, 'deactivate']);
            register_uninstall_hook($this->appPath . '/plugin.php', [$maintenance, 'uninstall']);
        }
    }

    /**
     * Register maintenance hooks
     */
    public function registerWidgets(): void
    {
        add_action('widgets_init', [$this, 'initWidgets']);
    }

    /**
     * Hook widgets_init
     */
    public function initWidgets()
    {
        $path = $this->appPath . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'Widget';

        if (!is_dir($path)) {
            return;
        }

        $widgets = scandir($path, SCANDIR_SORT_NONE);

        foreach ($widgets as $widget) {
            if (!\in_array($widget, [
                    '.',
                    '..'
                ]) && is_file($path . DIRECTORY_SEPARATOR . $widget)) {
                $widget = basename($widget, '.php');
                $class = $this->registry->get('config')->nameSpace . '\\Widget\\' . $widget;
                register_widget($class);
            }
        }
    }

    /**
     * Class loader
     *
     * @param string $type
     * @param string $name
     */
    public function load($type, $name)
    {
        try {
            $fullName = $this->registry->appNameSpace . '\\' . ucfirst($type) . '\\' . $name;
            $object = new $fullName($this->registry);
            $sanitizeName = str_replace('\\', '', $name);
            $this->registry->$sanitizeName = $object;
        } catch (\Error $e) {
            throw new AppException($e->getMessage());
        }
    }

    public function __call($method, $parameters)
    {
        $this->load($method, $parameters[0]);
    }

    public function view($name, $data, $callback = null)
    {
        $template = new Template($this->registry, $callback);

        foreach ($data as $key => $value) {
            $template->set($key, $value);
        }

        return $template->render($name);
    }
}