<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2020 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Sniffs\ParameterValues;

use PHPCompatibility\AbstractFunctionCallParameterSniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Utils\PassedParameters;

/**
 * Detect calls to Iconv and Mbstring functions with the optional `$encoding` parameter not set.
 *
 * The default value for the iconv and MbString `$encoding` parameters was changed
 * in PHP 5.6 to the value of `default_charset`, which defaults to `UTF-8`.
 *
 * Previously, the iconv functions would default to the value of `iconv.internal_encoding`;
 * The Mbstring functions would default to the return value of `mb_internal_encoding()`.
 * In both case, this would normally come down to `ISO-8859-1`.
 *
 * PHP version 5.6
 *
 * @link https://www.php.net/manual/en/migration56.new-features.php#migration56.new-features.default-encoding
 * @link https://www.php.net/manual/en/migration56.deprecated.php#migration56.deprecated.iconv-mbstring-encoding
 * @link https://wiki.php.net/rfc/default_encoding
 *
 * @since 9.3.0
 */
class NewIconvMbstringCharsetDefaultSniff extends AbstractFunctionCallParameterSniff
{

    /**
     * Functions to check for.
     *
     * Only those functions where the charset/encoding parameter is optional need to be listed.
     *
     * Key is the function name, value an array containing the 1-based parameter position
     * and the official name of the parameter.
     *
     * @since 9.3.0
     *
     * @var array
     */
    protected $targetFunctions = [
        'iconv_mime_decode_headers' => [
            'position' => 3,
            'name'     => 'encoding',
        ],
        'iconv_mime_decode' => [
            'position' => 3,
            'name'     => 'encoding',
        ],
        // Special case.
        'iconv_mime_encode' => [
            'position' => 3,
            'name'     => 'options',
        ],
        'iconv_strlen' => [
            'position' => 2,
            'name'     => 'encoding',
        ],
        'iconv_strpos' => [
            'position' => 4,
            'name'     => 'encoding',
        ],
        'iconv_strrpos' => [
            'position' => 3,
            'name'     => 'encoding',
        ],
        'iconv_substr' => [
            'position' => 4,
            'name'     => 'encoding',
        ],

        'mb_check_encoding' => [
            'position' => 2,
            'name'     => 'encoding',
        ],
        'mb_chr' => [
            'position' => 2,
            'name'     => 'encoding',
        ],
        'mb_convert_case' => [
            'position' => 3,
            'name'     => 'encoding',
        ],
        'mb_convert_encoding' => [
            'position' => 3,
            'name'     => 'from_encoding',
        ],
        'mb_convert_kana' => [
            'position' => 3,
            'name'     => 'encoding',
        ],
        'mb_decode_numericentity' => [
            'position' => 3,
            'name'     => 'encoding',
        ],
        'mb_encode_numericentity' => [
            'position' => 3,
            'name'     => 'encoding',
        ],
        'mb_ord' => [
            'position' => 2,
            'name'     => 'encoding',
        ],
        'mb_scrub' => [
            'position' => 2,
            'name'     => 'encoding',
        ],
        'mb_strcut' => [
            'position' => 4,
            'name'     => 'encoding',
        ],
        'mb_stripos' => [
            'position' => 4,
            'name'     => 'encoding',
        ],
        'mb_stristr' => [
            'position' => 4,
            'name'     => 'encoding',
        ],
        'mb_strlen' => [
            'position' => 2,
            'name'     => 'encoding',
        ],
        'mb_strpos' => [
            'position' => 4,
            'name'     => 'encoding',
        ],
        'mb_strrchr' => [
            'position' => 4,
            'name'     => 'encoding',
        ],
        'mb_strrichr' => [
            'position' => 4,
            'name'     => 'encoding',
        ],
        'mb_strripos' => [
            'position' => 4,
            'name'     => 'encoding',
        ],
        'mb_strrpos' => [
            'position' => 4,
            'name'     => 'encoding',
        ],
        'mb_strstr' => [
            'position' => 4,
            'name'     => 'encoding',
        ],
        'mb_strtolower' => [
            'position' => 2,
            'name'     => 'encoding',
        ],
        'mb_strtoupper' => [
            'position' => 2,
            'name'     => 'encoding',
        ],
        'mb_strwidth' => [
            'position' => 2,
            'name'     => 'encoding',
        ],
        'mb_substr_count' => [
            'position' => 3,
            'name'     => 'encoding',
        ],
        'mb_substr' => [
            'position' => 4,
            'name'     => 'encoding',
        ],
    ];


