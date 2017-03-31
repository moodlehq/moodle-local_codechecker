<?php
/**
 * PHPCompatibility_Sniffs_PHP_NewExecutionDirectivesSniff.
 *
 * @category  PHP
 * @package   PHPCompatibility
 * @author    Juliette Reinders Folmer <phpcompatibility_nospam@adviesenzo.nl>
 */

/**
 * PHPCompatibility_Sniffs_PHP_NewExecutionDirectivesSniff.
 *
 * @category  PHP
 * @package   PHPCompatibility
 * @author    Juliette Reinders Folmer <phpcompatibility_nospam@adviesenzo.nl>
 */
class PHPCompatibility_Sniffs_PHP_NewExecutionDirectivesSniff extends PHPCompatibility_AbstractNewFeatureSniff
{

    /**
     * A list of new execution directives
     *
     * The array lists : version number with false (not present) or true (present).
     * If the execution order is conditional, add the condition as a string to the version nr.
     * If's sufficient to list the first version where the execution directive appears.
     *
     * @var array(string => array(string => int|string|null))
     */
    protected $newDirectives = array (
                                'ticks' => array(
                                    '3.1' => false,
                                    '4.0' => true,
                                    'valid_value_callback' => 'isNumeric',
                                ),
                                'encoding' => array(
                                    '5.2' => false,
                                    '5.3' => '--enable-zend-multibyte', // Directive ignored unless.
                                    '5.4' => true,
                                    'valid_value_callback' => 'validEncoding',
                                ),
                                'strict_types' => array(
                                    '5.6' => false,
                                    '7.0' => true,
                                    'valid_values' => array(1),
                                ),
                               );


    /**
     * Tokens to ignore when trying to find the value for the directive.
     *
     * @var array
     */
    protected $ignoreTokens = array();


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        $this->ignoreTokens          = PHP_CodeSniffer_Tokens::$emptyTokens;
        $this->ignoreTokens[T_EQUAL] = T_EQUAL;

