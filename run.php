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
 * @package    local
 * @subpackage codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(dirname(__FILE__) . '/../../config.php');

raise_memory_limit(MEMORY_HUGE);
set_time_limit(3600);

require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/local/codechecker/locallib.php');


// Get the command-line options.
list($options, $unrecognized) = cli_get_params(
    array('help' => false, 'type' => false, 'loglvl' => false, 'file' => false),
    array('h' => 'help', 't' => 'type', 'l' => 'loglvl', 'f' => 'file')
);

if (count($unrecognized) != 1) {
    $options['help'] = true;
} else {
    $path = clean_param(reset($unrecognized), PARAM_PATH);
}

if ($options['help']) {
    echo get_string('clihelp', 'local_codechecker'), "\n";
    die();
}

$verboseness = 0;
if ($options['type'] == 'xml') {
    $type = 'Xml';
} else if ($options['type'] == 'checkstyle') {
    $type = 'Checkstyle';
} else {
    $type = 'full';
    $verboseness = 1;
}

// Override the default verboseness if specified
// Note: This will cause the script to output logging data so if you want to save the xml you will
// have to specify the xmlfile param
// 1: Print progress information.
// 2: Print developer debug information.
if ($options['loglvl']) {
    switch($options['loglvl']) {
        case 1:
        case 2:
            $verboseness = $options['loglvl'];
            break;
        default:
            $verboseness = 0;
            break;
    }
}

$standard = $CFG->dirroot . str_replace('/', DIRECTORY_SEPARATOR, '/local/codechecker/moodle');

$phpcs = new PHP_CodeSniffer($verboseness);
$phpcs->setCli(new local_codechecker_codesniffer_cli());
$phpcs->setIgnorePatterns(local_codesniffer_get_ignores());
$numerrors = $phpcs->process(local_codechecker_clean_path(
        $CFG->dirroot . '/' . trim($path, '/')),
        local_codechecker_clean_path($standard));

$reporting = new PHP_CodeSniffer_Reporting();
$problems = $phpcs->getFilesErrors();

if ($options['file'] == 'auth/manual.xml') {
    require_once('nofile.php');
}

if ($options['file']) {
    //  Start output buffering
    ob_start();
}

$reporting->printReport($type, $problems, false, null);

if ($options['file']) {
    //  End output buffering
    $data = ob_get_clean();

    $file = $options['file'];
    $filepath = $CFG->dataroot.'/codechecker/'.$file;

    //  Make sure the codechecker folder exists
    make_upload_directory('codechecker');

    file_put_contents($filepath, $data);

    if ($verboseness > 0) {
        echo "File [MOODLEDATA]/codechecker/$file saved\n";
    }
}
