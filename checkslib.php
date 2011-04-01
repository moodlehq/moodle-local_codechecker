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
 * Classes that peform particular checks.
 *
 * @package    local
 * @subpackage codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Whole file check base class.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class local_codechecker_file_check {
    /**
     * Perform the check
     * @param string $code the code to check.
     * @param string $file the full path name of the file being checked.
     * @return array of {@link local_codechecker_problem}s. The problems found.
     */
    abstract public function check($code, $file);
}


/**
 * Check the whole file using a regex that should not match anywhere.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_codechecker_preg_file_check extends local_codechecker_file_check {
    /** @var string the shortname. Used when constructing {@local_codechecker_problem}s. */
    protected $shortname;

    /** @var string the regular expression that must not match the code. */
    protected $regex;

    /**
     * Constructor
     * @param string $shortname string the shortname.
     *      Used when constructing {@local_codechecker_problem}s.
     * @param string $regex the regular expression that must not match the code.
     */
    public function __construct($shortname, $regex) {
        $this->shortname = $shortname;
        $this->regex = $regex;
    }

    public function check($code, $file) {
        if (preg_match($this->regex, $code)) {
            return array(new local_codechecker_problem($this->shortname));
        }
        return array();
    }
}


/**
 * Check the file to make sure it has the standard boiler-plate comment at the top.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_codechecker_header_file_check extends local_codechecker_file_check {
    /** @var string the standard header that should be present. */
    protected $header;

    /**
     * Constructor
     */
    public function __construct() {
        $this->header = <<<EOT
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
    }

    /**
     * Constructor
     */
    public function check($code, $file) {
        if (strpos(str_replace("<?php\n//", "<?php\n\n//", $code), $this->header) !== 0) {
            return array(new local_codechecker_problem('noheader'));
        }
        return array();
    }
}


/**
 * Single line check base class.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class local_codechecker_line_check {
    /**
     * Perform the check
     * @param string $code the code to check.
     * @param int $linenumber the number of this line in the file.
     * @param string $file the full path name of the file being checked.
     * @return array of {@link local_codechecker_problem}s. The problems found.
     */
    abstract public function check($code, $linenumber, $file);
}


/**
 * Check a line using a regex that should not match.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_codechecker_preg_line_check extends local_codechecker_line_check {
    /** @var string the shortname. Used when constructing {@local_codechecker_problem}s. */
    protected $shortname;

    /** @var string the regular expression that must not match the code. */
    protected $regex;

    /**
     * Constructor
     * @param string $shortname string the shortname.
     *      Used when constructing {@local_codechecker_problem}s.
     * @param string $regex the regular expression that must not match the code.
     */
    public function __construct($shortname, $regex) {
        $this->shortname = $shortname;
        $this->regex = $regex;
    }

    public function check($code, $linenumber, $file) {
        if (preg_match($this->regex, $code)) {
            return array(new local_codechecker_problem($this->shortname, $linenumber, $code));
        }
        return array();
    }
}


/**
 * Check a line using a regex that should not match.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_codechecker_line_length_check extends local_codechecker_line_check {
    /** @var string the shortname. Used when constructing {@local_codechecker_problem}s. */
    protected $max;

    /**
     * Constructor
     * @param int $maxlength the maximum permitted length.
     */
    public function __construct($maxlength) {
        $this->max = $maxlength;
    }

    public function check($code, $linenumber, $file) {
        // Very long lines are permitted in lang files.
        if (strlen($code) > $this->max && !preg_match('~/lang/~', $file)) {
            return array(new local_codechecker_problem('toolong', $linenumber, $code,
                    array('max' => $this->max, 'actual' => strlen($code))));
        }
        return array();
    }
}


/**
 * Check a line using a regex that should not match.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_codechecker_variable_name_check extends local_codechecker_line_check {
    public function check($code, $linenumber, $file) {
        $matches = array();
        if (!preg_match_all('~\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*~', $code, $matches)) {
            return array();
        }

        $problems = array();
        foreach ($matches[0] as $varname) {
            // Check for normal name (lower-case) or global (upper, underline allowed for statics)
            if (!preg_match('~^\$([a-z0-9]+|[A-Z0-9_]+)$~', $varname)) {
                $problems[] = new local_codechecker_problem('varname', $linenumber,
                        $code, $varname);
            }
        }
        return $problems;
    }
}
