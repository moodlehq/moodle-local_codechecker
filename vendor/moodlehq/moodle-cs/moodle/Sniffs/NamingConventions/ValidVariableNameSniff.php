<?php

// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Checks variable names are all lower-case, no underscores.
 *
 * @copyright  2009 Nicolas Connault
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleHQ\MoodleCS\moodle\Sniffs\NamingConventions;

use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Files\File;

class ValidVariableNameSniff extends AbstractVariableSniff
{
    public static $allowedglobals = [
        // PHP Superglobals.
        '_COOKIE',
        '_ENV',
        '_FILES',
        '_GET',
        '_POST',
        '_REQUEST',
        '_SERVER',
        '_SESSION',

        // PHP Predefined variables.
        'http_response_header',
        '_HTTP_RAW_POST_DATA', // Removed in PHP 7.0.

        // Moodle global objects.
        'ADMIN',
        'CFG',
        'COURSE',
        'DB',
        'OUTPUT',
        'PAGE',
        'PERF',
        'SESSION',
        'SITE',
        'THEME',
        'USER',
        'XMLDB',

        // Moodle global literals.
        'FULLME',
        'FULLSCRIPT',
        'ME',
        'SCRIPT',

        // Moodle private objects.
        'ACCESSLIB_PRIVATE',
        'CONDITIONLIB_PRIVATE', // Removed in Moodle 2.7.
        'FILTERLIB_PRIVATE',
        'MNET_REMOTE_CLIENT',
    ];

    /**
     * Processes class member variables.
     *
     * @param File $phpcsfile The file being scanned.
     * @param int $stackptr The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    protected function processMemberVar(File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->getTokens();
        $membername = ltrim($tokens[$stackptr]['content'], '$');

        if (preg_match('/[A-Z]+/', $membername)) {
            $error = "Member variable \"$membername\" must be all lower-case";
            $phpcsfile->addError($error, $stackptr, 'MemberNameUnderscore');
        }

        // Find underscores in variable names (accepting $_foo for private vars).
        $pos = strpos($membername, '_');
        if ($pos > 1) {
            $error = "Member variable \"$membername\" must not contain underscores.";
            $phpcsfile->addError($error, $stackptr, 'MemberNameUnderscore');
        }

        // Must not be preceded by 'var' keyword.
        $keyword = $phpcsfile->findPrevious(T_VAR, $stackptr);

        if ($tokens[$keyword]['line'] == $tokens[$stackptr]['line']) {
            $error = "The 'var' keyword is not permitted." .
                     'Visibility must be explicitly declared with public, private or protected';
            $phpcsfile->addError($error, $stackptr, 'MemberNameVisibility');
        }
    }

    /**
     * Processes normal variables.
     *
     * @param File $phpcsfile The file where this token was found.
     * @param int $stackptr The position where the token was found.
     *
     * @return void
     */
    protected function processVariable(File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->getTokens();
        $membername     = ltrim($tokens[$stackptr]['content'], '$');
        $this->validateMoodleVariableName($membername, $phpcsfile, $stackptr);
    }

    /**
     * Processes variables in double quoted strings.
     *
     * @param File $phpcsfile The file where this token was found.
     * @param int $stackptr The position where the token was found.
     *
     * @return void
     */
    protected function processVariableInString(File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->getTokens();

        if (
            preg_match_all(
                '/[^\\\\]\$([A-Za-z0-9_]+)(\-\>([A-Za-z0-9_]+))?/i',
                $tokens[$stackptr]['content'],
                $matches
            )
        ) {
            $captured = $matches[1];

            foreach ($captured as $varname) {
                $this->validateMoodleVariableName($varname, $phpcsfile, $stackptr);
            }
        }
    }

    /**
     * Processes normal moodle variables against Moodle coding guidelines. Note this
     * can't be used for member variables as we allow slightly different rules there.
     *
     * @param string $varname The name of the variable.
     * @param File $phpcsfile The file where this token was found.
     * @param int $stackptr The position where the token was found.
     *
     * @return void
     */
    private function validateMoodleVariableName($varname, File $phpcsfile, $stackptr) {
        if (preg_match('/[A-Z]+/', $varname) && !in_array($varname, self::$allowedglobals)) {
            $error = "Variable \"$varname\" must be all lower-case";
            $phpcsfile->addError($error, $stackptr, 'VariableNameLowerCase');
        }

        if (strpos($varname, '_') !== false && !in_array($varname, self::$allowedglobals)) {
            $error = "Variable \"$varname\" must not contain underscores.";
            $phpcsfile->addError($error, $stackptr, 'VariableNameUnderscore');
        }
    }
}
