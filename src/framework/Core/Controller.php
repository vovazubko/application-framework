<?php
/**
 * Application Framework
 *
 * Framework for Wordpress.
 *
 * @category   Application
 * @package    Core
 * @author     Vova Zubko <vozubko@gmail.com>
 * @copyright  2019 Vova Zubko
 * @version    1.0
 * @since      File available since Release 1.0
 * @file       src/framework/Core/AppController.php
 * @date       10/20/2019
 * @link       https://vauko.com
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GPLv2
 */

declare(strict_types=1);

namespace AppFramework\Core;


use AppFramework\Exception\AppException;

/**
 * Class Controller
 */
class Controller extends Common
{
    /**
     * Handle calls to missing methods on the controller.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     *
     * @throws AppException
     */
    public function __call($method, $parameters)
    {
        throw new AppException(sprintf(
            'Action %s::%s does not exist.', static::class, $method
        ));
    }
}