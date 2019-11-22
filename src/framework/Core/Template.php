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
 * @file       src/framework/Core/Template.php
 * @date       10/20/2019
 * @link       https://vauko.com
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GPLv2
 */

declare(strict_types=1);

namespace AppFramework\Core;


class Template extends Common
{
    private $template;
    private $data = [];
    private $callback;

    /**
     * Template constructor.
     *
     * @param $registry
     * @param $callback
     */
    public function __construct(&$registry, $callback)
    {
        parent::__construct($registry);

        $this->callback = $callback;
    }

    public function __call($name, $arguments)
    {
        if ($this->callback !== null) {
            return \call_user_func_array([$this->callback, $name], $arguments);
        }

        return null;
    }

    /**
     * Add keys and their values.
     *
     * @param string $key get key
     * @param string $value get key value
     *
     * @return Template current object
     */
    public function set($key, $value): Template
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Render the template file
     *
     * @param string $template Template foo/bar.
     *
     * @return string
     */
    public function render($template): string
    {
        $this->template = $template;
        $file = $this->getViewFile();

        if ($file === false) {
            return "Error: Could not load template ($this->template)!";

        }

        $countVariablesCreated = extract($this->data, EXTR_SKIP);
        if ($countVariablesCreated !== \count($this->data)) {
            throw new \RuntimeException('Extraction failed: scope modification attempted');
        }

        ob_start();
        include $file;

        return ob_get_clean();
    }

    /**
     * Looks for the view file according to the given view name.
     *
     * @param string $extension
     *
     * @return mixed the view file path, false if the view file does not exist
     */
    public function getViewFile($extension = 'php')
    {
        $viewPath = $this->registry->get('config')->path . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'View';

        $path[] = realpath($viewPath . DIRECTORY_SEPARATOR . $this->template . '_custom.' . $extension);
        $path[] = realpath($viewPath . DIRECTORY_SEPARATOR . $this->template . '.' . $extension);

        foreach ($path as $file) {
            if ($file !== false) {
                return $file;
            }
        }

        return false;
    }
}