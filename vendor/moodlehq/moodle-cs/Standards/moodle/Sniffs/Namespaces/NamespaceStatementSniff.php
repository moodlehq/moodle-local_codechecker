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
 * Checks that each file contains the standard GPL comment.
 *
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleHQ\MoodleCS\moodle\Sniffs\Namespaces;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class NamespaceStatementSniff implements Sniff
{
    public function register()
    {
        return [
            T_NAMESPACE,
        ];
    }

    public function process(File $file, $stackPtr)
    {
        $tokens = $file->getTokens();

        $checkPtr = $stackPtr + 2;
        $token = $tokens[$checkPtr];

        if ($token['code'] === T_NAME_FULLY_QUALIFIED) {
            $fqdn = $token['content'];
        } elseif ($token['code'] === T_NS_SEPARATOR) {
            $fqdn = '';
            for ($ptr = $checkPtr; isset($tokens[$ptr]); $ptr++) {
                if ($tokens[$ptr]['code'] !== T_NS_SEPARATOR && $tokens[$ptr]['code'] !== T_STRING) {
                    break;
                }
                $fqdn .= $tokens[$ptr]['content'];
            }
        } else {
            return;
        }

        if ($fqdn !== '' && $fqdn[0] === '\\') {
            $fix = $file->addFixableError(
                'Namespace should not start with a slash: %s',
                $checkPtr,
                'LeadingSlash',
                [$fqdn]
            );

            if ($fix) {
                if ($token['code'] === T_NAME_FULLY_QUALIFIED) {
                    $file->fixer->replaceToken($checkPtr, ltrim($fqdn, '\\'));
                } else {
                    $file->fixer->replaceToken($checkPtr, '');
                }
            }
        }
    }
}
