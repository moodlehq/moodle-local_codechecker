<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2020 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\Syntax;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the ForbiddenCallTimePassByReference sniff.
 *
 * @group forbiddenCallTimePassByReference
 * @group syntax
 *
 * @covers \PHPCompatibility\Sniffs\Syntax\ForbiddenCallTimePassByReferenceSniff
 *
 * @since 5.5
 */
class ForbiddenCallTimePassByReferenceUnitTest extends BaseSniffTest
{

    /**
     * testForbiddenCallTimePassByReference
     *
     * @dataProvider dataForbiddenCallTimePassByReference
     *
     * @param int $line Line number where the error should occur.
     *
     * @return void
     */
    public function testForbiddenCallTimePassByReference($line)
    {
        $file = $this->sniffFile(__FILE__, '5.3');
        $this->assertWarning($file, $line, 'Using a call-time pass-by-reference is deprecated since PHP 5.3');

        $file = $this->sniffFile(__FILE__, '5.4');
        $this->assertError($file, $line, 'Using a call-time pass-by-reference is deprecated since PHP 5.3 and prohibited since PHP 5.4');
    }

    /**
     * dataForbiddenCallTimePassByReference
     *
     * @see testForbiddenCallTimePassByReference()
     *
     * @return array
     */
    public function dataForbiddenCallTimePassByReference()
    {
        return [
            [10], // Bad: call time pass by reference.
            [14], // Bad: call time pass by reference with multi-parameter call.
            [17], // Bad: nested call time pass by reference.
            [25], // Bad: call time pass by reference with space.
            [44], // Bad: call time pass by reference.
            [45], // Bad: call time pass by reference.
            [49], // Bad: multiple call time pass by reference.
            [71], // Bad: call time pass by reference.
            [93], // Bad: call time pass by reference.
        ];
    }


    /**
     * testNoFalsePositives
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '5.4');
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
            [4], // OK: Declaring a parameter by reference.
            [9], // OK: Call time passing without reference.

            // OK: Bitwise operations as parameter.
            [20],
            [21],
            [22],
            [23],
            [24],
            [39],
            [40],
            [41],

            [51], // OK: No variables.
            [53], // OK: Outside scope of this sniff.

            // Assign by reference within function call.
            [56],
            [57],
            [58],
            [59],
            [60],
            [61],
            [62],
            [63],
            [64],
            [65],
            [66],
            [67],
            [68],
            [69],

            // Comparison with reference.
            [74],
            [75],

            // Issue #39 - Bitwise operations with (class) constants.
            [78],
            [79],
            [80],

            // References in combination with closures.
            [83],
            [85],
            [90],

            // Reference within an array argument.
            [96],
            [97],
            [99],

            // References in combination with arrow functions.
            [102],
            [103],
            [104],
        ];
    }


    /**
     * Verify no notices are thrown at all.
     *
     * @return void
     */
    public function testNoViolationsInFileOnValidVersion()
    {
        $file = $this->sniffFile(__FILE__, '5.2');
        $this->assertNoViolation($file);
    }
}
