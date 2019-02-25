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

    public function register() {
        return [T_WHITESPACE];//array(T_COMMENT);//'// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.');
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $file     The file being scanned.
     * @param int                  $stackptr The position of the current token in
     *                                       the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $file, $stackptr) {

        $tokens = $file->getTokens();
        $laststackptr = count($tokens) - 1;

        if ($stackptr == $laststackptr) {
            $lasttoken = $tokens[$laststackptr];
            print_r($lasttoken);
            if ($lasttoken['type'] !== T_WHITESPACE) {
                $error = 'File should have a blank line at the end.';
                $fix = $file->addFixableError($error, $laststackptr, "NoBlankLineAtEof");
                if ($fix === true) {
                    $file->fixer->beginChangeset();
                    $file->fixer->addNewline($stackptr);
                    $file->fixer->endChangeset();
                }
            }
        }

        /*if (strpos($tokens[$stackptr]['content'], self::LICENCE_LAST_LINE) !== false) {
            if ($tokens[$stackptr + 1]['code'] != T_WHITESPACE) {
                $error = 'License should have a blank line after.';
                $file->addError($error, $stackptr, 'NoSpace');
            }
        }*/
    }
}
