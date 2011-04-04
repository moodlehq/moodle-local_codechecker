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
 * @package    local
 * @subpackage codechecker
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
        $a->link = html_writer::link('http://docs.moodle.org/en/Development:Coding_style',
                get_string('moodlecodingguidelines', 'local_codechecker'));
        $a->path = html_writer::tag('tt', 'local/codechecker');
        $mform->addElement('static', '', '', get_string('info', 'local_codechecker', $a));

        $mform->addElement('text', 'path', get_string('path', 'local_codechecker'));

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
 * @return array of paths.
 */
function local_codesniffer_get_ignores() {
    global $CFG;

    $paths = array();

    $thirdparty = simplexml_load_file($CFG->libdir . '/thirdpartylibs.xml');
    foreach ($thirdparty->xpath('/libraries/library/location') as $lib) {
        $paths[] = preg_quote('/lib/' . $lib);
    }

    $paths[] = preg_quote('/local/codechecker' . DIRECTORY_SEPARATOR . 'pear');
    return $paths;
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