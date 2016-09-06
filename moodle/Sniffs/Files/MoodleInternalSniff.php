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
 * Checks that each file contains the standard MOODLE_INTERNAL check
 *
 * @package    local_codechecker
 * @copyright  2016 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class moodle_Sniffs_Files_MoodleInternalSniff implements PHP_CodeSniffer_Sniff {
    /**
     * Register for open tag (only process onecess per file).
     */
    public function register() {
        return array(T_OPEN_TAG);
    }

    public function process(PHP_CodeSniffer_File $file, $pointer) {
        // Special dispensation for behat files.
        if (basename(dirname($file->getFilename())) === 'behat') {
            return;
        }

        // Special dispensation for lang files.
        if (basename(dirname(dirname($file->getFilename()))) === 'lang') {
            return;
        }

        // We only want to do this once per file.
        $prevopentag = $file->findPrevious(T_OPEN_TAG, $pointer - 1);
        if ($prevopentag !== false) {
            return;
        }


        // Advance through tokens until we find some real code.
        $tokens = $file->getTokens();
        $relevantcodefound = false;
        $ignoredtokens = array_merge([T_OPEN_TAG, T_SEMICOLON], PHP_CodeSniffer_Tokens::$emptyTokens);

        while ($relevantcodefound == false) {
            // Find some non-whitespace (etc) code.
            $pointer = $file->findNext($ignoredtokens, $pointer, null, true);
            if ($tokens[$pointer]['code'] === T_NAMESPACE) {
                // Namespace definitions are allowed before anything else, jump to end of namspace statement.
                $pointer = $file->findEndOfStatement($pointer + 1);
            } else if ($tokens[$pointer]['code'] === T_STRING && $tokens[$pointer]['content'] == 'define') {
                // Some things like AJAX_SCRIPT NO_MOODLE_COOKIES need to be defined before config inclusion.
                // Jump to end of define().
                $pointer = $file->findEndOfStatement($pointer + 1);
            } else {
                $relevantcodefound = true;
            }
        }

        // OK, we've got to the first bit of releant code.
        if ($tokens[$pointer]['code'] === T_STRING && $tokens[$pointer]['content'] == 'defined') {
            // Its a defined() statement, is a MOODLE_INTERNAL check?
            if ($this->is_moodle_internal_check($file, $tokens, $pointer)) {
                // Yes. This file is good, hurrah!
                return;
            }
        } else if ($tokens[$pointer]['code'] === T_REQUIRE || $tokens[$pointer]['code'] === T_REQUIRE_ONCE) {
            // It's a require() or require_once() statement. Is it require(config.php)?

            $requirecontent = $file->getTokensAsString($pointer, ($file->findEndOfStatement($pointer) - $pointer));
            if (strpos($requirecontent, '/config.php') !== false) {
                // Yes we are requiring config.php. This file is good.
                return;
            }
        }

        // Got here because, so something is not right.
        $file->addWarningOnLine('Expected MOODLE_INTERNAL check or config.php inclusion', $tokens[$pointer]['line']);
    }

    protected function is_moodle_internal_check(PHP_CodeSniffer_File $file, $tokens, $pointer) {
        $ignoredtokens = array_merge(PHP_CodeSniffer_Tokens::$emptyTokens, PHP_CodeSniffer_Tokens::$bracketTokens);

        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_CONSTANT_ENCAPSED_STRING or
            $tokens[$pointer]['content'] !== "'MOODLE_INTERNAL'") {
            return false;
        }

        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);

        if ($tokens[$pointer]['code'] !== T_BOOLEAN_OR && $tokens[$pointer]['code'] !== T_LOGICAL_OR) {
            return false;
        }

        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_EXIT) {
            return false;
        }

        return true;
    }
}


