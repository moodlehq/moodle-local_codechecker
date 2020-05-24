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

/**
 * Sniff for debugging and other functions that we don't want used in finished code.
 *
 * @package    local_codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleCodeSniffer\moodle\Sniffs\PHP;

use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff as GenericForbiddenFunctionsSniff;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class ForbiddenFunctionsSniff extends GenericForbiddenFunctionsSniff {

    /** Constructor. */
    public function __construct() {
        $this->forbiddenFunctions = array(
            // Usual development debugging functions.
            'sizeof'       => 'count',
            'delete'       => 'unset',
            'error_log'    => null,
            'print_r'      => null,
            'print_object' => null,
            // Dangerous functions. From coding style.
            'extract'      => null,
        );
    }
}
