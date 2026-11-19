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

namespace MoodleHQ\MoodleCS\moodle\Sniffs\Deprecations;

use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Files\File;

/**
 * Detects usage of deprecated $CFG properties.
 *
 * Some $CFG properties (for example, $CFG->httpswwwroot) have been removed from
 * Moodle core but are kept as aliases for backwards compatibility with contributed
 * plugins. This sniff warns whenever such a property is accessed, so that plugin
 * developers can migrate away from them before they are removed for good.
 *
 * @copyright  2026 Paul Holden <paulh@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class DeprecatedCFGPropertiesSniff extends AbstractVariableSniff
{
    /**
     * List of deprecated $CFG properties.
     *
     * Keys are the deprecated property names, values are their suggested
     * replacements (or null when there is no direct replacement).
     *
     * @var array<string, string|null>
     */
    public $deprecatedProperties = [];

    /**
     * Processes class member variables.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the stack.
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr) {
        // Not applicable: class member variables cannot be $CFG.
    }

    /**
     * Processes normal variables.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the stack.
     */
    protected function processVariable(File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();

        if (ltrim($tokens[$stackPtr]['content'], '$') !== 'CFG') {
            return;
        }

        // Look for '->' immediately after (skipping whitespace).
        $next = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
        if ($next === false || $tokens[$next]['code'] !== T_OBJECT_OPERATOR) {
            return;
        }

        // Look for the property name after '->'.
        $propertyPtr = $phpcsFile->findNext(T_WHITESPACE, $next + 1, null, true);
        if ($propertyPtr === false || $tokens[$propertyPtr]['code'] !== T_STRING) {
            return;
        }

        $this->reportIfDeprecated($tokens[$propertyPtr]['content'], $phpcsFile, $propertyPtr);
    }

    /**
     * Processes variables in double quoted strings.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the stack.
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();

        // Match $CFG->property inside double-quoted strings (and heredocs).
        if (
            preg_match_all(
                '/(?<!\\\\)\$CFG->([A-Za-z_][A-Za-z0-9_]*)/',
                $tokens[$stackPtr]['content'],
                $matches
            )
        ) {
            foreach ($matches[1] as $property) {
                $this->reportIfDeprecated($property, $phpcsFile, $stackPtr);
            }
        }
    }

    /**
     * Emit a warning if the given property is on the deprecated list.
     *
     * @param string $property The property name found on $CFG.
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position to attach the warning to.
     */
    protected function reportIfDeprecated($property, File $phpcsFile, $stackPtr) {
        if (!array_key_exists($property, $this->deprecatedProperties)) {
            return;
        }

        $replacement = $this->deprecatedProperties[$property];
        if ($replacement !== null && $replacement !== '') {
            $phpcsFile->addWarning(
                '$CFG->%s is deprecated; use $CFG->%s instead',
                $stackPtr,
                'DeprecatedCFGPropertyWithReplacement',
                [$property, $replacement]
            );
        } else {
            $phpcsFile->addWarning(
                '$CFG->%s is deprecated and should not be used',
                $stackPtr,
                'DeprecatedCFGProperty',
                [$property]
            );
        }
    }
}
