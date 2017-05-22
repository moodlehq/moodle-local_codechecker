<?php
/**
 * PHPCompatibility_Sniffs_PHP_NewAnonymousClasses.
 *
 * PHP version 7.0
 *
 * @category  PHP
 * @package   PHPCompatibility
 * @author    Wim Godden <wim.godden@cu.be>
 */

/**
 * PHPCompatibility_Sniffs_PHP_NewAnonymousClasses.
 *
 * Anonymous classes are supported in PHP 7.0
 *
 * @category  PHP
 * @package   PHPCompatibility
 * @author    Wim Godden <wim.godden@cu.be>
 */
class PHPCompatibility_Sniffs_PHP_NewAnonymousClassesSniff extends PHPCompatibility_Sniff
{

    private $indicators = array(
        T_CLASS => T_CLASS,
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        if (defined('T_ANON_CLASS')) {
            $this->indicators[T_ANON_CLASS] = T_ANON_CLASS;
        }

        return array(T_NEW);
    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        if ($this->supportsBelow('5.6') === false) {
            return;
        }

        $tokens       = $phpcsFile->getTokens();
        $nextNonEmpty = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, $stackPtr + 1, null, true, null, true);
        if ($nextNonEmpty === false || isset($this->indicators[$tokens[$nextNonEmpty]['code']]) === false) {
            return;
        }

        // Still here ? In that case, it is an anonymous class.
        $phpcsFile->addError(
            'Anonymous classes are not supported in PHP 5.6 or earlier',
            $stackPtr,
            'Found'
        );

    }//end process()


}//end class
