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
use MoodleHQ\MoodleCS\moodle\Util\NamespaceScopeUtil;
use MoodleHQ\MoodleCS\moodle\Util\MoodleUtil;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Utils\FunctionDeclarations;

/**
 * Checks that a test file has the @coversxxx annotations properly defined.
 *
 * @copyright  2022 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class TestCaseProviderSniff extends AbstractTestCaseSniff
{
    /**
     * Whether to autofix static providers.
     *
     * @var bool
     */
    public $autofixStaticProviders = false;

    private static $internalProvidersAttributes = [
        \PHPUnit\Framework\Attributes\DataProvider::class,
    ];

    private static $externalProvidersAttributes = [
        \PHPUnit\Framework\Attributes\DataProviderExternal::class,
    ];

    /**
     * Register for open tag (only process once per file).
     */
    public function register(): array
    {
        return [
            T_OPEN_TAG,
        ];
    }

    /**
     * Processes php files and perform various checks with file.
     *
     * @param File $file The file being scanned.
     * @param int $pointer The position in the stack.
     */
    public function process(File $file, $pointer): void
    {
        if (!$this->shouldCheckFile($file)) {
            // Nothing to check.
            return; // @codeCoverageIgnore
        }

        $supportsAttributes = $this->shouldCheckTestCaseAttributes($file);

        // We have all we need from core, let's start processing the file.

        // Get the file tokens, for ease of use.
        $tokens = $file->getTokens();

        // In various places we are going to ignore class/method prefixes (private, abstract...)
        // and whitespace, create an array for all them.
        $skipTokens = Tokens::$methodPrefixes + [T_WHITESPACE => T_WHITESPACE];

        foreach ($this->getTestCasesInFile($file, $pointer) as $classPointer => $className) {
            foreach ($this->getTestMethodsInClass($file, $classPointer) as $methodName => $mStart) {
                // First check for Provider attributes.
                $this->checkProviderAttributes($file, $mStart, $supportsAttributes);

                // Let's see if the method has any phpdoc block (first non skip token must be end of phpdoc comment).
                $docPointer = $file->findPrevious($skipTokens, $mStart - 1, null, true);

                // Found a phpdoc block, let's look for @dataProvider tag.
                if ($tokens[$docPointer]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
                    $docStart = $tokens[$docPointer]['comment_opener'];
                    while ($docPointer) { // Let's look upwards, until the beginning of the phpdoc block.
                        $docPointer = $file->findPrevious(T_DOC_COMMENT_TAG, $docPointer - 1, $docStart);
                        if ($docPointer) {
                            $docTag = trim($tokens[$docPointer]['content']);
                            $docTagLC = strtolower($docTag);
                            switch ($docTagLC) {
                                case '@dataprovider':
                                    // Validate basic syntax (FQCN or ::).
                                    $this->checkDataProviderDocBlock($file, $docPointer);
                                    break;
                            }
                        }
                    }
                }

                // Advance until the end of the method, if possible, to find the next one quicker.
                $mStart = $tokens[$mStart]['scope_closer'] ?? $mStart + 1;
            }
        }
    }

    /**
     * Perform a basic syntax cheking of the values of the @dataProvider tag.
     *
     * @param File $file The file being scanned
     * @param int $pointer pointer to the token that contains the tag. Calculations are based on that.
     * @return void
     */
    protected function checkDataProviderDocBlock(
        File $file,
        int $pointer
    ) {
        // Get the file tokens, for ease of use.
        $tokens = $file->getTokens();
        $tag = $tokens[$pointer]['content'];
        $methodName = $tokens[$pointer + 2]['content'];
        $testPointer = $file->findNext(T_FUNCTION, $pointer + 2);
        $testName = FunctionDeclarations::getName($file, $testPointer);

        if ($tag !== '@dataProvider') {
            $fix = $file->addFixableError(
                'Wrong @dataProvider tag: %s provided, @dataProvider expected',
                $pointer,
                'dataProviderNaming',
                [$tag]
            );

            if ($fix) {
                $file->fixer->beginChangeset();
                $file->fixer->replaceToken($pointer, '@dataProvider');
                $file->fixer->endChangeset();
            }
        }

        if ($tokens[$pointer + 2]['code'] !== T_DOC_COMMENT_STRING) {
            $file->addError(
                'Wrong @dataProvider tag specified for test %s, it must be followed by a space and a method name.',
                $pointer,
                'dataProviderSyntaxMethodnameMissing',
                [
                    $testName,
                ]
            );

            // The remaining checks all relate to the method name, so we can't continue.
            return;
        }

        // Check that the method name is valid.
        $this->checkDataProvider(
            $file,
            $pointer + 2,
            $methodName
        );
    }

    /**
     * Check for Data Provider in attributes.
     */
    protected function checkProviderAttributes(
        File $file,
        int $mStart,
        bool $supportsAttributes
    ) {
        // First check for Provider attributes.
        if ($supportsAttributes) {
            $attributes = Attributes::getAttributePropertiesFromPointer(
                $file,
                $mStart,
                array_merge(self::$internalProvidersAttributes, self::$externalProvidersAttributes)
            );
            foreach ($attributes as $attributePointer => $attributeProps) {
                $this->checkProviderAttribute(
                    $file,
                    $attributePointer,
                    $attributeProps
                );
            }
        }
    }

    /**
     * Check the data provider attribute.
     *
     * @param File $file The file being scanned.
     * @param int $attributePointer The pointer to the attribute.
     * @param array $attributeProps The properties of the attribute.
     */
    protected function checkProviderAttribute(
        File $file,
        int $attributePointer,
        array $attributeProps
    ) {
        // Check if the attribute is a data provider.
        if (in_array($attributeProps['qualified_name'], self::$externalProvidersAttributes, true)) {
            // At this time we cannot verify external data providers.
            // Skip the rest of the checks.
            return;
        }

        if (!in_array($attributeProps['qualified_name'], self::$internalProvidersAttributes, true)) {
            // @codeCoverageIgnoreStart
            $file->addError(
                'Invalid data provider attribute %s, it must be one of %s.',
                $attributePointer,
                'dataProviderInvalid',
                [
                    $attributeProps['attribute_name'],
                    implode(
                        ', ',
                        self::$internalProvidersAttributes + self::$externalProvidersAttributes,
                    ),
                ]
            );

            return;
            // @codeCoverageIgnoreEnd
        }

        if ($attributeProps['parenthesis_opener'] === null || $attributeProps['parenthesis_closer'] === null) {
            // If the attribute has no parenthesis, it's invalid.
            $file->addError(
                'Invalid data provider attribute %s, it must have an opening and a closing parenthesis.',
                $attributePointer,
                'dataProviderInvalid',
                [$attributeProps['attribute_name']]
            );
            return;
        }
        $commaPtr = $file->findNext(
            T_COMMA,
            $attributeProps['parenthesis_opener'] + 1,
            $attributeProps['parenthesis_closer']
        );
        if ($commaPtr !== false) {
            // If there is a comma, it is invalid.
            $file->addError(
                'Invalid data provider attribute %s, it must not have any parameters.',
                $attributePointer,
                'dataProviderInvalid',
                [$attributeProps['attribute_name']]
            );

            return;
        }

        $tokens = $file->getTokens();

        // Find the name up to the closing parenthesis.
        $methodName = '';
        $i = $attributeProps['parenthesis_opener'] + 1;
        do {
            $methodName .= $tokens[$i]['content'];
            $i++;
        } while ($i < $attributeProps['parenthesis_closer']);

        $methodName = str_replace(
            ['"', "'"],
            '',
            trim($methodName)
        );

        $this->checkDataProvider(
            $file,
            $attributePointer,
            $methodName
        );

        return;
    }

    /**
     * Check the data provider method.
     *
     * @param File $file The file being scanned.
     * @param int $methodNamePointer The pointer to the method name.
     * @param string $methodName The name of the method.
     */
    protected function checkDataProvider(
        File $file,
        int $methodNamePointer,
        string $methodName
    ) {
        // Check that the method name is valid.
        // It must _not_ start with `test_`.
        if (substr($methodName, 0, 5) === 'test_') {
            $file->addError(
                'Data provider must not start with "test_". "%s" provided.',
                $methodNamePointer,
                'dataProviderSyntaxMethodnameInvalid',
                [
                    $methodName,
                ]
            );
        }

        if (substr($methodName, -2) === '()') {
            $fix = $file->addFixableWarning(
                'Data provider should not end with "()". "%s" provided.',
                $methodNamePointer,
                'dataProviderSyntaxMethodnameContainsParenthesis',
                [
                    $methodName,
                ]
            );

            $methodName = substr($methodName, 0, -2);
            if ($fix) {
                $file->fixer->beginChangeset();
                $file->fixer->replaceToken($methodNamePointer, $methodName);
                $file->fixer->endChangeset();
            }
        }

        // Find the method itself.
        $classPointer = $file->findPrevious(T_CLASS, $methodNamePointer - 1);
        $providerPointer = MoodleUtil::findClassMethodPointer($file, $classPointer, $methodName);
        if ($providerPointer === null) {
            $file->addError(
                'Data provider method "%s" not found.',
                $methodNamePointer,
                'dataProviderSyntaxMethodNotFound',
                [
                    $methodName,
                ]
            );

            return;
        }

        // https://docs.phpunit.de/en/9.6/writing-tests-for-phpunit.html#data-providers
        // A data provider method must be public and either return an array of arrays
        // or an object that implements the Iterator interface and yields an array for
        // each iteration step. For each array that is part of the collection the test
        // method will be called with the contents of the array as its arguments.

        // Check that the method is public.
        $methodProps = $file->getMethodProperties($providerPointer);
        if (!$methodProps['scope_specified']) {
            $fix = $file->addFixableError(
                'Data provider method "%s" visibility should be specified.',
                $providerPointer,
                'dataProviderSyntaxMethodVisibilityNotSpecified',
                [
                    $methodName,
                ]
            );

            if ($fix) {
                $file->fixer->beginChangeset();
                if ($methodProps['is_static']) {
                    $staticPointer = $file->findPrevious(T_STATIC, $providerPointer - 1);
                    $file->fixer->addContentBefore($staticPointer, 'public ');
                } else {
                    $file->fixer->addContentBefore($providerPointer, 'public ');
                }
                $file->fixer->endChangeset();
            }
        } elseif ($methodProps['scope'] !== 'public') {
            $scopePointer = $file->findPrevious(Tokens::$scopeModifiers, $providerPointer - 1);
            $fix = $file->addFixableError(
                'Data provider method "%s" must be public.',
                $scopePointer,
                'dataProviderSyntaxMethodNotPublic',
                [
                    $methodName,
                ]
            );

            if ($fix) {
                $file->fixer->beginChangeset();
                $file->fixer->replaceToken($scopePointer, 'public');
                $file->fixer->endChangeset();
            }
        }

        // Check the return type.
        $returnType = NamespaceScopeUtil::getQualifiedName(
            $file,
            $providerPointer,
            $methodProps['return_type']
        );
        switch ($returnType) {
            case 'array':
            case 'Generator':
            case 'Iterable':
                // All valid
                break;
            default:
                $returnPointer = $file->findNext(T_CLOSE_PARENTHESIS, $providerPointer + 1);
                $file->addError(
                    'Data provider method "%s" must return an array, a Generator or an Iterable.',
                    $returnPointer,
                    'dataProviderSyntaxMethodInvalidReturnType',
                    [
                        $methodName,
                    ]
                );
        }

        $tokens = $file->getTokens();
        // In preparation for PHPUnit 10, we want to recommend that data providers are statically defined.
        if (!$methodProps['is_static']) {
            $supportAutomatedFix = true;
            if (!$this->autofixStaticProviders) {
                $supportAutomatedFix = false;
            } else {
                // We can make this fixable if the method does not contain any `$this`.
                // Search the body.
                $currentPointer = $tokens[$providerPointer]['scope_opener'] + 1;
                $bodyEnd = $tokens[$providerPointer]['scope_closer'] - 1;
                while ($token = $file->findNext(T_VARIABLE, $currentPointer, $bodyEnd)) {
                    if ($tokens[$token]['content'] === '$this') {
                        $supportAutomatedFix = false;
                        break;
                    }
                    $currentPointer = $token + 1;
                }
            }

            if (!$supportAutomatedFix) {
                $file->addWarning(
                    'Data provider method "%s" will need to be converted to static in future.',
                    $providerPointer,
                    'dataProviderNotStatic',
                    [
                        $methodName,
                    ]
                );
            } else {
                $fix = $file->addFixableWarning(
                    'Data provider method "%s" will need to be converted to static in future.',
                    $providerPointer,
                    'dataProviderNotStatic',
                    [
                        $methodName,
                    ]
                );

                if ($fix) {
                    $file->fixer->beginChangeset();
                    $file->fixer->addContentBefore($providerPointer, "static ");
                    $uses = self::findMethodCalls($file, $classPointer, $methodName);
                    foreach ($uses as $use) {
                        $file->fixer->replaceToken($use['start'], 'self::' . $methodName);
                        $file->fixer->replaceToken($use['start'] + 1, '');
                        $file->fixer->replaceToken($use['start'] + 2, '');
                    }
                    $file->fixer->endChangeset();
                }
            }
        }
    }


    /**
     * Find all calls to a method.
     * @param File $phpcsFile
     * @param int $classPtr
     * @param string $methodName
     * @return array
     */
    protected static function findMethodCalls(
        File $phpcsFile,
        int $classPtr,
        string $methodName
    ): array {
        $data = [];

        $mStart = $classPtr;
        $tokens = $phpcsFile->getTokens();
        while ($mStart = $phpcsFile->findNext(T_VARIABLE, $mStart + 1, $tokens[$classPtr]['scope_closer'])) {
            if ($tokens[$mStart]['content'] !== '$this') {
                continue;
            }
            if ($tokens[$mStart + 1]['code'] !== T_OBJECT_OPERATOR) {
                continue;
            }
            if ($tokens[$mStart + 2]['content'] !== $methodName) {
                continue;
            }

            $data[] = [
                'start' => $mStart,
                'end' => $mStart + 2,
            ];
        }

        return $data;
    }
}
