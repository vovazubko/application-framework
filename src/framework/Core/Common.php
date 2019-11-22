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
 * @file       src/framework/Core/Common.php
 * @date       10/20/2019
 * @link       https://vauko.com
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GPLv2
 */

declare(strict_types=1);

namespace AppFramework\Core;


/**
 * Class Common
 */
abstract class Common
{
    use RegistryImplementation;

    protected $registry;

    public function __construct(&$registry)
    {
        $this->registry = $registry;
    }
}