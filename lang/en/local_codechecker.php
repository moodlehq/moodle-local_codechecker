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
 * Language strings
 *
 * @package    local
 * @subpackage codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$string['check'] = 'Check code';
$string['clihelp'] = 'Check some PHP code against the Moodle coding guidelines.
useage: php run.php [options] [file/folder]

examples:

php run.php /local/codechecker
php run.php --type=xml --loglvl=1 /local/codechecker
php run.php --type=checkstyle --file=checkstyle.xml /local/codechecker
php run.php --file=fullreport/full.txt /local/codechecker

-h, --help     Displays this help text
-t, --type     The type of report to output
               Default       full: Text format for CLI.
                              xml: XML format.
                       checkstyle: Checkstyle XML format.
-l, --loglvl   The level of verboseness to display
               Default for xml/checkstyle type   0: No Logging.
               Default for full type             1: Print progress information.
                                                 2: Print developer debug information.
-f, --file     The file to save the reporting output to (logging information is still written to stdout)
               Will be relative to $CFG->dataroot/codechecker. Will create folder if it doesn\'t exist.
';

$string['clihelp2'] = 'Run complete codebase against codechecker and saves checkstyle xml output.
useage: php runcheckstyle.php';
$string['error_find'] = 'Folder search failed';
$string['other_eol'] = 'Whitespace at end of line';
$string['other_tab'] = 'Tab character not permitted';
$string['other_toolong'] = 'Line longer than maximum 140 characters';
$string['other_ratherlong'] = 'Line longer than recommended 100 characters';
$string['other_crlf'] = 'Windows (CRLF) line ending instead of just LF (reporting only first occurrence)';
$string['other_missinglf'] = 'Missing LF at end of file (use exactly one)';
$string['other_extralfs'] = 'Extra blank line(s) at end of file (use exactly one)';
$string['filesfound'] = 'Files found: {$a}';
$string['filesummary'] = '{$a->path} - {$a->count}';
$string['info'] = '<p>Checks code against some aspects of the {$a->link}.</p>
<p>Enter a path relative to the Moodle code root, for example: {$a->path}.</p>
<p>You can enter either a specific PHP file, or to a folder to check all the files it contains.</p>';
$string['invalidpath'] = 'Invalid path {$a}';
$string['moodlecodingguidelines'] = 'Moodle coding guidelines';
$string['numerrorswarnings'] = '{$a->numErrors} error(s) and {$a->numWarnings} warning(s)';
$string['path'] = 'Path to check';
$string['pluginname'] = 'Code checker';
$string['recheckfile'] = 'Re-check just this file';
$string['success'] = 'Well done!';
$string['summary'] = 'Total: {$a}';
$string['wholefile'] = 'File';
