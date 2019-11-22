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
 * @file       src/framework/Core/Widget.php
 * @date       10/20/2019
 * @link       https://vauko.com
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GPLv2
 */

declare(strict_types=1);

namespace AppFramework\Core;


use Exception;
use WP_Widget;

abstract class Widget extends WP_Widget
{
    use RegistryImplementation;

    protected $registry;

    /**
     * AppWidget constructor.
     *
     * @param string $id_base
     * @param string $name
     * @param array $widget_options
     * @param array $control_options
     *
     * @throws Exception
     */
    public function __construct($id_base, $name, array $widget_options = [], array $control_options = [])
    {
        parent::__construct($id_base, $name, $widget_options, $control_options);

        $this->registry = Registry::getInstance();
    }
}