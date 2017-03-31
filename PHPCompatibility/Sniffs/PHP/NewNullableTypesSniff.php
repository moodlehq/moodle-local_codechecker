<?php
/**
 * PHPCompatibility_Sniffs_PHP_NewNullableTypes.
 *
 * PHP version 7.1
 *
 * @category  PHP
 * @package   PHPCompatibility
 * @author    Juliette Reinders Folmer <phpcompatibility_nospam@adviesenzo.nl>
 */

/**
 * PHPCompatibility_Sniffs_PHP_NewNullableTypes.
 *
 * Nullable type hints and return types are available since PHP 7.1.
 *
 * PHP version 7.1
 *
 * @category  PHP
 * @package   PHPCompatibility
 * @author    Juliette Reinders Folmer <phpcompatibility_nospam@adviesenzo.nl>
 */
class PHPCompatibility_Sniffs_PHP_NewNullableTypesSniff extends PHPCompatibility_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * {@internal Not sniffing for T_NULLABLE which was introduced in PHPCS 2.7.2
     * as in that case we can't distinguish between parameter type hints and
     * return type hints for the error message.}}
     *
     * @return array
     */
    public function register()
    {
        $tokens = array(
            T_FUNCTION,
            T_CLOSURE,
        );

        if (defined('T_RETURN_TYPE')) {
            $tokens[] = T_RETURN_TYPE;
        }

        return $tokens;

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        if ($this->supportsBelow('7.0') === false) {
            return;
        }

        $tokens    = $phpcsFile->getTokens();
        $tokenCode = $tokens[$stackPtr]['code'];

        if ($tokenCode === T_FUNCTION || $tokenCode === T_CLOSURE) {
            $this->processFunctionDeclaration($phpcsFile, $stackPtr);

            // Deal with older PHPCS version which don't recognize return type hints.
            $returnTypeHint = $this->getReturnTypeHintToken($phpcsFile, $stackPtr);
            if ($returnTypeHint !== false) {
                $this->processReturnType($phpcsFile, $returnTypeHint);
            }
        } else {
            $this->processReturnType($phpcsFile, $stackPtr);
        }

    }//end process()


    /**
     * Process this test for function tokens.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    protected function processFunctionDeclaration(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $params = $this->getMethodParameters($phpcsFile, $stackPtr);

        if (empty($params) === false && is_array($params)) {
            foreach ($params as $param) {
                if ($param['nullable_type'] === true) {
                    $phpcsFile->addError(
                        'Nullable type declarations are not supported in PHP 7.0 or earlier. Found: %s',
                        $stackPtr,
                        'typeDeclarationFound',
                        array($param['type_hint'])
                    );
                }
            }
        }
    }


    /**
     * Process this test for return type tokens.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    protected function processReturnType(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[($stackPtr - 1)]['code']) === false) {
            return;
        }

        $error  = false;

        // T_NULLABLE token was introduced in PHPCS 2.7.2. Before that it identified as T_INLINE_THEN.
        if ((defined('T_NULLABLE') === true && $tokens[($stackPtr - 1)]['type'] === 'T_NULLABLE')
            || (defined('T_NULLABLE') === false && $tokens[($stackPtr - 1)]['code'] === T_INLINE_THEN)
        ) {
            $error = true;
        }
        // Deal with namespaced class names.
        elseif ($tokens[($stackPtr - 1)]['code'] === T_NS_SEPARATOR
            || (version_compare(PHP_VERSION, '5.3.0', '<') && $tokens[($stackPtr - 1)]['code'] === T_STRING)
        ) {
            $validTokens = array(
                T_STRING,
                T_NS_SEPARATOR,
                T_WHITESPACE,
            );
            $stackPtr--;

            while(in_array($tokens[($stackPtr - 1)]['code'], $validTokens, true) === true) {
                $stackPtr--;
            }

            if ((defined('T_NULLABLE') === true && $tokens[($stackPtr - 1)]['type'] === 'T_NULLABLE')
                || (defined('T_NULLABLE') === false && $tokens[($stackPtr - 1)]['code'] === T_INLINE_THEN)
            ) {
                $error = true;
            }
        }

        if ($error === true) {
            $phpcsFile->addError(
                'Nullable return types are not supported in PHP 7.0 or earlier.',
                $stackPtr,
                'returnTypeFound'
            );
        }
    }

}//end class
