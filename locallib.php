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
        $mform = $this->_form;

        $a = new stdClass();
        $a->link = html_writer::link('http://docs.moodle.org/dev/Coding_style',
                get_string('moodlecodingguidelines', 'local_codechecker'));
        $a->path = html_writer::tag('tt', 'local/codechecker');
        $a->excludeexample = html_writer::tag('tt', 'db, backup/*1, *lib*');
        $mform->addElement('static', '', '', get_string('info', 'local_codechecker', $a));

        $mform->addElement('textarea', 'path', get_string('path', 'local_codechecker'), ['rows' => '4', 'cols' => 48]);
        $mform->setType('path', PARAM_RAW);
        $mform->addRule('path', null, 'required', null, 'client');

        $mform->addElement('text', 'exclude', get_string('exclude', 'local_codechecker'), array('size' => '48'));
        $mform->setType('exclude', PARAM_NOTAGS);
        $mform->setDefault('exclude', '');

        $mform->addElement('advcheckbox', 'includewarnings', get_string('includewarnings', 'local_codechecker'));
        $mform->setType('includewarnings', PARAM_BOOL);

        $mform->addElement('submit', 'submitbutton', get_string('check', 'local_codechecker'));
    }
}

// These classes are not following the moodle standard but phpcs one,
// so we intruct the checker to ignore them
// TODO: Move these classes to own php files.
// @codingStandardsIgnoreStart
/**
 * Code sniffer insists on having an PHP_CodeSniffer_CLI, even though we don't
 * really want one. This is a dummy class to make it work.
 */
class local_codechecker_codesniffer_cli extends PHP_CodeSniffer_CLI {

    private $report = 'full';
    private $reportfile = null;

    /** Constructor */
    public function __construct() {
        // Horrible, cannot be set programatically.
        $this->errorSeverity = 1;
        $this->warningSeverity = 1;
    }

    /** Set the report to use */
    public function setReport($report) {
        $this->report = $report;
    }

    /** Set the reportfile to use */
    public function setReportFile($reportfile) {
        $this->reportfile = $reportfile;
    }

    /** Set the warnings flag to use */
    public function setIncludeWarnings($includewarnings) {
        $this->warningSeverity = (int)$includewarnings;
    }

    /* Overload method to inject our settings */
    public function getCommandLineValues() {

        // Inject our settings to defaults.
        $defaults = array_merge(
            $this->getDefaults(),
            array(
                'reports' => array($this->report => $this->reportfile),
            )
        );
        return $defaults;
    }
}

/**
 * Custom XML reporting returning files without violations.
 *
 * By default the CodeSniffer reporting does not return information
 * about files with 0 errors and 0 warnings anymore. But in the web
 * UI of local_codechecker we want to show, with a lovely green, all
 * the passed files.
 *
 * So this is extending {@link PHP_CodeSniffer_Reports_Xml} to modify
 * every bit needed to get those files reported. The extension will
 * try to rely in the original report as much as possible.
 *
 * This has been reported @ https://pear.php.net/bugs/bug.php?id=20202
 */
class PHP_CodeSniffer_Reports_local_codechecker extends PHP_CodeSniffer_Reports_Xml {
    /**
     * Generate a partial report for a single processed file.
     *
     * For files with violations delegate processing to parent class. For files
     * without violations, just return the plain <file> element, without any err/warn.
     *
     * @param array $report Prepared report data.
     * @param PHP_CodeSniffer_File $phpcsFile The file being reported on.
     * @param boolean $showSources Show sources?
     * @param int $width Maximum allowed line width.
     *
     * @return boolean
     */
    public function generateFileReport($report, PHP_CodeSniffer_File $phpcsFile, $showSources = false, $width = 80) {

        // Report has violations, delegate to parent standard processing.
        if ($report['errors'] !== 0 || $report['warnings'] !== 0) {
            return parent::generateFileReport($report, $phpcsFile, $showSources, $width);
        }

        // Here we are, with a file with 0 errors and warnings.
        $out = new XMLWriter;
        $out->openMemory();
        $out->setIndent(true);

        $out->startElement('file');
        $out->writeAttribute('name', $report['filename']);
        $out->writeAttribute('errors', $report['errors']);
        $out->writeAttribute('warnings', $report['warnings']);

        $out->endElement();
        echo $out->flush();

        return true;

    }
}

// End of phpcs classes, we end ignoring now.
// @codingStandardsIgnoreEnd

/**
 * Convert a full path name to a relative one, for output.
 * @param string $file a full path name of a file.
 * @return string the prettied up path name.
 */
function local_codechecker_pretty_path($file) {
    global $CFG;
    return substr($file, strlen($CFG->dirroot) + 1);
}

/**
 * Get a list of folders to ignores.
 *
 * @param string $extraignorelist optional comma separated list of substring matching paths to ignore.
 * @return array of paths.
 */
