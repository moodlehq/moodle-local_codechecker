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


require_once($CFG->dirroot . '/lib/formslib.php');


/**
 * Settings form for the code checker.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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
 * Class to do the checking.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_codechecker {
    /** @var int max line length to enforce. */
    const MAX_LINE_LENGTH = 100;

    /** @var array list if directory entires ignored by @link find_all_files()}. */
    protected $ignored = array('.', '..', '.git');

    /** @var string root directory of the codebase to search inside. */
    protected $dirroot;

    /** @var array the list of files to check. */
    protected $files;

    /**
     * Constructor.
     * @param string $dirroot full path of the root of the codebase to search inside.
     */
    public function __construct($dirroot) {
        $this->dirroot = $dirroot;
    }

    /**
     * Factory method. Make a checker to check a particular file or subfolder in $dirroot.
     * @param string $dirroot full path of the root of the codebase to search inside.
     * @param string $path path of the file or folder to check, relative to $dirroot.
     *      It should not have a leading /.
     * @return local_codechecker|null if path was valid a checker, otherwise null.
     */
    public static function create($dirroot, $path) {
        $checker = new self($dirroot);

        $fullpath = $dirroot . '/' . $path;
        if (is_file($fullpath)) {
            $files = array($fullpath);
        } else if (is_dir($fullpath)) {
            $files = $checker->find_all_files($fullpath);
            sort($files);
        } else {
            return null;
        }

        $checker->set_files($files);
        return $checker;
    }

    /**
     * Whether a particular files can be checked
     * @param string $file the full path name of a file.
     * @return whether we can check it.
     */
    public function is_checkable($file) {
        return preg_match('~\\.php$~', $file);
    }

    /**
     * Set the list of files to check. The list is fitered by {@link is_checkable()}.
     * @param array $files of ful path names of files.
     */
    public function set_files($files) {
        // Remove all non-php files from array
        foreach ($files as $i => $file) {
            if (!$this->is_checkable($file)) {
                unset($files[$i]);
            }
        }

        $this->files = $files;
    }

    /**
     * Recursively finds all files within a folder.
     * @param array $files Array to add file paths to.
     * @param string $folder Path to search
     * @return array updated files array.
     */
    public function find_all_files($folder, $files = array()) {
        if (!$handle = opendir($folder)) {
            throw new moodle_exception('error_find', 'local_codechecker');
        }

        while (($file = readdir($handle)) !== false) {
            $fullpath = $folder . '/' . $file;
            if (in_array($file, $this->ignored)) {
                continue;
            } else if (is_file($fullpath)) {
                $files[] = $fullpath;
            } else if (is_dir($fullpath)) {
                $files = $this->find_all_files($fullpath, $files);
            }
        }

        closedir($handle);
        return $files;
    }

    /**
     * Output summary results for checking all the files.
     * @param local_codechecker_renderer $output to generate the output
     */
    public function summary(local_codechecker_renderer $output) {
        echo $output->summary_start(count($this->files));

        $totalproblems = 0;
        foreach ($this->files as $i => $file) {
            $numproblems = count($this->check_php_file($file));
            echo $output->summary_line($i, $this->pretty_path($file), $numproblems);
            $totalproblems += $numproblems;
        }

        echo $output->summary_end(count($this->files));

        return $totalproblems;
    }

    /**
     * Convert a full path name to a relative one, for output.
     * @param string $file a full path name of a file.
     * @return string the prittied up path name.
     */
    public function pretty_path($file) {
        return substr($file, strlen($this->dirroot) + 1);
    }

    /**
     * Output full results for checking all the files.
     * @param local_codechecker_renderer $output to generate the output
     */
    public function check(local_codechecker_renderer $output) {
        foreach ($this->files as $i => $file) {
            $problems = $this->check_php_file($file);
            if ($problems) {
                echo $output->problems($i, $this->pretty_path($file), $problems);
            }
        }
    }

    /**
     * Check a single file.
     * @param string $file the full path name of the file.
     * @return array of {@link local_codechecker_problem}s. The problems found.
     */
    public function check_php_file($file) {
        $problems = array();

        // Whole file checks
        $wholefile = file_get_contents($file);

        // Windows line ending
        if (strpos($wholefile, "\r\n") !== false) {
            $problems[] = new local_codechecker_problem('windows');
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
            $problems[] = new local_codechecker_problem('noheader');
        }

        if (!preg_match('~[^\n]\n$~', $wholefile)) {
            $problems[] = new local_codechecker_problem('eoflf');
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
                $problems[] = new local_codechecker_problem('eol', $index, $line);
            }
            // Tab anywhere in line
            if (preg_match('~\t~', $l)) {
                $problems[] = new local_codechecker_problem('tab', $index, $line);
            }
            // Missing space after if, foreach, etc
            if (preg_match('~\b(if|foreach|for|while|catch|switch)\(~', $l)) {
                $problems[] = new local_codechecker_problem('keywordspace', $index, $line);
            }
            // Missing space between ) and {
            if (preg_match('~\){~', $l)) {
                $problems[] = new local_codechecker_problem('spacebeforebrace', $index, $line);
            }
            // Missing space after comma
            if (preg_match('~\,(?!( |\'[ ,)]|\"[ ,)]|\n))~', $l)) {
                $problems[] = new local_codechecker_problem('spaceaftercomma', $index, $line);
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
                    $problems[] = new local_codechecker_problem('closephp', $index, $line);
                }
            }
            // Very long lines (permitted in lang files)
            if (strlen($line) > local_codechecker::MAX_LINE_LENGTH &&
                    !preg_match('~/lang/~', $file)) {
                $problems[] = new local_codechecker_problem(
                        'toolong', $index, $line, strlen($line));
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
                        $problems[] = new local_codechecker_problem('varname', $index, $line);
                        break;
                    }
                }
            }
        }

        return $problems;
    }
}


/**
 * Class to represent a problem that has been found.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_codechecker_problem {
    /**
     * @var string internal name of this error. Get looked up in the lang file as
     * 'fail_' . $this->err.
     */
    public $code;

    /** @var int the line number on which the problme occurred. */
    public $linenum;

    /** @var string the contents of the problem line. */
    public $line;

    /** @var mixed extra data used to render the message. */
    public $a;

    /**
     * Constructor
     * @param string $code internal name of this error.
     * @param int $linenum the line number on which the problme occurred (optional).
     * @param int $line the contents of the problem line (optional).
     * @param mixed $a extra data used to render the message (optional).
     */
    public function __construct($code, $linenum = null, $line = null, $a = null) {
        $this->linenum = $linenum;
        $this->code = $code;
        $this->line = $line;
        $this->a = $a;
    }

    /**
     * @return string the human-readable message about this failure.
     */
    public function get_message() {
        return get_string('fail_' . $this->code, 'local_codechecker', $this->a);
    }

    /**
     * @return string the line number of which the error occurred, or 'Whole file'.
     */
    public function get_line() {
        if ($this->linenum) {
            return $this->linenum;
        } else {
            return get_string('wholefile', 'local_codechecker');
        }
    }
}
