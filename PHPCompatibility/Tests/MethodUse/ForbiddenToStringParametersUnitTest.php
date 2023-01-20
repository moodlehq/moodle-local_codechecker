<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2020 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\MethodUse;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the ForbiddenToStringParameters sniff.
 *
 * @group newForbiddenToStringParameters
 * @group methodUse
 *
 * @covers \PHPCompatibility\Sniffs\MethodUse\ForbiddenToStringParametersSniff
 *
 * @since 9.2.0
 */
class ForbiddenToStringParametersUnitTest extends BaseSniffTest
{

    /**
     * testForbiddenToStringParameters.
     *
     * @dataProvider dataForbiddenToStringParameters
     *
     * @param int $line The line number where a warning is expected.
     *
     * @return void
     */
    public function testForbiddenToStringParameters($line)
    {
        $file = $this->sniffFile(__FILE__, '5.3');
        $this->assertError($file, $line, 'The __toString() magic method will no longer accept passed arguments since PHP 5.3');
    }

    /**
     * Data provider.
     *
     * @see testForbiddenToStringParameters()
     *
     * @return array
     */
    public function dataForbiddenToStringParameters()
    {
        return [
            [37],
            [38],
            [39],
            [44],
            [45],
            [46],
            [47],
            [48],
            [52],
        ];
    }


    /**
     * testNoFalsePositives.
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '5.3');
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
        $cases = [];
        // No errors expected on the first 35 lines.
        for ($line = 1; $line <= 35; $line++) {
            $cases[] = [$line];
        }

        return $cases;
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
