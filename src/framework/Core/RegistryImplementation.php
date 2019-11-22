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
 * @file       src/framework/Core/RegistryImplementation.php
 * @date       10/20/2019
 * @link       https://vauko.com
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GPLv2
 */

namespace AppFramework\Core;


trait RegistryImplementation
{
    public function __get($key) {
        return ($this->registry->$key ?? null);
    }

    public function __set($key, $value) {
        $this->registry->$key = $value;
    }

    public function __isset($key): bool {
        return isset($this->registry->$key);
    }
}