<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2020 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\TextStrings;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewUnicodeEscapeSequence sniff.
 *
 * @group newUnicodeEscapeSequence
 * @group textStrings
 *
 * @covers \PHPCompatibility\Sniffs\TextStrings\NewUnicodeEscapeSequenceSniff
 *
 * @since 9.3.0
 */
class NewUnicodeEscapeSequenceUnitTest extends BaseSniffTest
{

    /**
     * testNewUnicodeEscapeSequence
     *
     * @dataProvider dataNewUnicodeEscapeSequence
     *
     * @param int    $line     Line number where the error should occur.
     * @param string $sequence The unicode escape sequence found.
     *
     * @return void
     */
    public function testNewUnicodeEscapeSequence($line, $sequence)
    {
        $file  = $this->sniffFile(__FILE__, '5.6');
        $error = 'Unicode codepoint escape sequences are not supported in PHP 5.6 or earlier. Found: ' . $sequence;
        $this->assertError($file, $line, $error);
    }

    /**
     * dataNewUnicodeEscapeSequence
     *
     * @see testNewUnicodeEscapeSequence()
     *
     * @return array
     */
    public function dataNewUnicodeEscapeSequence()
    {
        return [
            [41, '\u{aa}'],
            [43, '\u{0000aa}'],
            [44, '\u{9999}'],
            [46, '\u{9999}'],
            [47, '\u{00F1}'],
            [48, '\u{0303}'],
            [49, '\u{1F602}'],
            [52, '\u{aa}'],
            [55, '\u{202E}'],
            [59, '\u{D801}'],
            [60, '\u{DC00}'],
            [61, '\u{D801}'],
            [61, '\u{DC00}'],
        ];
    }


    /**
     * testNoFalsePositivesNewSequence
     *
     * @return void
     */
    public function testNoFalsePositivesNewSequence()
    {
        $file = $this->sniffFile(__FILE__, '5.6');

        // No errors expected on the first 40 lines.
        for ($line = 1; $line <= 40; $line++) {
            $this->assertNoViolation($file, $line);
        }
    }


    /**
     * testNewUnicodeEscapeSequenceFatals
     *
     * @dataProvider dataNewUnicodeEscapeSequenceFatals
     *
     * @param int    $line     Line number where the error should occur.
     * @param string $sequence The invalid unicode escape sequence found.
     *
     * @return void
     */
    public function testNewUnicodeEscapeSequenceFatals($line, $sequence)
    {
        $file  = $this->sniffFile(__FILE__, '7.0');
        $error = 'Strings containing a literal \u{ followed by an invalid unicode codepoint escape sequence will cause a fatal error in PHP 7.0 and above. Escape the leading backslash to prevent this. Found: ' . $sequence;

        $this->assertError($file, $line, $error);
    }

    /**
     * dataNewUnicodeEscapeSequenceFatals
     *
     * @see testNewUnicodeEscapeSequenceFatals()
     *
     * @return array
     */
    public function dataNewUnicodeEscapeSequenceFatals()
    {
        return [
            [27, '\u{foobar'],
            [28, '\u{9999'],
            [29, '\u{}'],
            [30, '\u{+1F602}'],
            [31, '\u{-1F602}'],
            [32, '\u{1F602 }'],
            [35, '\u{110000}'],
        ];
    }


    /**
     * testNoFalsePositivesFatals
     *
     * @return void
     */
    public function testNoFalsePositivesFatals()
    {
        $file = $this->sniffFile(__FILE__, '7.0');

        // No errors expected on the first 25 lines.
        for ($line = 1; $line <= 25; $line++) {
            $this->assertNoViolation($file, $line);
        }

        // No errors expected on the last 20 or so lines.
        for ($line = 40; $line <= 62; $line++) {
            $this->assertNoViolation($file, $line);
        }
    }


    /*
     * `testNoViolationsInFileOnValidVersion` test omitted as this sniff will throw errors
     * in all testVersions.
     */
}
