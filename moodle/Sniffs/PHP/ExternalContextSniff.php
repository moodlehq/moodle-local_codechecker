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
 * Sniff for debugging and other functions that we don't want used in finished code.
 *
 * @package    local_codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (class_exists('Generic_Sniffs_PHP_ForbiddenFunctionsSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception(
            'Class Generic_Sniffs_PHP_ForbiddenFunctionsSniff not found');
}

class moodle_Sniffs_PHP_ExternalContextSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff {

    /**
     * Constructor.
     * */
    public function __construct() {
        $this->error = false;
        $this->forbiddenFunctions = array(
            'require_login'=> 'external_api::validate_context',
            'set_context' => 'external_api::validate_context', // Does not work yet..
        );
    }

    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
        // Only run this check when in external api.
        // TODO: this seems extremely inefficient to do on every token, better way
        // required.
        $classptr = $phpcsFile->findPrevious(array(T_CLASS), $stackPtr);
        $extendedclass = $phpcsFile->findExtendedClassName($classptr);

        if ($extendedclass == 'external_api') {
            parent::process($phpcsFile, $stackPtr);
        }
    }
}
