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
use PHPCSUtils\Utils\Context;
use PHPCSUtils\Utils\Namespaces;

/**
 * Utilities related to PHP Attributes.
 *
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class Attributes
{
    /**
     * Get the pointer for an Attribute on an Attributable object.
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return array
     */
    public static function getAttributePointers(
        File $phpcsFile,
        int $stackPtr
    ): array {
        $tokens = $phpcsFile->getTokens();
        $attributes = [];

        $stopAt = [
            T_DOC_COMMENT_CLOSE_TAG,
            T_CLOSE_CURLY_BRACKET,
            T_OPEN_CURLY_BRACKET,
            T_SEMICOLON,
        ];

        for ($attributePtr = $stackPtr; $attributePtr > 0; $attributePtr--) {
            $token = $tokens[$attributePtr];
            // The phpcs parser places an attribute_opener and attribute_closer on every part of an attribute.
            if (isset($token['attribute_opener'])) {
                $attributePtr = $token['attribute_opener'];
                $attributes[] = $attributePtr;
            }

            if (in_array($token['code'], $stopAt)) {
                break;
            }
        }

        return $attributes;
    }

    /**
     * Get the properties of an Attribute.
     *
     * Note: The attribute name is not currently qualified relative to the current namespace or any imported classes.
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return array|null
     */
    public static function getAttributeProperties(
        File $phpcsFile,
        int $stackPtr
    ): ?array {
        $tokens = $phpcsFile->getTokens();
        if (!isset($tokens[$stackPtr]['attribute_opener'])) {
            return null;
        }

        $opener = $tokens[$stackPtr]['attribute_opener'];
        $closer = $tokens[$stackPtr]['attribute_closer'];

        $properties = [
            'attribute_opener' => $opener,
            'attribute_closer' => $closer,
            'attribute_name' => null,
        ];

        $stopAt = [
            T_OPEN_PARENTHESIS,
        ];

        for ($i = $opener + 1; $i < $closer; $i++) {
            if (in_array($tokens[$i]['code'], $stopAt)) {
                break;
            }
            $properties['attribute_name'] .= $tokens[$i]['content'];
        }

        // TODO Get the qualified name.

        return $properties;
    }
}
