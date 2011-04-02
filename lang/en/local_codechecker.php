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
Example:
  php run.php local/codechecker';
$string['error_find'] = 'Folder search failed';
$string['fail_eol'] = 'Whitespace at end of line';
$string['fail_tab'] = 'Tab in line';
$string['fail_keywordspace'] = 'Missing space after language keyword and before (';
$string['fail_spacebeforebrace'] = 'Missing space between ) and {';
$string['fail_spaceaftercomma'] = 'Missing space after a comma';
$string['fail_closephp'] = 'Closing PHP tag not permitted';
$string['fail_windows'] = 'File contains Windows line endings (fix with Eclipse File / Convert Line Delimiters To)';
$string['fail_noheader'] = 'Does not precisely contain standard header (the // part)';
$string['fail_eoflf'] = 'File must end with precisely one line feed';
$string['fail_toolong'] = 'Lines should not be longer than {$a->max} characters (actual length {$a->actual})';
$string['fail_varname'] = 'Variable names must be all lower case, no _ ({$a})';
$string['filesfound'] = 'Files found: {$a}';
$string['filesummary'] = '{$a->path} ({$a->count})';
$string['info'] = '<p>Checks code against some aspects of the {$a->link}.</p>
<p>Enter a path relative to the Moodle code root, for example: {$a->path}.</p>
<p>You can enter either a specific PHP file, or to a folder to check all the files it contains.</p>';
$string['invalidpath'] = 'Invalid path {$a}';
$string['moodlecodingguidelines'] = 'Moodle coding guidelines';
$string['path'] = 'Path to check';
$string['pluginname'] = 'Code checker';
$string['success'] = 'Well done!';
$string['summary'] = '{$a} error(s)';
$string['wholefile'] = 'File';