    /**
     * Do a version check to determine if this sniff needs to run at all.
     *
     * Note: This sniff should only trigger errors when both PHP 5.5 or lower,
     * as well as PHP 5.6 or higher need to be supported within the application.
     *
     * @since 9.3.0
     *
     * @return bool
     */
    protected function bowOutEarly()
    {
        return ($this->supportsBelow('5.5') === false || $this->supportsAbove('5.6') === false);
    }


    /**
     * Process the parameters of a matched function.
     *
     * @since 9.3.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile    The file being scanned.
     * @param int                         $stackPtr     The position of the current token in the stack.
     * @param string                      $functionName The token content (function name) which was matched.
     * @param array                       $parameters   Array with information about the parameters.
     *
     * @return int|void Integer stack pointer to skip forward or void to continue
     *                  normal file processing.
     */
    public function processParameters(File $phpcsFile, $stackPtr, $functionName, $parameters)
    {
        $functionLC = \strtolower($functionName);
        if ($functionLC === 'iconv_mime_encode') {
            // Special case the iconv_mime_encode() function.
            return $this->processIconvMimeEncode($phpcsFile, $stackPtr, $functionName, $parameters);
        }

        $paramInfo   = $this->targetFunctions[$functionLC];
        $targetParam = PassedParameters::getParameterFromStack($parameters, $paramInfo['position'], $paramInfo['name']);
        if ($targetParam !== false) {
            return;
        }

        $error = 'The default value of the $%1$s parameter for %2$s() was changed from ISO-8859-1 to UTF-8 in PHP 5.6. For cross-version compatibility, the $%1$s parameter should be explicitly set.';
        $data  = [
            $paramInfo['name'],
            $functionName,
        ];

        $phpcsFile->addError($error, $stackPtr, 'NotSet', $data);
    }

    /**
     * Process the parameters of a matched call to the iconv_mime_encode() function.
     *
     * @since 9.3.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile    The file being scanned.
     * @param int                         $stackPtr     The position of the current token in the stack.
     * @param string                      $functionName The token content (function name) which was matched.
     * @param array                       $parameters   Array with information about the parameters.
     *
     * @return int|void Integer stack pointer to skip forward or void to continue
     *                  normal file processing.
     */
    public function processIconvMimeEncode(File $phpcsFile, $stackPtr, $functionName, $parameters)
    {
        $error = 'The default value of the %s parameter index for iconv_mime_encode() was changed from ISO-8859-1 to UTF-8 in PHP 5.6. For cross-version compatibility, the %s should be explicitly set.';

        $functionLC  = \strtolower($functionName);
        $paramInfo   = $this->targetFunctions[$functionLC];
        $targetParam = PassedParameters::getParameterFromStack($parameters, $paramInfo['position'], $paramInfo['name']);
        if ($targetParam === false) {
            $phpcsFile->addError(
                $error,
                $stackPtr,
                'PreferencesNotSet',
                [
                    '$options[\'input/output-charset\']',
                    '$options[\'input-charset\'] and $options[\'output-charset\'] indexes',
                ]
            );

            return;
        }

        $tokens        = $phpcsFile->getTokens();
        $firstNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, $targetParam['start'], ($targetParam['end'] + 1), true);
        if ($firstNonEmpty === false) {
            // Parse error or live coding.
            return;
        }

        if ($tokens[$firstNonEmpty]['code'] === \T_ARRAY
            || $tokens[$firstNonEmpty]['code'] === \T_OPEN_SHORT_ARRAY
        ) {
            // Note: the item names are treated case-sensitively in PHP, so match on exact case.
            $hasInputCharset  = \preg_match('`([\'"])input-charset\1\s*=>`', $targetParam['clean']);
            $hasOutputCharset = \preg_match('`([\'"])output-charset\1\s*=>`', $targetParam['clean']);
            if ($hasInputCharset === 1 && $hasOutputCharset === 1) {
                // Both input as well as output charset are set.
                return;
            }

            if ($hasInputCharset !== 1) {
                $phpcsFile->addError(
                    $error,
                    $firstNonEmpty,
                    'InputPreferenceNotSet',
                    [
                        '$options[\'input-charset\']',
                        '$options[\'input-charset\'] index',
                    ]
                );
            }

            if ($hasOutputCharset !== 1) {
                $phpcsFile->addError(
                    $error,
                    $firstNonEmpty,
                    'OutputPreferenceNotSet',
                    [
                        '$options[\'output-charset\']',
                        '$options[\'output-charset\'] index',
                    ]
                );
            }

            return;
        }

        // The $options parameter was passed, but it was a variable/constant/output of a function call.
        $phpcsFile->addWarning(
            $error,
            $firstNonEmpty,
            'Undetermined',
            [
                '$options[\'input/output-charset\']',
                '$options[\'input-charset\'] and $options[\'output-charset\'] indexes',
            ]
        );
    }
}
