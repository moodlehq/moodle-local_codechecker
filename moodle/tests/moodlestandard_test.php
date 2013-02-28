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
 * This file contains the test cases covering the "moodle" standard.
 *
 * @package    local_codechecker
 * @subpackage phpunit
 * @category   phpunit
 * @copyright  2013 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../tests/local_codechecker_testcase.php');

/**
 * PHP CS moodle standard test cases.
 *
 * Each case covers one sniff. Self-explanatory
 *
 * @todo Complete coverage of all Sniffs.
 */
class moodlestandard_testcase extends local_codechecker_testcase {

    public function test_moodle_comenting_inlinecomment() {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('moodle_Sniffs_Commenting_InlineCommentSniff');
        $this->set_fixture(__DIR__ . '/fixtures/moodle_comenting_inlinecomment.php');

        // Define expected results (errors and warnings). Format, array of:
        //   - line => number of problems,  or
        //   - line => array of contents for message / source problem matching.
        //   - line => string of contents for message / source problem matching (only 1).
        $this->set_errors(array(
            4 => array('3 slashes comments are not allowed'),
            6 => 1,
            8 => 'No space before comment text',
           28 => 1,
           44 => 1));
        $this->set_warnings(array(
            4 => 0,
            6 => array(null, 'Commenting.InlineComment.InvalidEndChar')));

        // Let's do all the hard work!
        $this->verify_cs_results();
    }

    public function test_moodle_controlstructures_controlsignature() {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('moodle_Sniffs_ControlStructures_ControlSignatureSniff');
        $this->set_fixture(__DIR__ . '/fixtures/moodle_controlstructures_controlsignature.php');

        // Define expected results (errors and warnings). Format, array of:
        //   - line => number of problems,  or
        //   - line => array of contents for message / source problem matching.
        //   - line => string of contents for message / source problem matching (only 1).
        $this->set_errors(array(
            3 => 0,
            4 => array('found "if(...) {'),
            5 => 0,
            6 => '@Message: Expected "} else {\n"'));
        $this->set_warnings(array());

        // Let's do all the hard work!
        $this->verify_cs_results();
    }

    public function test_moodle_whitespace_scopeindent() {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('moodle_Sniffs_WhiteSpace_ScopeIndentSniff');
        $this->set_fixture(__DIR__ . '/fixtures/moodle_whitespace_scopeindent.php');

        // Define expected results (errors and warnings). Format, array of:
        //   - line => number of problems,  or
        //   - line => array of contents for message / source problem matching.
        //   - line => string of contents for message / source problem matching (only 1).
        $this->set_errors(array(
            6 => 'indented incorrectly; expected at least 4 spaces, found 2 @Source: moodle.WhiteSpace.ScopeIndent.Incorrect',
            18 => 'indented incorrectly; expected at least 4 spaces, found 2 @Source: moodle.WhiteSpace.ScopeIndent.Incorrect'));
        $this->set_warnings(array());

        // Let's do all the hard work!
        $this->verify_cs_results();
    }

    /**
     * Test external sniff incorporated to moodle standard.
     */
    public function test_phpcompatibility_php_deprecatedfunctions() {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('PHPCompatibility_Sniffs_PHP_DeprecatedFunctionsSniff');
        $this->set_fixture(__DIR__ . '/fixtures/phpcompatibility_php_deprecatedfunctions.php');

        // Define expected results (errors and warnings). Format, array of:
        //   - line => number of problems,  or
        //   - line => array of contents for message / source problem matching.
        //   - line => string of contents for message / source problem matching (only 1).
        $this->set_errors(array());
        $this->set_warnings(array(
            5 => array('function ereg_replace', 'use call_user_func instead', '@Source: phpcompat')));

        // Let's do all the hard work!
        $this->verify_cs_results();
    }
}