function local_codesniffer_get_ignores($extraignorelist = '') {
    global $CFG;

    $files = array(); // XML files to be processed.
    $paths = array(); // Absolute paths to be excluded.

    $files['core'] = $CFG->libdir . DIRECTORY_SEPARATOR . '/thirdpartylibs.xml'; // This one always exists.

    // With MDL-42148, for 2.6 and upwards, the general 'thirdpartylibs.xml' file
    // has been split so any plugin with dependencies can have its own. In order to
    // keep master compatibility with older branches we are doing some
    // conditional coding here.
    if (file_exists($CFG->dirroot . '/' . $CFG->admin . '/' . 'thirdpartylibs.php')) {
        // New behavior, distributed XML files, let's look for them.
        $plugintypes = core_component::get_plugin_types();
        foreach ($plugintypes as $type => $ignored) {
            $plugins = core_component::get_plugin_list_with_file($type, 'thirdpartylibs.xml', false);
            foreach ($plugins as $plugin => $path) {
                $files[$type.'_'.$plugin] = $path;
            }
        }
    }

    // Let's extract all the paths from the XML files.
    foreach ($files as $file) {
        $base = realpath(dirname($file));
        $thirdparty = simplexml_load_file($file);
        foreach ($thirdparty->xpath('/libraries/library/location') as $location) {
            $location = substr($base, strlen($CFG->dirroot)) . '/' . $location;
            // This was happening since ages ago, leading to incorrect excluded
            // paths like: "/lib/theme/bootstrapbase/less/bootstrap", so we try
            // reducing it. Note this does not affect 2.6 and up, where all
            // locations are relative to their xml file so this problem cannot happen.
            if (!file_exists(dirname($CFG->dirroot . DIRECTORY_SEPARATOR . $location))) {
                // Only if it starts with '/lib'.
                if (strpos($location, DIRECTORY_SEPARATOR . 'lib') === 0) {
                    $candidate = substr($location, strlen(DIRECTORY_SEPARATOR . 'lib'));
                    // Only modify the original location if the candidate exists.
                    if (file_exists(dirname($CFG->dirroot . DIRECTORY_SEPARATOR . $candidate))) {
                        $location = $candidate;
                    }
                }
            }
            // Accept only correct paths from XML files.
            if (file_exists(dirname($CFG->dirroot . DIRECTORY_SEPARATOR . $location))) {
                $paths[] = preg_quote(local_codechecker_clean_path($location));
            } else {
                debugging("Processing $file for exclussions, incorrect $location path found. Please fix it");
            }
        }
    }

    // Manually add our own pear stuff to be excluded.
    $paths[] = preg_quote(local_codechecker_clean_path(
            '/local/codechecker' . DIRECTORY_SEPARATOR . 'pear'));

    // Changed in PHP_CodeSniffer 1.4.4 and upwards, so we apply the
    // same here: Paths go to keys and mark all them as 'absolute'.
    $finalpaths = array();
    foreach ($paths as $pattern) {
        $finalpaths[$pattern] = 'absolute';
    }

    // Let's add any substring matching path passed in $extraignorelist.
    if ($extraignorelist) {
        $extraignorearr = explode(',', $extraignorelist);
        foreach ($extraignorearr as $extraignore) {
            $extrapath = trim($extraignore);
            $finalpaths[$extrapath] = 'absolute';
        }
    }

    // Ignore any compiled JS and test fixtures.
    $finalpaths['*/amd/build/*'] = 'absolute';
    $finalpaths['*/yui/build/*'] = 'absolute';
    if (!defined('BEHAT_SITE_RUNNING')) { // We need testing fixtures at hand for testing purposes, heh.
        $finalpaths['*/tests/fixtures/*'] = 'absolute';
    }

    return $finalpaths;
}

/** Get the source code for a given file and line */
function local_codechecker_get_line_of_code($line, $prettypath) {
    global $CFG;

    static $lastfilename = null;
    static $file = null;

    if ($prettypath != $lastfilename) {
        $file = file($CFG->dirroot . '/' . $prettypath);
        $lastfilename = $prettypath;
    }
    $linecontents = $file[$line - 1];
    // Handle empty lines.
    if (trim($linecontents) === '') {
        $linecontents = '&#x00d8;';
    }

    return $linecontents;
}

/**
 * The code-checker code assumes that paths always use DIRECTORY_SEPARATOR,
 * whereas Moodle is more relaxed than that. This method cleans up file paths by
 * converting all / and \ to DIRECTORY_SEPARATOR. It should be used whenever a
 * path is passed to the CodeSniffer library.
 * @param string $path a file path
 * @return string The path with all directory separators changed to DIRECTORY_SEPARATOR.
 */
function local_codechecker_clean_path($path) {
    return str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $path);
}

