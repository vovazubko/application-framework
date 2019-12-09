<?php
/**
 * Application Framework
 *
 * Framework for Wordpress.
 *
 * @category   Application
 * @package    AppFramework\Core
 * @author     Vova Zubko <vozubko@gmail.com>
 * @copyright  2019 Vova Zubko
 * @version    1.0
 * @since      File available since Release 1.0
 * @file       src/framework/Core/App.php
 * @date       10/20/2019
 * @link       https://vauko.com
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GPLv2
 */

declare(strict_types=1);

namespace AppFramework\Core;


use Error;
use Exception;
use AppFramework\Exception\AppException;

/**
 * Class Application
 * @package Application\Common
 */
class App
{
    use RegistryImplementation;

    /**
     * @var Registry Application registry
     */
    private $registry;

    private $exception;

    /**
     * Application constructor.
     *
     * @param string $nameSpace
     */
    public function __construct($nameSpace)
    {
        try {
            $this->registry = Registry::getInstance();

            $this->appFrameworkVersion = '0.0.2';
            $this->appNameSpace        = $nameSpace;
            $this->appPath             = dirname(__DIR__, 3);
            $this->appURL              = plugin_dir_url(dirname(__DIR__, 2));

            $loader               = new Loader($this->registry);
            $this->registry->load = $loader;
            $this->load->registerWidgets();

            $option                 = new Option($this->registry);
            $this->registry->option = $option;

            $this->load->config('Config');

            $this->registerPostTypes();
            $this->addHooks();
            $this->loadShortcodes();

            if (is_admin()) {
                // Backend Controller
                $this->load->registerMaintenance();

                $this->load->controller('BackendController');
                if ($this->BackendController instanceof InitInterface) {
                    $this->BackendController->init();
                }
            } else {
                // Frontend Controller
                $this->load->controller('FrontendController');
                if ($this->FrontendController instanceof InitInterface) {
                    $this->FrontendController->init();
                }
            }
        } catch (AppException $exception) {
            $this->exception = $exception;

            //echo $exception->getMessage();

            if ($this->config->deactivateOnError) {
                add_action('admin_notices', [$this, 'showNotice']);
                add_action('admin_init', [$this, 'deactivatePlugin']);
            }
        } catch (Error $exception) {
            $this->exception = $exception;

            //echo $exception->getMessage();

            if ($this->config->deactivateOnError) {
                add_action('admin_notices', [$this, 'showNotice']);
                add_action('admin_init', [$this, 'deactivatePlugin']);
            }
        }
    }

    /**
     *
     */
    public function deactivatePlugin()
    {
        $plugin = end(explode(DIRECTORY_SEPARATOR, $this->config->path));
        $plugin = $plugin . DIRECTORY_SEPARATOR . $plugin . '.php';
        \deactivate_plugins($plugin);
    }

    public function showNotice()
    {
        $class   = 'notice notice-error';
        $message = __('Plugin Application Error: ' . $this->exception->getMessage(), 'text-domain');

        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));

        $deactivated = __('Plugin ' . $this->option->get('applicationName') . ' deactivated! Use Config file to change behavior.', 'text-domain');

        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($deactivated));
    }

    // Private Methods
    // -----------------------------------------------------------------------------------------------------------------

    public function loadShortcodes()
    {
        $path = $this->config->path . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'Shortcode';

        if (!is_dir($path)) {
            return;
        }

        $shortcodes = scandir($path, SCANDIR_SORT_NONE);

        foreach ($shortcodes as $shortcode) {
            if (!\in_array($shortcode, [
                    '.',
                    '..'
                ])
                && is_file($path . DIRECTORY_SEPARATOR . $shortcode)) {
                $shortcode = basename($shortcode, '.php');
                $class     = $this->config->nameSpace . '\\Shortcode\\' . $shortcode;
                $object    = new $class($this->registry);

                if (isset($object->name) && \is_string($object->name)) {
                    $shortcode = $object->name;
                }

                add_shortcode($shortcode, [$object, 'init']);
            }
        }
    }

    private function registerPostTypes()
    {
        if (isset($this->config->postTypes) && \is_array($this->config->postTypes)) {
            foreach ($this->config->postTypes as $postType) {
                add_action('init', [$this->config, 'registerPostType_' . $postType['name']]);
            }
        }
    }

    private function addHooks()
    {
        $types = [
            [
                'field' => 'actions',
                'function' => 'add_action'
            ],
            [
                'field' => 'filters',
                'function' => 'add_filter'
            ]
        ];

        foreach ($types as $type) {
            $field    = $type['field'];
            $function = $type['function'];

            if (isset($this->Config->$field) && \is_array($this->Config->$field)) {
                foreach ($this->Config->$field as $action) {
                    $controllerName = $action['callback']['controller'];

                    if (!isset($this->$controllerName)) {
                        $this->load->controller($action['callback']['controller']);
                    }

                    $callback = [
                        $this->$controllerName,
                        $action['callback']['action']
                    ];

                    if (isset($action['priority'])) {
                        if (isset($action['arguments'])) {
                            $function($action['name'], $callback, $action['priority'], $action['arguments']);
                        } else {
                            $function($action['name'], $callback, $action['priority']);
                        }
                    } else {
                        $function($action['name'], $callback);
                    }
                }
            }
        }
    }
}
