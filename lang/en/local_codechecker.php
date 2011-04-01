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

$string['pluginname'] = 'Code checker';

$string['info'] = '<p>Checks code for whitespace and similar errors within a given path.</p>
<p>You can enter a path to a specific PHP file or to a folder, in which case all subfolders will be checked.</p>
<p>Paths are relative to Moodle code root, for example: <tt>local/codechecker</tt></p>';
$string['path'] = 'Path to check';
$string['check'] = 'Check code';

$string['filesfound'] = 'Found files: {$a}';

$string['invalidpath'] = 'Invalid path';

$string['error_find'] = 'Folder search failed';

$string['wholefile'] = 'File';
$string['summary'] = '{$a} error(s)';
$string['summary_ok'] = 'No errors or warnings';

$string['fail_eol'] = 'Whitespace at end of line';
$string['fail_tab'] = 'Tab in line';
$string['fail_keywordspace'] = 'Missing space after language keyword and before (';
$string['fail_spacebeforebrace'] = 'Missing space after ) and before {';
$string['fail_spaceaftercomma'] = 'Missing space after comma';
$string['fail_closephp'] = 'Closing PHP tag not permitted';
$string['fail_windows'] = 'File contains Windows line endings (fix with Eclipse File / Convert Line Delimiters To)';
$string['fail_noheader'] = 'Does not precisely contain standard header (the // part)';
$string['fail_eoflf'] = 'File must end with precisely one line feed';
$string['fail_toolong'] = 'Lines should not be longer than {$a->max} characters (actual length {$a->actual})';
$string['fail_varname'] = 'Variable names must be all lower case, no _ ({$a})';
$string['filesummary'] = '{$a->path} ({$a->count})';
