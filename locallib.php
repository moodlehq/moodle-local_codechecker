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
 * Code checker library code.
 *
 * @package    local_codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/local/codechecker/pear/PHP/CodeSniffer.php');


/**
 * Settings form for the code checker.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_codechecker_form extends moodleform {
    protected function definition() {
        global $path;
        $mform = $this->_form;

        $a = new stdClass();
        $a->link = html_writer::link('http://docs.moodle.org/dev/Coding_style',
                get_string('moodlecodingguidelines', 'local_codechecker'));
        $a->path = html_writer::tag('tt', 'local/codechecker');
        $a->excludeexample =  html_writer::tag('tt', 'db, backup/*1, *lib*');
        $mform->addElement('static', '', '', get_string('info', 'local_codechecker', $a));

        $mform->addElement('text', 'path', get_string('path', 'local_codechecker'), array('size'=>'48'));
        $mform->setType('path', PARAM_PATH);
        $mform->addRule('path', null, 'required', null, 'client');

        $mform->addElement('text', 'exclude', get_string('exclude', 'local_codechecker'), array('size'=>'48'));
        $mform->setType('exclude', PARAM_NOTAGS);

        $mform->addElement('submit', 'submitbutton', get_string('check', 'local_codechecker'));
    }
}


/**
 * Code sniffer insists on having an PHP_CodeSniffer_CLI, even though we don't
 * really want one. This is a dummy class to make it work.
 */
class local_codechecker_codesniffer_cli extends PHP_CodeSniffer_CLI {
    /** Constructor */
    public function __construct() {
        $this->errorSeverity = 1;
        $this->warningSeverity = 1;
    }
    public function getCommandLineValues() {
        return array('showProgress' => false);
    }
}


/**
 * Convert a full path name to a relative one, for output.
 * @param string $file a full path name of a file.
 * @return string the prittied up path name.
 */
function local_codechecker_pretty_path($file) {
    global $CFG;
    return substr($file, strlen($CFG->dirroot) + 1);
}

/**
 * Get a list of folders to ignores.
 *
 * @param string $extraignorelist optional comma separated list of substr matching paths to ignore.
 * @return array of paths.
 */
function local_codesniffer_get_ignores($extraignorelist = '') {
    global $CFG;

    $paths = array();

    $thirdparty = simplexml_load_file($CFG->libdir . '/thirdpartylibs.xml');
    foreach ($thirdparty->xpath('/libraries/library/location') as $lib) {
        $paths[] = preg_quote(local_codechecker_clean_path('/lib/' . $lib));
    }

    $paths[] = preg_quote(local_codechecker_clean_path(
            '/local/codechecker' . DIRECTORY_SEPARATOR . 'pear'));
    // Changed in PHP_CodeSniffer 1.4.4 and upwards, so we apply the
    // same here: Paths go to keys and mark all them as 'absolute'.
    $finalpaths = array();
    foreach ($paths as $pattern) {
        $finalpaths[$pattern] = 'absolute';
    }
    // Let's add any substr matching path passed in $extraignorelist.
    if ($extraignorelist) {
        $extraignorearr = explode(',', $extraignorelist);
        foreach ($extraignorearr as $extraignore) {
            $extrapath = trim($extraignore);
            $finalpaths[$extrapath] = 'absolute';
        }
    }
    return $finalpaths;
}

function local_codechecker_get_line_of_code($line, $prettypath) {
    global $CFG;

    static $lastfilename = null;
    static $file = null;

    if ($prettypath != $lastfilename) {
        $file = file($CFG->dirroot . '/' . $prettypath);
        $lastfilename = $prettypath;
    }

    return $file[$line - 1];
}

/**
 * The code-checker code assumes that paths always use DIRECTORY_SEPARATOR,
 * whereas Moodle is more relaxed than that. This method cleans up file paths by
 * converting all / and \ to DIRECTORY_SEPARATOR. It should be used whenever a
 * path is passed to the CodeSniffer library.
 * @param string $path a file path
 * @return the path with all directory separators changed to DIRECTORY_SEPARATOR.
 */
function local_codechecker_clean_path($path) {
    return str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $path);
}

/**
 * Recursively finds all files within a folder that match particular extensions.
 * @param array &$arr Array to add file paths to
 * @param string $folder Path to search (or may be a single file)
 * @param string $extensions File extensions to include (not including .)
 */
