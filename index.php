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
 * Run the code checker from the web.
 *
 * @package    local_codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/local/codechecker/locallib.php');

$pathlist = optional_param('path', '', PARAM_RAW);
$exclude = optional_param('exclude', '', PARAM_NOTAGS);
$includewarnings = optional_param('includewarnings', true, PARAM_BOOL);

$pageparams = array();
if ($pathlist) {
    $pageparams['path'] = $pathlist;
}
if ($exclude) {
    $pageparams['exclude'] = $exclude;
}
$pageparams['includewarnings'] = $includewarnings;

admin_externalpage_setup('local_codechecker', '', $pageparams);

// We are going to need lots of memory and time.
raise_memory_limit(MEMORY_HUGE);
set_time_limit(600);

$mform = new local_codechecker_form(new moodle_url('/local/codechecker/'));
$mform->set_data((object)$pageparams);
if ($data = $mform->get_data()) {
    redirect(new moodle_url('/local/codechecker/', $pageparams));
}

$output = $PAGE->get_renderer('local_codechecker');

echo $OUTPUT->header();

if ($pathlist) {
    $paths = preg_split('~[\r\n]+~', $pathlist);

    $failed = false;
    $fullpaths = [];
    foreach ($paths as $path) {
        $path = trim(clean_param($path, PARAM_PATH));
        if (empty($path)) { // No blanks, we don't want to check the whole dirroot.
            continue;
        }
        $fullpath = $CFG->dirroot . '/' . trim($path, '/');
        if (!is_file($fullpath) && !is_dir($fullpath)) {
            echo $output->invald_path_message($path);
            $failed = true;
            continue;
        }
        $fullpaths[] = local_codechecker_clean_path($fullpath);
    }

    if ($fullpaths && !$failed) {
        $reportfile = make_temp_directory('phpcs') . '/phpcs_' . random_string(10) . '.xml';
        $phpcs = new PHP_CodeSniffer();
        $phpcs->setAllowedFileExtensions(['php']); // We are only going to process php files ever.
        $cli = new local_codechecker_codesniffer_cli();
        $cli->setReport('local_codechecker'); // Using own custom xml format for easier handling later.
        $cli->setReportFile($reportfile); // Send the report to dataroot temp.
        $cli->setIncludeWarnings($includewarnings); // Decide if we want to show warnings (defaults yes).
        $phpcs->setCli($cli);
        $phpcs->setIgnorePatterns(local_codesniffer_get_ignores($exclude));
        $phpcs->process($fullpaths,
                local_codechecker_clean_path($CFG->dirroot . '/local/codechecker/moodle'));
        // Save the xml report file to dataroot/temp.
        $phpcs->reporting->printReport('local_codechecker', false, $cli->getCommandLineValues(), $reportfile);
        // Load the XML file to proceed with the rest of checks.
        $xml = simplexml_load_file($reportfile);

        // Look for other problems, not handled by codesniffer.
        local_codechecker_check_other_files(local_codechecker_clean_path($fullpath), $xml);
        list($numerrors, $numwarnings) = local_codechecker_count_problems($xml);

        // Output the results report.
        echo $output->report($xml, $numerrors, $numwarnings);

        // And clean the report temp file.
        @unlink($reportfile);
    }
}

$mform->display();
echo $OUTPUT->footer();