/**
 * Recursively finds all files within a folder that match particular extensions.
 * @param array &$arr Array to add file paths to
 * @param string $folder Path to search (or may be a single file)
 * @param array $extensions File extensions to include (not including .)
 */
function local_codechecker_find_other_files(&$arr, $folder,
        $extensions = array('txt', 'html', 'csv')) {
    $regex = '~\.(' . implode('|', $extensions) . ')$~';

    // Handle if this is called directly with a file and not folder.
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
 *
 * @param SimpleXMLElement $fileinxml structure to which new problem will be added.
 * @param string $file File path
 * @param int $line Line number (1-based)
 * @param string $key key within language file ('other_' will be prepended)
 * @param bool $warning if true is warning, otherwise error
 */
function local_codechecker_add_problem($fileinxml, $file, $line, $key, $warning=false) {
    $type = $warning ? 'warning' : 'error';
    $counter = $warning ? 'warnings' : 'errors';

    // Add the new problem.
    $newproblem = $fileinxml->addChild($type, get_string('other_' . $key, 'local_codechecker'));
    $newproblem->addAttribute('line', $line);
    $newproblem->addAttribute('column', 0);
    $newproblem->addAttribute('source', 'other.' . $key);
    $newproblem->addAttribute('severity', $warning ? PHPCS_DEFAULT_WARN_SEV : PHPCS_DEFAULT_ERROR_SEV);

    // Increment error/warning counters.
    $fileinxml[$counter] = $fileinxml[$counter] + 1;
}

/**
 * Checks an individual other file and adds basic problems to result.
 * @param string $file File to check
 * @param SimpleXMLElement $xml structure containin all violations
 *   to which new problems will be added.
 */
function local_codechecker_check_other_file($file, $xml) {

    // If the file does not exist, add it.
    $fileinxml = $xml->xpath("file[@name='$file']");
    if (!count($fileinxml)) {
        $fileinxml = $xml->addChild('file');
        $fileinxml->addAttribute('name', $file);
        $fileinxml->addAttribute('errors', 0);
        $fileinxml->addAttribute('warnings', 0);
    }

    // Certain files are permitted lines of any length because they are
    // Auto-generated.
    $allowanylength = in_array(basename($file), array('install.xml')) ||
            substr($file, -4, 4) === '.csv';

    $lines = file($file);
    $index = 0;
    $blankrun = 0;
    foreach ($lines as $l) {
        $index++;
        // Incorrect [Windows] line ending.
        if ((strpos($l, "\r\n") !== false) && empty($donecrlf)) {
            local_codechecker_add_problem($fileinxml, $file, $index, 'crlf');
            $donecrlf = true;
        }
        // Missing line ending (at EOF presumably).
        if (strpos($l, "\n") === false) {
            local_codechecker_add_problem($fileinxml, $file, $index, 'missinglf');
        }
        $l = rtrim($l);
        if ($l === '') {
            $blankrun++;
        } else {
            $blankrun = 0;
        }

        // Whitespace at EOL.
        if (preg_match('~ +$~', $l)) {
            local_codechecker_add_problem($fileinxml, $file, $index, 'eol');
        }
        // Tab anywhere in line.
        if (preg_match('~\t~', $l)) {
            local_codechecker_add_problem($fileinxml, $file, $index, 'tab');
        }

        if (strlen($l) > 180 && !$allowanylength) {
            // Line length > 180.
            local_codechecker_add_problem($fileinxml, $file, $index, 'toolong');
        } else if (strlen($l) > 132 && !$allowanylength) {
            // Line length > 132.
            local_codechecker_add_problem($fileinxml, $file, $index, 'ratherlong', true);
        }
    }
    if ($blankrun > 0) {
        local_codechecker_add_problem($fileinxml, $file, $index - $blankrun, 'extralfs');
    }
}

/**
 * Checking the parts that PHPCodeSniffer can't reach (i.e. anything except
 * php, css, js) for basic whitespace problems.
 * @param string $path Path to search (may be file or folder)
 * @param SimpleXMLElement $xml structure containin all violations.
 *   to which new problems will be added
 */
function local_codechecker_check_other_files($path, $xml) {
    $files = array();
    local_codechecker_find_other_files($files, $path);
    foreach ($files as $file) {
        local_codechecker_check_other_file($file, $xml);
    }
}

/**
 * Calculate the total number of errors and warnings in the execution
 *
 * @param SimpleXMLElement $xml structure containin all violations
 *   for which total number of errors and warnings will be counted.
 * @return array with the total count of errors and warnings.
 */
function local_codechecker_count_problems($xml) {
    $errors = 0;
    $warnings = 0;
    // Get all the files in the xml.
    $files = $xml->xpath('file');
    foreach ($files as $file) {
        $errors += $file['errors'];
        $warnings += $file['warnings'];
    }
    return array($errors, $warnings);
}
