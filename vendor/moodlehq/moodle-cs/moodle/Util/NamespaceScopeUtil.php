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

namespace MoodleHQ\MoodleCS\moodle\Util;

use PHP_CodeSniffer\Files\File;
use PHPCSUtils\Exceptions\ValueError;
use PHPCSUtils\Internal\Cache;
use PHPCSUtils\Utils\Namespaces;
use PHPCSUtils\Utils\UseStatements;

/**
 * Utilities related to Classes and Namespacing.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class NamespaceScopeUtil
{
    protected static array $builtintypes = [
        'int',
        'float',
        'string',
        'bool',
        'array',
        'object',
        'callable',
        'iterable',
    ];

    /**
     * Get the qualified name relative to the current namespace, considering any class aliases.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being processed.
     * @param int $stackPtr The stack pointer of the token to qualify.
     * @param string $classname The classname to qualify.
     * @return string The fully qualified classname.
     */
    public static function getQualifiedName(
        File $phpcsFile,
        int $stackPtr,
        string $classname
    ): string {
        if (substr($classname, 0, 1) === '\\') {
            // If the classname starts with a backslash, it is already fully qualified.
            return substr($classname, 1);
        }

        if (in_array($classname, self::$builtintypes, true)) {
            // If the classname is a scalar type, we can return it as is.
            return $classname;
        }

        // Check if the classname is imported in the current file.
        $imports = self::getClassImports($phpcsFile, $stackPtr);
        if (isset($imports[$classname])) {
            // If the classname is imported, we can return the original.
            return $imports[$classname];
        }

        // If the classname is not imported, we need to qualify it relative to the current namespace or use statement.
        $namespace = Namespaces::determineNamespace($phpcsFile, $stackPtr);
        if ($namespace !== '') {
            return "{$namespace}\\$classname";
        }

        // If there is no namespace, we can return the classname as is.
        return $classname;
    }

    /**
     * Get the class imports for a given stack pointer.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile
     * @param int $stackPtr The stack pointer to search to
     * @return array<string, string>
     */
    public static function getClassImports(
        File $phpcsFile,
        int $stackPtr
    ): array {
        if (Cache::isCached($phpcsFile, __METHOD__, $stackPtr) === true) {
            return Cache::get($phpcsFile, __METHOD__, $stackPtr); // @codeCoverageIgnore
        }

        // Check for any class imports.
        $nextToken = $phpcsFile->findNext(
            \T_USE,
            0,
            $stackPtr
        );

        $imports = [];
        if ($nextToken !== false) {
            do {
                try {
                    $imports = UseStatements::splitAndMergeImportUseStatement($phpcsFile, $nextToken, $imports);
                    // @codeCoverageIgnoreStart
                } catch (ValueError $e) {
                    // Not an import use statement. Bow out.
                    continue;
                }
                // @codeCoverageIgnoreEnd

                $nextToken = $phpcsFile->findNext(
                    \T_USE,
                    $nextToken + 1,
                    $stackPtr
                );
            } while ($nextToken !== false);
        }

        $classnameImports = $imports['name'] ?? [];
        Cache::set($phpcsFile, __METHOD__, $stackPtr, $classnameImports);

        return $classnameImports;
    }
}
