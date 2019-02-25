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
 * Verify there is a line after license.
 *
 * @package    local_codechecker
 * @copyright  2018 University of Strathclyde
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class moodle_Sniffs_WhiteSpace_LineAtEndOfFileSniff implements PHP_CodeSniffer_Sniff {

    const ERROR_MSG = 'File should have a blank line at the end.';

    private $noBlankLineEmitted = false;

    public function register() {
        return [T_WHITESPACE];//array(T_COMMENT);//'// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.');
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * This is a bit of a fudge, in that it gets the last token in the file and
     * checks if it's white space and ONLY a new line character.
     *
     * @param PHP_CodeSniffer_File $file     The file being scanned.
     * @param int                  $stackptr The position of the current token in
     *                                       the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $file, $stackptr) {

        $tokens = $file->getTokens();
        $token = $tokens[$stackptr];
        $laststackptr = count($tokens) - 1 ;

        $lasttoken = $tokens[$laststackptr];
        if (!$this->noBlankLineEmitted) {
            $this->test($lasttoken, $file, $laststackptr);
        }
    }

    private function test($token, $file, $stackptr) {

        if ($token['code'] !== T_WHITESPACE) {
            $this->noBlankLineEmitted = true;
            $file->addError(self::ERROR_MSG, $stackptr, "NoBlankLineAtEof");
        } else { // it is whitespace but it's not just a new line character.
            if ($token['content'] !== $file->eolChar) {
                $this->noBlankLineEmitted = true;
                $file->addError(self::ERROR_MSG, $stackptr, "NoBlankLineAtEof");
            }
        }
    }
}
