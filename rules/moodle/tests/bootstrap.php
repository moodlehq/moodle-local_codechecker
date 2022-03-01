<?php
/**
 * Moodle Coding Standards.
 *
 * Bootstrap file for running PHPUnit tests.
 *
 * @package   MoodleHQ\MoodleCS\moodle
 * @link      https://github.com/moodlehq/moodle-cs
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright Andrew Lyons <andrew@nicols.co.uk>
 */

if (!defined('PHPUNIT_TEST')) {
    define('PHPUNIT_TEST', true);
}

$ds = DIRECTORY_SEPARATOR;
$root = dirname(dirname(__DIR__));

require_once("{$root}/vendor/autoload.php");
require_once("{$root}/vendor/squizlabs/php_codesniffer/tests/bootstrap.php");
require_once("{$root}/vendor/phpcompatibility/php-compatibility/PHPCSAliases.php");
