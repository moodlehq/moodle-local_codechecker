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
 * Main code
 *
 * @copyright &copy; 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package local
 * @subpackage codechecker
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/lib/formslib.php');

define('MAX_LINE_LENGTH', 100);

$path = optional_param('path', '', PARAM_SAFEPATH);
$pageparams = array('path' => $path);

class local_codechecker_form extends moodleform {
    function definition() {
        global $path;
        $mform = $this->_form;
        $mform->addElement('static', '', '', get_string('info', 'local_codechecker'));
        $mform->addElement('text', 'path', get_string('path', 'local_codechecker'));
        $mform->setDefault('path', $path);
        $mform->addElement('submit', 'submitbutton', get_string('check', 'local_codechecker'));
    }
}

/**
 * Recursively finds all files within a folder.
 * @param array &$arr Array to add file paths to
 * @param string $folder Path to search
 */
function local_codechecker_find_all_files(&$arr, $folder) {
    if ($handle = opendir($folder)) {
        while (($file = readdir($handle)) !== false) {
            $fullpath = $folder . '/' . $file;
            if ($file === '.' || $file === '..') {
                continue;
            } else if (is_file($fullpath)) {
                $arr[] = $fullpath;
            } else if (is_dir($fullpath)) {
                local_codechecker_find_all_files($arr, $fullpath);
            }
        }
        closedir($handle);
    } else {
        throw new moodle_exception('error_find', 'local_codechecker');
    }
}

/**
 * @param int $linenum Line number
 * @param string $err Error type (e.g. if 'frog', uses lang string 'fail_frog')
 * @param string $line Content of code line to display
 * @return string Error HTML code (list item)
 */
function local_codechecker_err($linenum, $err, $line=null) {
    global $errors;
    $errors++;
    if ($linenum) {
        $lineinfo = $linenum;
    } else {
        $lineinfo = get_string('wholefile', 'local_codechecker');
    }

    $info = html_writer::tag('div', html_writer::tag('strong', $lineinfo) . ': ' .
            get_string('fail_' . $err, 'local_codechecker'), array('class'=>'info'));
    if ($line) {
        $line = s($line);
        $line = str_replace("\t", '<span>&#2192;</span>', $line);
        $line = str_replace(" ", '<span>&#183;</span>', $line);
        $linedisplay = html_writer::tag('pre', $line);
    } else {
        $linedisplay = '';
    }
    return html_writer::tag('li', $linedisplay . $info, array('class'=>'fail'));
}

/**
 * Check a PHP file
 * @param string $file PHP file full path
 * @return string Result HTML
 */
