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
 * Test the IncludingFile sniff.
 *
 * @package    local_codechecker
 * @category   test
 * @copyright  2021 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\PHP\MemberVarScopeSniff
 */
class PHPMemberVarScopeTest extends MoodleCSBaseTestCase {

    public function test_php_membervarscope() {
        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.PHP.MemberVarScope');
        $this->set_fixture(__DIR__ . '/fixtures/php/membervarscope.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->set_errors([
            8 => '@Message: Scope modifier not specified for member variable "$missingprop"',
            9 => '@Source: moodle.PHP.MemberVarScope.Missing',
        ]);
        $this->set_warnings([]);

        // Let's do all the hard work!
        $this->verify_cs_results();
    }
}
