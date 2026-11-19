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

namespace MoodleHQ\MoodleCS\moodle\Sniffs\PHPUnit;

use MoodleHQ\MoodleCS\moodle\Util\Attributes;
use MoodleHQ\MoodleCS\moodle\Util\MoodleUtil;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHPCSUtils\Utils\ObjectDeclarations;

/**
 * Checks that a test file has the @coversxxx annotations properly defined.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class AbstractTestCaseSniff implements Sniff
{
    /**
     * Check if the file is a test file that the sniff should check.
     *
     * @param \PHP_CodeSniffer\Files\File $file
     * @return bool
     */
    protected function shouldCheckFile(File $file) {
        // Before starting any check, let's look for various things.

        // If we aren't checking Moodle 4.0dev (400) and up, nothing to check.
        // Make and exception for codechecker phpunit tests, so they are run always.
        if (!MoodleUtil::meetsMinimumMoodleVersion($file, 400) && !MoodleUtil::isUnitTestRunning()) {
            return false; // @codeCoverageIgnore
        }

        // If the file is not a unit test file, nothing to check.
        if (!MoodleUtil::isUnitTest($file) && !MoodleUtil::isUnitTestRunning()) {
            return false; // @codeCoverageIgnore
        }

        return true;
    }

    /**
     * Check if the sniff should check attributes on test cases.
     *
     * @param \PHP_CodeSniffer\Files\File $file
     * @return bool
     */
    protected function shouldCheckTestCaseAttributes(File $file): bool {
        return MoodleUtil::meetsMinimumMoodleVersion($file, 500) !== false;
    }

    /**
     * Get the test cases in a file.
     *
     * @param File $file
     * @param int $cStart
     * @return array
     */
    protected function getTestCasesInFile(
        File $file,
        int $cStart = 0
    ) {
        $tokens = $file->getTokens();
        $testClasses = [];
        while ($cStart = $file->findNext(T_CLASS, $cStart + 1)) {
            $className = $file->getDeclarationName($cStart);

            // Only if the class is extending something.
            // TODO: We could add a list of valid classes once we have a class-map available.
            if (!$file->findNext(T_EXTENDS, $cStart + 1, $tokens[$cStart]['scope_opener'])) {
                continue;
            }

            // Ignore any classname which does not end in "_test" or "_testcase".
            if (substr($className, -5) !== '_test' && substr($className, -9) !== '_testcase') {
                continue;
            }

            $testClasses[$cStart] = $className;
        }

        return $testClasses;
    }

    /**
     * Get the test methods in a class.
     *
     * @param File $file
     * @param int $classPointer
     * @return array
     */
    protected function getTestMethodsInClass(
        File $file,
        int $classPointer
    ) {
        // Iterate over all the methods in the class.
        $methodPointers = ObjectDeclarations::getDeclaredMethods($file, $classPointer);

        $testMethods = [];
        foreach ($methodPointers as $methodName => $methodPointer) {
            // The method must either:
            // 1. Start with 'test_'.
            // 2. Have a #[\PHPUnit\Framework\Attributes\Test] attribute

            if (strpos($methodName, 'test_') === 0) {
                $testMethods[$methodName] = $methodPointer;
                unset($methodPointers[$methodName]);
                continue;
            }
        }

        if ($this->shouldCheckTestCaseAttributes($file)) {
            $attributeName = \PHPUnit\Framework\Attributes\Test::class;
            foreach ($methodPointers as $methodName => $methodPointer) {
                if (Attributes::hasAttribute($file, $methodPointer, $attributeName)) {
                    $testMethods[$methodName] = $methodPointer;
                }
            }
        }

        return $testMethods;
    }
}