function local_codechecker_check($file, $i) {
    global $CFG, $errors;
    $errors = 0;
    $out = '';
    $out .= html_writer::start_tag('div', array('class'=>'resultfile', 'id'=>'file' . $i));
    $out .= html_writer::tag('h3', s(substr($file, strlen($CFG->dirroot) + 1)));

    $issues = '';

    // Whole file checks
    $wholefile = file_get_contents($file);

    // Windows line ending
    if (strpos($wholefile, "\r\n") !== false) {
        $issues .= local_codechecker_err(0, 'windows');
    }

    // Check for expected header
    $header = <<<EOT
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
EOT;
    if (strpos(str_replace("<?php\n//", "<?php\n\n//", $wholefile), $header) !== 0) {
        $issues .= local_codechecker_err(0, 'noheader');
    }

    if (!preg_match('~[^\n]\n$~', $wholefile)) {
        $issues .= local_codechecker_err(0, 'eoflf');
    }

    // Line-by-line checks
    $lines = file($file);
    $index = 0;
    foreach ($lines as $line) {
        $index++;

        // Automatically exclude regular expressions (unless they are formatted
        // across lines, we're not that clever)
        $l = preg_replace('~preg_(match|replace)\(\'.*?\',~', 'ignore', $line);

        // Whitespace at EOL
        if (preg_match('~ +$~', $l)) {
            $issues .= local_codechecker_err($index, 'eol', $line);
        }
        // Tab anywhere in line
        if (preg_match('~\t~', $l)) {
            $issues .= local_codechecker_err($index, 'tab', $line);
        }
        // Missing space after if, foreach, etc
        if (preg_match('~\b(if|foreach|for|while|catch|switch)\(~', $l)) {
            $issues .= local_codechecker_err($index, 'keywordspace', $line);
        }
        // Missing space between ) and {
        if (preg_match('~\){~', $l)) {
            $issues .= local_codechecker_err($index, 'spacebeforebrace', $line);
        }
        // Missing space after comma
        if (preg_match('~\,(?!( |\'[ ,)]|\"[ ,)]|\n))~', $l)) {
            $issues .= local_codechecker_err($index, 'spaceaftercomma', $line);
        }
        // Closing PHP tag
        if (preg_match('~\?\>\s*$~', $l)) {
            // Check it's at end of file
            $ok = false;
            for ($i=$index; $i < count($lines); $i++) {
                if (preg_match('~\S~', $lines[$i])) {
                    $ok = true;
                    break;
                }
            }
            if (!$ok) {
                $issues .= local_codechecker_err($index, 'closephp', $line);
            }
        }
        // Very long lines (permitted in lang files)
        if (strlen($line) > MAX_LINE_LENGTH && !preg_match('~/lang/~', $file)) {
            $issues .= local_codechecker_err($index, 'toolong', $line);
        }
        // Wrong variable names
        $matches = array();
        if (preg_match_all('~\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*~', $l,
                $matches)) {
            foreach ($matches[0] as $varname) {
                // Check for normal name (lower-case) or global (upper, underline
                // allowed for statics)
                if (!preg_match('~^\$[a-z0-9]+$~', $varname) &&
                        !preg_match('~^\$[A-Z0-9_]+$~', $varname)) {
                    $issues .= local_codechecker_err($index, 'varname', $line);
                    break;
                }
            }
        }
    }

    if ($issues) {
        $out .= html_writer::tag('ul', $issues);
    }

    if (!$errors) {
        $out .= html_writer::tag('p', get_string('summary_ok', 'local_codechecker'));
    } else {
        $out .= html_writer::tag('p', get_string('summary', 'local_codechecker', $errors),
                array('class'=>'fail'));
    }
    $out .= html_writer::end_tag('div');
    return $out;
}


$context = get_context_instance(CONTEXT_SYSTEM);
$pagename = get_string('pluginname', 'local_codechecker');
$PAGE->set_url(new moodle_url('/local/codechecker/', $pageparams));
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title($SITE->fullname. ': ' . $pagename);
$PAGE->navbar->add($pagename);

require_login();
require_capability('moodle/site:config', $context);

$mform = new local_codechecker_form('./');

if ($data = $mform->get_data()) {
    redirect('./?path=' . urlencode($data->path));
}

print $OUTPUT->header();

if ($path) {
    // Remove / from start and end of path if included
    $path = preg_replace('~^/?(.*?)/?$~', '$1', $path);

    // Search for all files in that path
    $fullpath = $CFG->dirroot . '/' . $path;
    if (is_file($fullpath)) {
        $files = array($fullpath);
    } else if (is_dir($fullpath)) {
        $files = array();
        local_codechecker_find_all_files($files, $fullpath);
        sort($files);
    } else {
        print $OUTPUT->notification(get_string('invalidpath', 'local_codechecker'));
        $files = array();
    }

    // Remove all non-php files from array
    foreach ($files as $i=>$file) {
        if (!preg_match('~\\.php$~', $file)) {
            unset($files[$i]);
        }
    }

    print html_writer::tag('h2', get_string('filesfound', 'local_codechecker', count($files)));
    if (count($files)) {
        $details = '';
        print html_writer::start_tag('ul');
        foreach ($files as $i => $file) {
            $results = local_codechecker_check($file, $i);
            $numproblems = substr_count($results, '<div class="info"><strong>');
            $prettypath = s(substr($file, strlen($CFG->dirroot) + 1));

            if ($numproblems) {
                $details .= $results;
                $a = new stdClass();
                $a->path = $prettypath;
                $a->count = $numproblems;
                print html_writer::start_tag('li', array('class' => 'fail'));
                print html_writer::tag('a', get_string('filesummary', 'local_codechecker', $a),
                        array('href' => '#file'.$i));
                print html_writer::end_tag('li');
            } else {
                print html_writer::tag('li', $prettypath, array('class' => 'good'));
            }
        }
        print html_writer::end_tag('ul');

        if ($details) {
            print html_writer::tag('div', $details, array('class' => 'checkresults'));
        }
    }
}

$mform->display();

print $OUTPUT->footer();
