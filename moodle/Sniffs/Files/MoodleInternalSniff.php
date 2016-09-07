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
 * Checks that each file contains the standard MOODLE_INTERNAL check or
 * a config.php inclusion.
 *
 * @package    local_codechecker
 * @copyright  2016 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class moodle_Sniffs_Files_MoodleInternalSniff implements PHP_CodeSniffer_Sniff {
    /**
     * Register for open tag (only process once per file).
     */
    public function register() {
        return array(T_OPEN_TAG);
    }

    /**
     * Processes php files and looks for MOODLE_INTERNAL or config.php
     * inclusion.
     *
     * @param PHP_CodeSniffer_File $file The file being scanned.
     * @param int $pointer The position in the stack.
     */
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

        // Find where real code is and check from there.
        $pointer = $this->get_position_of_relevant_code($file, $pointer);

        // OK, we've got to the first bit of relevant code.
        if ($this->is_moodle_internal_or_die_check($file, $pointer)) {
            // There is a MOODLE_INTERNAL check. This file is good, hurrah!
            return;
        }
        if ($this->is_config_php_incluson($file, $pointer)) {
            // We are requiring config.php. This file is good, hurrah!
            return;
        }

        if ($this->is_if_not_moodle_internal_die_check($file, $pointer)) {
            // It's an old-skool MOODLE_INTERNAL check. This file is good, hurrah!
            return;
        }

        // Got here because, so something is not right.
        $file->addWarning('Expected MOODLE_INTERNAL check or config.php inclusion', $pointer);
    }

    /**
     * Finds the position of the first bit of relevant code (ignoring namespaces
     * and define statements).
     *
     * @param PHP_CodeSniffer_File $file The file being scanned.
     * @param int $pointer The position in the stack.
     * @return int position in stack of relevant code.
     */
    protected function get_position_of_relevant_code(PHP_CodeSniffer_File $file, $pointer) {
        // Advance through tokens until we find some real code.
        $tokens = $file->getTokens();
        $relevantcodefound = false;
        $ignoredtokens = array_merge([T_OPEN_TAG, T_SEMICOLON], PHP_CodeSniffer_Tokens::$emptyTokens);

        do {
            // Find some non-whitespace (etc) code.
            $pointer = $file->findNext($ignoredtokens, $pointer, null, true);
            if ($tokens[$pointer]['code'] === T_NAMESPACE || $tokens[$pointer]['code'] === T_USE) {
                // Namespace definitions are allowed before anything else, jump to end of namspace statement.
                $pointer = $file->findEndOfStatement($pointer + 1);
            } else if ($tokens[$pointer]['code'] === T_STRING && $tokens[$pointer]['content'] == 'define') {
                // Some things like AJAX_SCRIPT NO_MOODLE_COOKIES need to be defined before config inclusion.
                // Jump to end of define().
                $pointer = $file->findEndOfStatement($pointer + 1);
            } else {
                $relevantcodefound = true;
            }
        } while (!$relevantcodefound);

        return $pointer;
    }

    /**
     * Is the code in the passes position a moodle internal check?
     * Looks for code like:
     *   defined('MOODLE_INTERNAL') or die()
     *
     * @param PHP_CodeSniffer_File $file The file being scanned.
     * @param int $pointer The position in the stack.
     * @return bool true if is a moodle internal statement
     */
    protected function is_moodle_internal_or_die_check(PHP_CodeSniffer_File $file, $pointer) {
        $tokens = $file->getTokens();
        if ($tokens[$pointer]['code'] !== T_STRING or $tokens[$pointer]['content'] !== 'defined') {
            return false;
        }

        $ignoredtokens = array_merge(PHP_CodeSniffer_Tokens::$emptyTokens, PHP_CodeSniffer_Tokens::$bracketTokens);

        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_CONSTANT_ENCAPSED_STRING or
            $tokens[$pointer]['content'] !== "'MOODLE_INTERNAL'") {
            return false;
        }

        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);

        if ($tokens[$pointer]['code'] !== T_BOOLEAN_OR and $tokens[$pointer]['code'] !== T_LOGICAL_OR) {
            return false;
        }

        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_EXIT) {
            return false;
        }

        return true;
    }

    /**
     * Is the code in the passes position a require(config.php) statement?
     *
     * @param PHP_CodeSniffer_File $file The file being scanned.
     * @param int $pointer The position in the stack.
     * @return bool true if is a config.php inclusion.
     */
    protected function is_config_php_incluson(PHP_CodeSniffer_File $file, $pointer) {
        $tokens = $file->getTokens();

        if ($tokens[$pointer]['code'] !== T_REQUIRE and $tokens[$pointer]['code'] !== T_REQUIRE_ONCE) {
            return false;
        }

        // It's a require() or require_once() statement. Is it require(config.php)?
        $requirecontent = $file->getTokensAsString($pointer, ($file->findEndOfStatement($pointer) - $pointer));
        if (strpos($requirecontent, '/config.php') === false) {
            return false;
        }

        return true;
    }

    /**
     * Is the code in the passed position an old skool MOODLE_INTERNAL check?
     * Looks for code like:
     *    if (!defined('MOODLE_INTERNAL')) {
     *       die('Direct access to this script is forbidden.');
     *    }
     *
     * @param PHP_CodeSniffer_File $file The file being scanned.
     * @param int $pointer The position in the stack.
     * @return bool true if is a moodle internal statement
     */
    protected function is_if_not_moodle_internal_die_check(PHP_CodeSniffer_File $file, $pointer) {
        $tokens = $file->getTokens();

        // Detect 'if'.
        if ($tokens[$pointer]['code'] !== T_IF ) {
            return false;
        }

        $ignoredtokens = array_merge(PHP_CodeSniffer_Tokens::$emptyTokens, PHP_CodeSniffer_Tokens::$bracketTokens);

        // Detect '!'.
        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_BOOLEAN_NOT) {
            return false;
        }

        // Detect 'defined'.
        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_STRING or $tokens[$pointer]['content'] !== 'defined') {
            return false;
        }

        // Detect 'MOODLE_INTERNAL'.
        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_CONSTANT_ENCAPSED_STRING or
            $tokens[$pointer]['content'] !== "'MOODLE_INTERNAL'") {
            return false;
        }

        // Detect die.
        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_EXIT) {
            return false;
        }

        return true;
    }
}


