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
 * Code checker renderers.
 *
 * @package    local
 * @subpackage codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;


/**
 * Renderer for displaying code-checker reports as HTML.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_codechecker_renderer extends plugin_renderer_base {
    /** @var array string replaces used to clean up the input line for display. */
    protected $replaces = array(
        "\t" => '<span>&#2192;</span>',
        ' '  => '<span>&#183;</span>',
    );

    /**
     * Display the start of the list of the files checked.
     * @param int $numfiles the number of files checked.
     * @return string HTML to output.
     */
    public function summary_start($numfiles) {
        return html_writer::tag('h2', get_string('filesfound', 'local_codechecker', $numfiles)) .
                html_writer::start_tag('ul');
    }

    /**
     * Display an entry in the list of the files checked.
     * @param int $fileindex unique index of this file.
     * @param string $prettypath the name of the file checked.
     * @param int $numproblems the number of problems found in this file.
     * @return string HTML to output.
     */
    public function summary_line($fileindex, $prettypath, $numproblems) {
        if ($numproblems) {
            return html_writer::tag('li', html_writer::link(new moodle_url('#file' . $fileindex),
                    get_string('filesummary', 'local_codechecker',
                        array('path' => s($prettypath), 'count' => $numproblems))),
                    array('class' => 'fail'));
        } else {
            return html_writer::tag('li', s($prettypath), array('class' => 'good'));
        }
    }

    /**
     * Display the end of the list of the files checked.
     * @param int $numfiles the number of files checked.
     * @param int $totalproblems the total number of problems found.
     * @return string HTML to output.
     */
    public function summary_end($numfiles, $totalproblems) {
        $output = html_writer::end_tag('ul');
        if ($totalproblems > 0) {
            $output .= html_writer::tag('p', get_string('summary', 'local_codechecker',
                    $totalproblems), array('class' => 'fail'));
        } else {
            $output .= html_writer::tag('p', get_string('success', 'local_codechecker'),
                    array('class' => 'good'));
        }
        return $output;
    }

    /**
     * Display a message about the path being invalid.
     * @param string $path the invaid path.
     * @return string HTML to output.
     */
    public function invald_path_message($path) {
        return $this->output->notification(get_string(
                'invalidpath', 'local_codechecker', s($path)));
    }

    /**
     * Display the full results of checking a file. Will only be called if
     * $problems is a non-empty array.
     * @param int $fileindex unique index of this file.
     * @param string $prettypath the name of the file checked.
     * @param array $problems of {@link local_codechecker_problem}s. The problems found.
     * @return string HTML to output.
     */
    public function problems($fileindex, $prettypath, $problems) {
        $output = html_writer::start_tag('div',
                array('class'=>'resultfile', 'id'=>'file' . $fileindex));
        $output .= html_writer::tag('h3', s($prettypath));
        $output .= html_writer::start_tag('ul');

        foreach ($problems as $problem) {
            $output .= $this->problem_message($problem);
        }

        $output .= html_writer::end_tag('ul');
        $output .= html_writer::tag('p', get_string('summary', 'local_codechecker',
                count($problems)), array('class'=>'fail'));
        $output .= html_writer::end_tag('div');

        return $output;
    }

    /**
     * Display the message about one particular problem.
     * @param local_codechecker_problem $problem
     */
    public function problem_message(local_codechecker_problem $problem) {
        $info = html_writer::tag('div', html_writer::tag('strong', $problem->get_line()) . ': ' .
                $problem->get_message(), array('class'=>'info'));

        if ($problem->code) {
            $info = html_writer::tag('pre', str_replace(
                    array_keys($this->replaces), array_values($this->replaces),
                    s($problem->code))) . $info;
        }

        return html_writer::tag('li', $info, array('class' => 'fail ' . $problem->shortname));
    }
}


/**
 * Renderer for displaying code-checker reports on the command line.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_codechecker_renderer_cli extends local_codechecker_renderer {
    /** @var array string replaces used to clean up the input line for display. */
    protected $replaces = array(
        "\t" => '\t',
        ' '  => '·',
    );

    /**
     * Display the start of the list of the files checked.
     * @param int $numfiles the number of files checked.
     * @return string HTML to output.
     */
    public function summary_start($numfiles) {
        return get_string('filesfound', 'local_codechecker', $numfiles) . "\n";
    }

    /**
     * Display an entry in the list of the files checked.
     * @param int $fileindex unique index of this file.
     * @param string $prettypath the name of the file checked.
     * @param int $numproblems the number of problems found in this file.
     * @return string HTML to output.
     */
    public function summary_line($fileindex, $prettypath, $numproblems) {
        return '  ' . get_string('filesummary', 'local_codechecker',
                        array('path' => s($prettypath), 'count' => $numproblems)) . "\n";
    }

    /**
     * Display the end of the list of the files checked.
     * @param int $numfiles the number of files checked.
     * @param int $totalproblems the total number of problems found.
     * @return string HTML to output.
     */
    public function summary_end($numfiles, $totalproblems) {
        if ($totalproblems) {
            return get_string('summary', 'local_codechecker', $totalproblems) . "\n";
        }
        return '';
    }

    /**
     * Display a message about the path being invalid.
     * @param string $path the invaid path.
     * @return string HTML to output.
     */
    public function invald_path_message($path) {
        return get_string('invalidpath', 'local_codechecker', $path) . "\n";
    }

    /**
     * Display the full results of checking a file. Will only be called if
     * $problems is a non-empty array.
     * @param int $fileindex unique index of this file.
     * @param string $prettypath the name of the file checked.
     * @param array $problems of {@link local_codechecker_problem}s. The problems found.
     * @return string HTML to output.
     */
    public function problems($fileindex, $prettypath, $problems) {
        $output = "\n" . $prettypath . "\n";
        foreach ($problems as $problem) {
            $output .= $this->problem_message($problem);
        }
        $output .= get_string('summary', 'local_codechecker', count($problems)) . "\n";
        return $output;
    }

    /**
     * Display the message about one particular problem.
     * @param local_codechecker_problem $problem
     */
    public function problem_message(local_codechecker_problem $problem) {
        $info = $problem->get_line() . ': ' . $problem->get_message() . "\n";

        return '  ' . $info;
    }
}
