<?php
/**
 * Application Framework
 *
 * Framework for Wordpress.
 *
 * @category   Application
 * @package    AppFramework
 * @author     Vova Zubko <vozubko@gmail.com>
 * @copyright  2019 Vova Zubko
 * @version    1.0
 * @since      File available since Release 1.0
 * @file       src/framework/loader.php
 * @date       10/20/2019
 * @link       https://vauko.com
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GPLv2
 */

declare(strict_types=1);

defined('ABSPATH') || exit;


use AppFramework\Core\App;


/**
 * PSR-4 Autoloader
 *
 * @param string $class The fully-qualified class name.
 *
 * @return void
 */
try {
    spl_autoload_register(function ($class) {
        $loaders = [
            [
                'prefix' => 'AppFramework\\',
                'directory' => __DIR__,
            ],
            [
                'prefix' => 'App\\',
                'directory' => dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'application',
            ],
        ];

        foreach ($loaders as $loader) {
            $length = strlen($loader['prefix']);
            if (strncmp($loader['prefix'], $class, $length) === 0) {
                $relativeClass = substr($class, $length);
                $file = $loader['directory'] . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

                if (file_exists($file)) {
                    require_once $file;
                }
            }
        }
    });
} catch (Exception $e) {
    if (defined('DEBUG')) {
        error_log($e->getMessage());
    }
}


if (!function_exists('get_application')) {
    /**
     * Get reference for application
     *
     * @return App
     */
    function get_application()
    {
        static $app = null;

        if ($app === null) {
            $app = new App('Application');
        }

        return $app;
    }
} else {
    throw new \RuntimeException(sprintf('Global function "%s" has a name conflict', 'get_application'));
}
