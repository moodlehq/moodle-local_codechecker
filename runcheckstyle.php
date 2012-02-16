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
 * Runs each of the top level moodle folders and files through the codechecker then combines the
 * results into one XML output
 *
 * @package    local
 * @subpackage codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

/*
0: No Logging.
1: Print progress information.
2: Print developer debug information
*/
define('LOGLEVEL', 0);

require(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/clilib.php');

// Get the command-line options.
list($options, $unrecognized) = cli_get_params(array());

if (count($unrecognized)) {
    echo get_string('clihelp2', 'local_codechecker'), "\n";
    die();
}


ini_set('memory_limit', -1);
set_time_limit(3600);

$todo = array();
if ($handle = opendir($CFG->dirroot)) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != ".." && substr($file, 0, 1) != '.' &&
            (substr(strrchr($file, '.'), 1) == 'php' || is_dir($CFG->dirroot.'/'.$file))) {
            $todo[] = $file;
        }
    }
    closedir($handle);
}

sort($todo);

function runcli_make_filename($item) {
    $filename    = basename($item);
    $foldername  = str_replace($filename, '', $item);
    $foldername  = str_replace('/', '~', $foldername);
    return "{$foldername}{$filename}.xml";
}

//- Do all of the files
$count = 0;
foreach ($todo as $item) {
    $output = array();
    $run  = escapeshellarg(dirname(__file__).'/run.php');
    $file = escapeshellarg("/$item");
    $save = escapeshellarg(runcli_make_filename($item));
    $cmd = "php $run --type=checkstyle --loglvl=".LOGLEVEL." --file=$save $file";

    if (LOGLEVEL > 0) {
        echo "------------------- START $item -------------------\n";
    }

    //- This will echo out any data as it goes.
    //- This shouldn't output any data if logging isn't enabled!
    system($cmd);

    if (LOGLEVEL > 0) {
        echo "------------------- END $item -------------------\n\n";
    }
}

//- Now merge files together
$xml = '';
$checkstyle_start = '';
foreach ($todo as $item) {
    $file = join("", file($CFG->dataroot.'/codechecker/'.runcli_make_filename($item)));
    if (empty($checkstyle_start)) {
        //- Find the checkstyle tag
        $pos = strpos($file, '<checkstyle');
        $end = strpos($file, '>', $pos)+1;
        $checkstyle_start = substr($file, 0, $end);
    }

    $pos      = strpos($file, '<checkstyle');
    $ckend    = strpos($file, '>', $pos)+1;
    $fileend  = strpos($file, '</checkstyle>', $ckend);
    $length   = $fileend-$ckend;
    $xml     .= "\n".trim(substr($file, $ckend, $length));
}
$xml = "$checkstyle_start\n$xml\n</checkstyle>";
file_put_contents($CFG->dataroot.'/codechecker/codechecker_checkstyle.xml', $xml);