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
 * @file       src/framework/Core/Option.php
 * @date       10/20/2019
 * @link       https://vauko.com
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GPLv2
 */

declare(strict_types=1);

namespace AppFramework\Core;


use AppFramework\Exception\AppException;

/**
 * Class Option
 * @package Application\Core
 */
class Option
{
    protected $registry;

    private $configName;

    private $options;

    /**
     * Option constructor.
     *
     * @param Registry $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->configName = $this->registry->config->configName;
        $this->options = get_option($this->configName);
    }

    /**
     * Get one option by key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (isset($this->options->$key)) {
            return $this->options->$key;
        }

        return null;
    }

    /**
     * Add dynamic option
     *
     * @param string $key
     * @param mixed $value
     *
     * @return bool
     */
    public function set($key, $value): bool
    {
        $this->options->$key = $value;

        return update_option($this->configName, $this->options);
    }

    /**
     * Get all registry as object
     *
     * @return object
     */
    public function getAll()
    {
        return $this->options;
    }

    /**
     * Initialization options
     *
     * @param object $options Initialization options
     *
     * @return bool
     */
    public function initialization($options): bool
    {
        $this->options = $options;
        return add_option($this->configName, $this->options);
    }

    /**
     * Clear application options
     *
     * @return bool
     */
    public function clear()
    {
        $this->options = null;

        return delete_option($this->configName);
    }
}