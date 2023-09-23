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

namespace MoodleHQ\MoodleCS\moodle\Tests;

// phpcs:disable moodle.NamingConventions

/**
 * Test the WhiteSpaceInStrings sniff.
 *
 * @package    local_codechecker
 * @category   test
 * @copyright  2021 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\WhiteSpace\WhiteSpaceInStringsSniff
 */
class WhiteSpaceWhiteSpaceInStringsTest extends MoodleCSBaseTestCase {

    public function test_whitespace_whitespaceinstrings() {
        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.WhiteSpace.WhiteSpaceInStrings');
        $this->set_fixture(__DIR__ . '/fixtures/whitespace/whitespaceinstrings.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->set_errors([
            5 => '@Message: Whitespace found at end of line within string',
            7 => '@Message: Tab found within whitespace',
        ]);
        $this->set_warnings([]);

        // Let's do all the hard work!
        $this->verify_cs_results();
    }
}
