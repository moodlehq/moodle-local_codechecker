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
        if ($this->code_changes_global_state($file, $pointer, ($file->numTokens - 1))) {
            $file->addError('Expected MOODLE_INTERNAL check or config.php inclusion. Change in global state detected.', $pointer);
        } else {
            // Only if there are more than one artifact (class, interface, trait), we show the warning.
            // (files with only one, are allowed to be MOODLE_INTERNAL free - MDLSITE-5967).
            if ($this->count_artifacts($file) > 1) {
                $file->addWarning('Expected MOODLE_INTERNAL check or config.php inclusion. Multiple artifacts detected.', $pointer);
            }
        }
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
            } else if ($tokens[$pointer]['code'] === T_DECLARE && $tokens[$pointer]['content'] == 'declare') {
                // Declare statements must be at start of file.
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

    /**
     * Counts how many classes, interfaces or traits a file has.
     *
     * @param PHP_CodeSniffer_File $file The file being scanned.
     *
     * @return int the number of classes, interfaces and traits in the file.
     */
    private function count_artifacts(PHP_CodeSniffer_File $file) {
        $position = 0;
        $counter = 0;
        while ($position !== false) {
            if ($position = $file->findNext([T_CLASS, T_INTERFACE, T_TRAIT], ($position + 1))) {
                $counter++;
            }

        }
        return $counter;
    }

    /**
     * Searches for changes in 'global state' rather than just symbol definitions in the code.
     *
     * Heavily inspired by PSR1.Files.SideEffects:
     * https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/PSR1/Sniffs/Files/SideEffectsSniff.php
     *
     * @param PHP_CodeSniffer_File $file The file being scanned.
     * @param int                  $start     The token to start searching from.
     * @param int                  $end       The token to search to.
     * @param array                $tokens    The stack of tokens that make up
     *                                        the file.
     * @return true if side effect is detected in the code.
     */
    private function code_changes_global_state(PHP_CodeSniffer_File $file, $start, $end) {
        $tokens = $file->getTokens();
        $symbols = [T_CLASS => T_CLASS, T_INTERFACE => T_INTERFACE, T_TRAIT => T_TRAIT, T_FUNCTION => T_FUNCTION];
        $conditions = [T_IF => T_IF, T_ELSE   => T_ELSE, T_ELSEIF => T_ELSEIF];

        for ($i = $start; $i <= $end; $i++) {
            // Ignore whitespace and comments.
            if (isset(PHP_CodeSniffer_Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                continue;
            }

            // Ignore function/class prefixes.
            if (isset(PHP_CodeSniffer_Tokens::$methodPrefixes[$tokens[$i]['code']]) === true) {
                continue;
            }

            // Ignore anon classes.
            if ($tokens[$i]['code'] === T_ANON_CLASS) {
                $i = $tokens[$i]['scope_closer'];
                continue;
            }

            switch ($tokens[$i]['code']) {
                case T_NAMESPACE:
                case T_USE:
                case T_DECLARE:
                case T_CONST:
                    // Ignore entire namespace, declare, const and use statements.
                    if (isset($tokens[$i]['scope_opener']) === true) {
                        $i = $tokens[$i]['scope_closer'];
                    } else {
                        $semicolon = $file->findNext(T_SEMICOLON, ($i + 1));
                        if ($semicolon !== false) {
                            $i = $semicolon;
                        }
                    }
                    continue 2;
            }

            // Detect and skip over symbols.
            if (isset($symbols[$tokens[$i]['code']]) === true && isset($tokens[$i]['scope_closer']) === true) {
                $i = $tokens[$i]['scope_closer'];
                continue;
            } else if ($tokens[$i]['code'] === T_STRING && strtolower($tokens[$i]['content']) === 'define') {
                $prev = $file->findPrevious(T_WHITESPACE, ($i - 1), null, true);
                if ($tokens[$prev]['code'] !== T_OBJECT_OPERATOR) {

                    $semicolon = $file->findNext(T_SEMICOLON, ($i + 1));
                    if ($semicolon !== false) {
                        $i = $semicolon;
                    }

                    continue;
                }
            }

            // Conditional statements are allowed in symbol files as long as the
            // contents is only a symbol definition. So don't count these as effects
            // in this case.
            if (isset($conditions[$tokens[$i]['code']]) === true) {
                if (isset($tokens[$i]['scope_opener']) === false) {
                    // Probably an "else if", so just ignore.
                    continue;
                }

                if ($this->code_changes_global_state($file, ($tokens[$i]['scope_opener'] + 1), ($tokens[$i]['scope_closer'] - 1))) {
                    return true;
                }

                $i = $tokens[$i]['scope_closer'];
                continue;
            }

            // If we got here, there is a token which change state..
            return true;
        }

        // If we got here, we got out of the loop.
        return false;
    }
}


