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

PHP_SAPI === 'cli' || exit;


if (isset($argv[1])) {
    $frameworkNameSpace = $argv[1];
} else {
    echo PHP_EOL . 'Please enter your application name.' . PHP_EOL;
    echo 'EXAMPLE: php app-cli.php MyPlugin' . PHP_EOL . PHP_EOL;
    exit;
}


$sources = [];
$sources[][] = __DIR__ . DIRECTORY_SEPARATOR . 'plugin.php';
$sources[] = glob(__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*.php');
$sources[] = glob(__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . '*.php');
$sources[] = glob(__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*.php');
$sources = array_merge(...$sources);


foreach ($sources as $source) {
    $sourceContent = file_get_contents($source);
    $content = str_replace([
        'AppFramework',
        'App\\',
        'get_application'
    ], [
        $frameworkNameSpace . 'Framework',
        $frameworkNameSpace . '\\',
        'get_' . $frameworkNameSpace
    ], $sourceContent);

    $newSource = str_replace(basename(__DIR__), $frameworkNameSpace, $source);
    $newSourceDir = dirname($newSource);

    if (!is_dir($newSourceDir) && !mkdir($newSourceDir, 0777, true) && !is_dir($newSourceDir)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $newSourceDir));
    }

    file_put_contents($newSource, $content);

    echo 'File: "' . $newSource . '" was updated.' . PHP_EOL;
}