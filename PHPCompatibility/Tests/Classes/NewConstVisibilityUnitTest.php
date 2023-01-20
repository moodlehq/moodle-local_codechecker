<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2020 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Classes;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewConstVisibility sniff.
 *
 * @group newConstVisibility
 * @group classes
 *
 * @covers \PHPCompatibility\Sniffs\Classes\NewConstVisibilitySniff
 *
 * @since 7.0.7
 */
class NewConstVisibilityUnitTest extends BaseSniffTest
{

    /**
     * Test that an error is thrown for class constants declared with visibility.
     *
     * @dataProvider dataConstVisibility
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testConstVisibility($line)
    {
        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertError($file, $line, 'Visibility indicators for class constants are not supported in PHP 7.0 or earlier.');
    }

    /**
     * Data provider.
     *
     * @see testConstVisibility()
     *
     * @return array
     */
    public function dataConstVisibility()
    {
        return [
            [10],
            [11],
            [12],

            [20],
            [23],
            [24],

            [33],
            [34],
            [35],

            [57],
            [58],
            [59],
        ];
    }


    /**
     * Verify that there are no false positives for valid code.
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '7.0');
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testNoFalsePositives()
     *
     * @return array
     */
    public function dataNoFalsePositives()
    {
        return [
            [3],
            [7],
            [17],
            [30],
            [44],
            [48],
        ];
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '7.1');
        $this->assertNoViolation($file);
    }
}
