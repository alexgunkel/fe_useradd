<?php
/**
 * PHPUnit Bootstrap
 *
 * @category TYPO3 Extension
 * @package  axel-kummer/aku-logbook
 * @author   Axel Kummer <kummeraxel@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @link     https://github.com/axel-kummer/aku-logbook
 */

$testDir = __DIR__ . "/";

$autoload = $testDir . "../vendor/autoload.php";
if (false === file_exists($autoload)) {
    $autoload = $testDir . "../../../../vendor/autoload.php";
}
if (false === file_exists($autoload)) {
    $autoload = $testDir . "../../../../../vendor/autoload.php";
}

include_once $autoload;