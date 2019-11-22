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
 * @file       src/framework/Core/Registry.php
 * @date       10/20/2019
 * @link       https://vauko.com
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GPLv2
 */

declare(strict_types=1);

namespace AppFramework\Core;


final class Registry
{
    use RegistryImplementation;

    /**
     * @var Registry
     */
    private static $instance;

    private $registry;

    /**
     * gets the instance via lazy initialization (created on first usage)
     */
    public static function getInstance(): Registry
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * That's not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    private function __construct()
    {
        $this->registry = new \stdClass();
    }

    /**
     * Prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    /**
     * Prevent from being unserialized (which would create a second instance of it)
     */
    private function __wakeup()
    {
    }
}