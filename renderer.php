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
 * @package    local_codechecker
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
        "\t" => '<span>&#x25b6;</span>',
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
        if ($totalproblems) {
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

    public function report(array $problems, PHP_CodeSniffer $phpcs, $totalproblems) {
        $output = '';

        $numfiles = count($problems);
        $output .= $this->summary_start($numfiles);

        $index = 0;
        foreach ($problems as $file => $info) {
            $index++;

            $summary = '';
            if ($info['numErrors'] + $info['numWarnings'] > 0) {
                $summary = get_string('numerrorswarnings', 'local_codechecker', $info);
            }

            $output .= $this->summary_line($index, local_codechecker_pretty_path($file), $summary);
        }
        $output .= $this->summary_end($numfiles, $totalproblems);

        $index = 0;
        foreach ($problems as $file => $info) {
            $index++;

            if ($info['numErrors'] + $info['numWarnings'] == 0) {
                continue;
            }

            $output .= $this->problems($index, local_codechecker_pretty_path($file), $info);
        }

        return $output;
    }

    /**
     * Display the full results of checking a file. Will only be called if
     * $problems is a non-empty array.
     * @param int $fileindex unique index of this file.
     * @param string $prettypath the name of the file checked.
     * @param array $problems the problems found.
     * @return string HTML to output.
     */
    public function problems($fileindex, $prettypath, $info) {
        $output = html_writer::start_tag('div',
                array('class'=>'resultfile', 'id'=>'file' . $fileindex));
        $output .= html_writer::tag('h3', html_writer::link(
                new moodle_url('/local/codechecker/', array('path' => $prettypath)),
                s($prettypath), array('title' => get_string('recheckfile', 'local_codechecker'))));
        $output .= html_writer::start_tag('ul');

        $output .= $this->problem_list('error', $info['errors'], $prettypath);
        $output .= $this->problem_list('warning', $info['warnings'], $prettypath);

        $output .= html_writer::end_tag('ul');
        $output .= html_writer::end_tag('div');

        return $output;
    }

    public function problem_list($level, $problems, $prettypath) {
        $output = '';
        foreach ($problems as $line => $lineproblems) {
            foreach ($lineproblems as $char => $charproblems) {
                foreach ($charproblems as $problem) {
                    $output .= $this->problem_message(
                            $line, $char, $level, $problem, $prettypath);
                }
            }
        }
        return $output;
    }

    public function problem_message($line, $char, $level, $problem, $prettypath) {
        $sourceclass = str_replace('.', '_', $problem['source']);
        $info = html_writer::tag('div', html_writer::tag('strong', $line) . ': ' .
                s($problem['message']), array('class'=>'info ' . $sourceclass));

        $code = html_writer::tag('pre', str_replace(
                array_keys($this->replaces), array_values($this->replaces),
                s(local_codechecker_get_line_of_code($line, $prettypath))));

        return html_writer::tag('li', $code . $info, array('class' => 'fail ' . $level));
    }
}
