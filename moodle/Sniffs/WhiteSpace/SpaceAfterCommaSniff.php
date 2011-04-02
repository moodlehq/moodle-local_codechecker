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
 * Verify there is a single space after a comma.
 *
 * @package    local
 * @subpackage codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Verify there is a single space after a comma.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_Sniffs_WhiteSpace_SpaceAfterCommaSniff implements PHP_CodeSniffer_Sniff {
    public function register() {
        return array(T_COMMA);
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

        if ($tokens[$stackptr + 1]['code'] === T_WHITESPACE) {
            $content       = $tokens[($stackptr + 1)]['content'];
            $contentlength = strlen($content);
            if ($contentlength !== 1) {
                $error = 'Commas (,) must be followed by a single space; ' .
                        'expected 1 space but found %s';
                $data  = array($contentlength);
                $file->addError($error, $stackptr, 'ExtraSpace', $data);
            }

        } else {
            $error = 'Commas (,) must be followed by a single space; ' .
                    'expected 1 space but found none';
            $file->addError($error, $stackptr, 'NoSpace');
        }
    }
}