function local_codechecker_find_other_files(&$arr, $folder,
        $extensions = array('txt', 'html', 'csv')) {
    $regex = '~\.(' . implode('|', $extensions) . ')$~';

    // Handle if this is called directly with a file and not folder
    if (is_file($folder)) {
        if (preg_match($regex, $folder)) {
            $arr[] = $folder;
        }
        return;
    }
    if ($handle = opendir($folder)) {
        while (($file = readdir($handle)) !== false) {
            $fullpath = $folder . '/' . $file;
            if ($file === '.' || $file === '..') {
                continue;
            } else if (is_file($fullpath)) {
                if (preg_match($regex, $fullpath)) {
                    $arr[] = $fullpath;
                }
            } else if (is_dir($fullpath)) {
                local_codechecker_find_other_files($arr, $fullpath);
            }
        }
        closedir($handle);
    } else {
        throw new moodle_exception('error_find', 'local_codechecker');
    }
}

/**
 * Adds a problem report with a given file.
 * @param array $problems Existing problem structure from PHPCodeSniffer
 *   to which new problem will be added
 * @param string $file File path
 * @param int $line Line number (1-based)
 * @param string $key Key within language file ('other_' will be prepended)
 * @param bool $warning If true is warning, otherwise error
 */
function local_codechecker_add_problem(&$problems, $file, $line, $key, $warning=false) {
    // Build new problem structure
    $newproblem = array(
        'message' => get_string('other_' . $key, 'local_codechecker'),
        'source' => 'other.' . $key,
        'severity' => $warning? PHPCS_DEFAULT_WARN_SEV : PHPCS_DEFAULT_ERROR_SEV
    );

    // Find appropriate place and add new problem
    if ($warning) {
        $problems[$file]['numWarnings']++;
        $inner =& $problems[$file]['warnings'];
    } else {
        $problems[$file]['numErrors']++;
        $inner =& $problems[$file]['errors'];
    }

    if (!array_key_exists($line, $inner)) {
        $inner[$line] = array();
    }
    if (!array_key_exists(1, $inner[$line])) {
        $inner[$line][1] = array();
    }

    $inner[$line][1][] = $newproblem;
}

/**
 * Checks an individual other file and adds basic problems to result.
 * @param string $file File to check
 * @param array $problems Existing problem structure from PHPCodeSniffer
 *   to which new problems will be added
 */
function local_codechecker_check_other_file($file, &$problems) {
    if (!array_key_exists($file, $problems)) {
        $problems[$file] = array('warnings' => array(), 'errors' => array(),
                'numWarnings' => 0, 'numErrors' => 0);
    }

    // Certain files are permitted lines of any length because they are
    // auto-generated
    $allowanylength = in_array(basename($file), array('install.xml')) ||
            substr($file, -4, 4) === '.csv';

    $lines = file($file);
    $index = 0;
    $blankrun = 0;
    foreach ($lines as $l) {
        $index++;
        // Incorrect [Windows] line ending
        if ((strpos($l, "\r\n") !== false) && empty($donecrlf)) {
            local_codechecker_add_problem($problems, $file, $index, 'crlf');
            $donecrlf = true;
        }
        // Missing line ending (at EOF presumably)
        if (strpos($l, "\n") === false) {
            local_codechecker_add_problem($problems, $file, $index, 'missinglf');
        }
        $l = rtrim($l);
        if ($l === '') {
            $blankrun++;
        } else {
            $blankrun = 0;
        }

        // Whitespace at EOL
        if (preg_match('~ +$~', $l)) {
            local_codechecker_add_problem($problems, $file, $index, 'eol');
        }
        // Tab anywhere in line
        if (preg_match('~\t~', $l)) {
            local_codechecker_add_problem($problems, $file, $index, 'tab');
        }

        if (strlen($l) > 180 && !$allowanylength) {
            // Line length > 180
            local_codechecker_add_problem($problems, $file, $index, 'toolong');
        } else if (strlen($l) > 132 && !$allowanylength) {
            // Line length > 132
            local_codechecker_add_problem($problems, $file, $index, 'ratherlong', true);
        }
    }
    if ($blankrun > 0) {
        local_codechecker_add_problem($problems, $file, $index - $blankrun, 'extralfs');
    }
}

/**
 * Checking the parts that PHPCodeSniffer can't reach (i.e. anything except
 * php, css, js) for basic whitespace problems.
 * @param string $path Path to search (may be file or folder)
 * @param array $problems Existing problem structure from PHPCodeSniffer
 *   to which new problems will be added
 */
function local_codechecker_check_other_files($path, &$problems) {
    $files = array();
    local_codechecker_find_other_files($files, $path);
    foreach ($files as $file) {
        local_codechecker_check_other_file($file, $problems);
    }
}

/**
 * Calculate the total number of errors and warnings in the execution
 *
 * @param array $problems Existing problem structure from PHPCodeSniffer
 *   for which total number of errors and warnings will be counted.
 * return array with the total count of errors and warnings.
 */
function local_codechecker_count_problems($problems) {
    $errors = 0;
    $warnings = 0;
    foreach ($problems as $file => $info) {
        $errors += $info['numErrors'];
        $warnings += $info['numWarnings'];
    }
    return array($errors, $warnings);
}
