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
use PHPCSUtils\Utils\ObjectDeclarations;

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
            'qualified_name' => null,
            'parenthesis_opener' => null,
            'parenthesis_closer' => null,
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

        if ($properties['attribute_name'] !== null) {
            $properties['qualified_name'] = NamespaceScopeUtil::getQualifiedName(
                $phpcsFile,
                $stackPtr,
                $properties['attribute_name']
            );
        }

        // Find the parenthesis if they exist.
        $openParen = $phpcsFile->findNext(
            T_OPEN_PARENTHESIS,
            $opener + 1,
            $closer
        );

        if ($openParen !== false) {
            $properties['parenthesis_opener'] = $openParen;
            $properties['parenthesis_closer'] = $tokens[$openParen]['parenthesis_closer'];
        }

        return $properties;
    }

    /**
     * Check if an Attribute exists on an Attributable object.
     *
     * @param File $file
     * @param int $pointer
     * @param string $attributeName The name of the attribute to check for.
     * @return bool True if the attribute exists, false otherwise.
     */
    public static function hasAttribute(
        File $file,
        int $pointer,
        string $attributeName
    ) {
        $attributes = self::getAttributePointers($file, $pointer);
        foreach ($attributes as $attributePtr) {
            $attribute = self::getAttributeProperties($file, $attributePtr);
            if ($attribute['qualified_name'] === $attributeName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the properties of an Attribute from a pointer.
     *
     * @param File $file
     * @param int $pointer The pointer to the attribute.
     * @param array $attributeNameFilter An optional filter for attribute names.
     * @return array An array of attributes with their properties.
     */
    public static function getAttributePropertiesFromPointer(
        File $file,
        int $pointer,
        array $attributeNameFilter = []
    ): array {
        $attributesWithProperties = [];

        $attributes = Attributes::getAttributePointers($file, $pointer);
        foreach ($attributes as $attributePtr) {
            $attribute = Attributes::getAttributeProperties($file, $attributePtr);
            if ($attribute === null) {
                continue; // @codeCoverageIgnore
            }

            if (count($attributeNameFilter) > 0) {
                // If the attribute name is not in the filter, skip it.
                if (!in_array($attribute['qualified_name'], $attributeNameFilter, true)) {
                    continue;
                }
            }

            // If the attribute is already in the array, skip it.
            $attributesWithProperties[$attributePtr] = $attribute;
        }
        return $attributesWithProperties;
    }

    /**
     * Check if a function has an \Override Attribute.
     *
     * Note: Override attributes can only be valid on methods of classes which extend or implement another class.
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return bool
     */
    public static function hasOverrideAttribute(
        File $phpcsFile,
        int $stackPtr
    ): bool {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];
        if ($token['code'] !== T_FUNCTION) {
            // Not a function so can't have an Override Attribute.
            return false;
        }

        if (empty($token['conditions'])) {
            // Not in a class or interface.
            return false;
        }

        $extendsOrImplements = false;
        foreach ($token['conditions'] as $condition => $conditionCode) {
            $extendsOrImplements = $extendsOrImplements || ObjectDeclarations::findExtendedClassName(
                $phpcsFile,
                $condition
            );
            $extendsOrImplements = $extendsOrImplements || ObjectDeclarations::findImplementedInterfaceNames(
                $phpcsFile,
                $condition
            );
            $extendsOrImplements = $extendsOrImplements || ObjectDeclarations::findExtendedInterfaceNames(
                $phpcsFile,
                $condition
            );

            if ($extendsOrImplements) {
                break;
            }
        }

        if (!$extendsOrImplements) {
            // The OVerride attrinbute can only apply to a class which has a parent.
            return false;
        }

        $attributes = self::getAttributePointers($phpcsFile, $stackPtr);
        foreach ($attributes as $attributePtr) {
            $attribute = self::getAttributeProperties($phpcsFile, $attributePtr);
            if ($attribute['attribute_name'] === '\Override') {
                return true;
            }
        }

        return false;
    }
}
