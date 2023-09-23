<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace MoodleHQ\MoodleCS\moodle\Tests\Sniffs\Namespaces;

use MoodleHQ\MoodleCS\moodle\Tests\MoodleCSBaseTestCase;

// phpcs:disable moodle.NamingConventions

/**
 * Test the NoLeadingSlash sniff.
 *
 * @package    moodle-cs
 * @category   test
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleHQ\MoodleCS\moodle\Sniffs\Namespaces\NoLeadingSlashSniff
 */
class NamespaceStatementSniffTest extends MoodleCSBaseTestCase
{
    public static function leading_slash_provider(): array
    {
        return [
            [
                'fixture' => 'correct_namespace',
                'warnings' => [],
                'errors' => [],
            ],
            [
                'fixture' => 'leading_backslash',
                'warnings' => [],
                'errors' => [
                    3 => 'Namespace should not start with a slash: \MoodleHQ\MoodleCS\moodle\Tests\Sniffs\Namespaces',
                ],
            ],
            [
                'fixture' => 'curly_namespace',
                'warnings' => [],
                'errors' => [
                    3 => 'Namespace should not start with a slash: \MoodleHQ\MoodleCS\moodle\Tests\Sniffs\Namespaces',
                ],
            ],
        ];
    }
    /**
     * @dataProvider leading_slash_provider
     */
    public function test_leading_slash(
        string $fixture,
        array $warnings,
        array $errors
    ): void
    {
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Namespaces.NamespaceStatement');
        $this->set_fixture(sprintf("%s/fixtures/%s.php", __DIR__, $fixture));
        $this->set_warnings($warnings);
        $this->set_errors($errors);

        $this->verify_cs_results();
    }
}