        return array(T_DECLARE);
    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['parenthesis_opener'], $tokens[$stackPtr]['parenthesis_closer']) === true) {
	        $openParenthesis  = $tokens[$stackPtr]['parenthesis_opener'];
	        $closeParenthesis = $tokens[$stackPtr]['parenthesis_closer'];
        }
        else {
	        if (version_compare(PHP_CodeSniffer::VERSION, '2.0', '>=')) {
				return;
			}

			// Deal with PHPCS 1.x which does not set the parenthesis properly for declare statements.
			$openParenthesis = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($stackPtr + 1), null, false, null, true);
			if ($openParenthesis === false || isset($tokens[$openParenthesis]['parenthesis_closer']) === false) {
				return;
			}
			$closeParenthesis = $tokens[$openParenthesis]['parenthesis_closer'];
		}

        $directivePtr = $phpcsFile->findNext(T_STRING, ($openParenthesis + 1), $closeParenthesis, false);
        if ($directivePtr === false) {
            return;
        }

        $directiveContent = $tokens[$directivePtr]['content'];

        if (isset($this->newDirectives[$directiveContent]) === false) {
            $error = 'Declare can only be used with the directives %s. Found: %s';
            $data  = array(
                implode(', ', array_keys($this->newDirectives)),
                $directiveContent,
            );

            $phpcsFile->addError($error, $stackPtr, 'InvalidDirectiveFound', $data);
        }
        else {
            // Check for valid directive for version.
            $itemInfo = array(
                'name'   => $directiveContent,
            );
            $this->handleFeature($phpcsFile, $stackPtr, $itemInfo);

            // Check for valid directive value.
            $valuePtr = $phpcsFile->findNext($this->ignoreTokens, $directivePtr + 1, $closeParenthesis, true);
            if ($valuePtr === false) {
                return;
            }

            $this->addWarningOnInvalidValue($phpcsFile, $valuePtr, $directiveContent);
        }

    }//end process()


    /**
     * Determine whether an error/warning should be thrown for an item based on collected information.
     *
     * @param array $errorInfo Detail information about an item.
     *
     * @return bool
     */
    protected function shouldThrowError(array $errorInfo)
    {
        return ($errorInfo['not_in_version'] !== '' || $errorInfo['conditional_version'] !== '');
    }


    /**
     * Get the relevant sub-array for a specific item from a multi-dimensional array.
     *
     * @param array $itemInfo Base information about the item.
     *
     * @return array Version and other information about the item.
     */
    public function getItemArray(array $itemInfo)
    {
        return $this->newDirectives[$itemInfo['name']];
    }


    /**
     * Get an array of the non-PHP-version array keys used in a sub-array.
     *
     * @return array
     */
    protected function getNonVersionArrayKeys()
    {
        return array(
            'valid_value_callback',
            'valid_values',
        );
    }


    /**
     * Retrieve the relevant detail (version) information for use in an error message.
     *
     * @param array $itemArray Version and other information about the item.
     * @param array $itemInfo  Base information about the item.
     *
     * @return array
     */
    public function getErrorInfo(array $itemArray, array $itemInfo)
    {
        $errorInfo = parent::getErrorInfo($itemArray, $itemInfo);
        $errorInfo['conditional_version'] = '';
        $errorInfo['condition']           = '';

        $versionArray = $this->getVersionArray($itemArray);

        if (empty($versionArray) === false) {
            foreach ($versionArray as $version => $present) {
                if (is_string($present) === true && $this->supportsBelow($version) === true) {
                    // We cannot test for compilation option (ok, except by scraping the output of phpinfo...).
                    $errorInfo['conditional_version'] = $version;
                    $errorInfo['condition']           = $present;
                }
            }
        }

        return $errorInfo;
    }


    /**
     * Get the error message template for this sniff.
     *
     * @return string
     */
    protected function getErrorMsgTemplate()
    {
        return 'Directive '.parent::getErrorMsgTemplate();
    }


    /**
     * Generates the error or warning for this item.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the relevant token in
     *                                        the stack.
     * @param array                $itemInfo  Base information about the item.
     * @param array                $errorInfo Array with detail (version) information
     *                                        relevant to the item.
     *
     * @return void
     */
    public function addError(PHP_CodeSniffer_File $phpcsFile, $stackPtr, array $itemInfo, array $errorInfo)
    {
        if ($errorInfo['not_in_version'] !== '') {
            parent::addError($phpcsFile, $stackPtr, $itemInfo, $errorInfo);
        } else if ($errorInfo['conditional_version'] !== '') {
            $error     = 'Directive %s is present in PHP version %s but will be disregarded unless PHP is compiled with %s';
            $errorCode = $this->stringToErrorCode($itemInfo['name']).'WithConditionFound';
            $data      = array(
                $itemInfo['name'],
                $errorInfo['conditional_version'],
                $errorInfo['condition'],
            );

            $phpcsFile->addWarning($error, $stackPtr, $errorCode, $data);
        }

    }//end addError()


    /**
     * Generates a error or warning for this sniff.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the execution directive value
     *                                        in the token array.
     * @param string               $directive The directive.
     *
     * @return void
     */
    protected function addWarningOnInvalidValue($phpcsFile, $stackPtr, $directive)
    {
        $tokens = $phpcsFile->getTokens();

        $value = $tokens[$stackPtr]['content'];
        if (in_array($tokens[$stackPtr]['code'], PHP_CodeSniffer_Tokens::$stringTokens, true) === true) {
            $value = $this->stripQuotes($value);
        }

        $isError = false;
        if (isset($this->newDirectives[$directive]['valid_values'])) {
            if (in_array($value, $this->newDirectives[$directive]['valid_values']) === false) {
                $isError = true;
            }
        }
        else if (isset($this->newDirectives[$directive]['valid_value_callback'])) {
            $valid = call_user_func(array($this, $this->newDirectives[$directive]['valid_value_callback']), $value);
            if ($valid === false) {
                $isError = true;
            }
        }

        if ($isError === true) {
            $error     = 'The execution directive %s does not seem to have a valid value. Please review. Found: %s';
            $errorCode = $this->stringToErrorCode($directive).'InvalidValueFound';
            $data      = array(
                $directive,
                $value,
            );

            $phpcsFile->addWarning($error, $stackPtr, $errorCode, $data);
        }
    }// addErrorOnInvalidValue()


    /**
     * Check whether a value is numeric.
     *
     * Callback function to test whether the value for an execution directive is valid.
     *
     * @param mixed $value The value to test.
     *
     * @return bool
     */
    protected function isNumeric($value)
    {
        return is_numeric($value);
    }


    /**
     * Check whether a value is valid encoding.
     *
     * Callback function to test whether the value for an execution directive is valid.
     *
     * @param mixed $value The value to test.
     *
     * @return bool
     */
    protected function validEncoding($value)
    {
        $encodings = array();
        if (function_exists('mb_list_encodings')) {
            $encodings = mb_list_encodings();
        }

        if (empty($encodings)) {
            // If we can't test the encoding, let it pass through.
            return true;
        }

        if (in_array($value, $encodings, true)) {
            return true;
        }
        else {
            return false;
        }
    }


}//end class
