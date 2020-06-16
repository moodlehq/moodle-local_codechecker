<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Run the code checker from the command-line.
 *
 * @package    local_codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/local/codechecker/locallib.php');


// Get the command-line options.
list($options, $unrecognized) = cli_get_params(
    array('help' => false, 'interactive' => false),
    array('h' => 'help', 'i' => 'interactive'));

if (count($unrecognized) != 1) {
    $options['help'] = true;
} else {
    $path = clean_param(reset($unrecognized), PARAM_PATH);
}

if ($options['help']) {
    echo get_string('clihelp', 'local_codechecker'), "\n";
    die();
}

$interactive = false;
if ($options['interactive']) {
    $interactive = true;
}

raise_memory_limit(MEMORY_HUGE);

$standard = $CFG->dirroot . str_replace('/', DIRECTORY_SEPARATOR, '/local/codechecker/moodle');

$cli = new local_codechecker_codesniffer_cli();
$phpcs = new PHP_CodeSniffer(1, 0, 'utf-8', $interactive);
$phpcs->setAllowedFileExtensions(['php']); // We are only going to process php files ever.
$phpcs->setCli($cli);
$phpcs->setIgnorePatterns(local_codesniffer_get_ignores());
$phpcs->process(local_codechecker_clean_path(
        $CFG->dirroot . '/' . trim($path, '/')),
        local_codechecker_clean_path($standard));
$phpcs->reporting->printReport('full', false, $cli->getCommandLineValues(), null);
