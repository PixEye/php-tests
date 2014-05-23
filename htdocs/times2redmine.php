#!/usr/bin/env php
<?php
/**
 * This script parses my time-sheet text file & transform it to remdine time records
 *
 * Last commit of this file (GMT):
 * $Id$
 *
 * PHP version 5.3
 *
 * @category  PHP
 * @package   PhpTests
 * @author    Julien Moreau <jmoreau@pixeye.net>
 * @copyright 2013 VeePee
 * @license   Affero GPL http://choosealicense.com/licenses/agpl/
 * @version   GIT: $Revision$
 * @link      https://github.com/PixEye/php-tests
 * @since     2013-09-30
 * @filesource
 */

require_once 'Include/RedmineTimeEntry.class.php';

// Configuration:
$nl = PHP_EOL;
$start_tic = microtime(true);
$input = '~/Dropbox/Documents/Work/VP/calepin.wri';
$line_format = '^(20\d\d-\d\d-\d\d) [a-z][a-z]\/ ';

// Some tests:
$te = new RedmineTimeEntry('2014-05-19', 1.0, 1368);
$te = new RedmineTimeEntry('2014-05-19', '1.0', '1368');

if (!isSet($argv)) { // if called from the web
    is_readable('Include/head.php') && include_once 'Include/head.php';
}

if (isSet($argv) && isSet($argv[1])) {
    if ('-h'===$argv[1] || '--help'===$argv[1]) {
        $cmd = basename($argv[0]);
        fprintf(
            STDERR,
            "Parse a time-sheet text file to generate redmine time records.%s%s",
            $nl, $nl
        );
        fprintf(STDERR, "Usage:$nl\t%s [input_text_file]%s", $cmd, $nl);
        fprintf(STDERR, "%sDefault input file is:$nl\t%s%s", $nl, $input, $nl);
        exit(1);
    }

    $input = trim($argv[1]);
}

$input = trim($input);
if ('~'===subStr($input, 0, 1)) {
    $home = getEnv('HOME');
    if (false===$home) {
        die("No HOME env variable set!".$nl);
    }

    $input = $home.subStr($input, 1);
}

if (!is_readable($input)) {
    die("'$input' is not readable!".$nl);
}

$fd = fopen($input, 'r');
if (false===$fd) {
    die("Cannot open '$input'!".$nl);
}

/**
 * Transform '1:12' to 1.2 for example
 *
 * @param string $time Example: '1:12'
 *
 * @return float
 */
function time2float ($time)
{
    $time = str_replace('h', ':', $time);
    list($h, $m) = explode(':', $time);

    return round($h + $m/60, 1);
}

$nb_eod_lines = 0;
$nb_read_lines = 0;
$nb_data_lines = 0;
$nb_empty_lines = 0;
$nb_lunch_lines = 0;
$nb_ignored_lines = 0;
$nb_lines_with_date = 0;
while (($line = fgets($fd, 4096)) !== false) {
    ++$nb_read_lines;

    // Comment lines:
    if ('#'===subStr($line, 0, 1)) {
        ++$nb_ignored_lines;
        continue;
    }

    $line = trim($line);

    // Empty lines:
    if (''===$line) {
        ++$nb_empty_lines;
        ++$nb_ignored_lines;
        continue;
    }

    $line = str_replace("\t", ' ', $line);

    // Start of the day lines:
    if ('20'===subStr($line, 0, 2)) {
        ++$nb_lines_with_date;

        unset($section, $comments, $task_id);
        $date = subStr($line, 0, 10);

        $words = explode(' ', $line);
        $start_time = $words[2];
        $start_float = time2float($start_time);
        $prefix = "$date /$start_time ~= $start_float h| ";

        $line = subStr($line, 21);
        $records2add = array();
        $nb_lines_in_day = 0;
    } elseIf (!isSet($date)) {
        ++$nb_ignored_lines;
        continue;
    } else {
        $prefix = "$date ";
    }

    // Lunch lines:
    if (':'===subStr($line, 1, 1)) {
        ++$nb_lunch_lines;
        $lunch_period = subStr($line, 0, 4);
        $lunch_nb_h = time2float($lunch_period);

        // Optional:
        $prefix.= "|$lunch_period ~= $lunch_nb_h h| ";
        $line = ltrim(subStr($line, 5));
        echo $prefix, $line, $nl;

        continue;
    }

    // End of the day lines:
    $marker = ' => ';
    $pos = strPos($line, $marker);
    if (false!==$pos) {
        $words = explode(' ', $line);
        $nb_words = count($words);
        if ($nb_words>=3 && $words[1]===trim($marker)) {
            ++$nb_eod_lines;
            $quit_time = $words[0];
            $nb_h_pre_computed = $words[2];
            if ('h'===subStr($nb_h_pre_computed, -1)) {
                $nb_h_pre_computed = subStr($nb_h_pre_computed, 0, -1);
            }
            if (!is_numeric($nb_h_pre_computed)) {
                die("Invalid end of day line:$nl$line$nl");
            }

            $prefix.= "\\$quit_time\\_____ ";
            $line = "$nb_h_pre_computed (h)";

            // Compute periods in the day:
            $nb_hours_listed = 0;
            forEach ($lines2add as $record2add) {
                unset($hours, $comments, $task_id);

                extract($record2add); // get: $hours, $comments, $task_id, ...
                $nb_hours_listed+= isSet($hours)?$hours:0;
            }
            $diff = $nb_h_pre_computed - $nb_hours_listed - $lunch_nb_h;
            $line.= " (nbHListed=$nb_hours_listed h ; ".
                "lunch=$lunch_nb_h h ; diff=$diff h)";

            // TODO: Compute redmine records:

            echo $prefix, $line, $nl;
            continue;
        }
    }

    $first_char = subStr($line, 0, 1);
    if ('_'===$first_char) {
        $add2redmine = false;
        $line = subStr($line, 1);
    } else {
        $add2redmine = true;
    }

    if (is_numeric(subStr($line, 0, 1))) {
        $hours = subStr($line, 0, 3);
        $prefix.= "[$hours h] ";
        $line = subStr($line, 5);
    } elseIf ('- '!==subStr($line, 0, 2)) {
        $lines2add[$nb_lines_in_day - 1]['comments'].= $line;
        continue;
    }

    $words = explode(' ', $line);
    if (!is_numeric($words[1])) {
        $section = $words[1]; // section can be a project or a meta-task
        $prefix.= "{ $section } ";
        $line = str_replace("- $section : ", '', $line);
        $line = trim($line);
    }

    // Parse the remain parts of the line:
    if (isSet($task_id)) {
        unset($task_id);
    }
    if (preg_match('|(\d+\.\d+)h\s*-\s+(.+)\s*#(\d+)|', $line, $matches)) {
        list($hours, $comments, $task_id) = $matches;
    } elseIf (preg_match('|(\d+\.\d+)h\s*-\s+(.+)|', $line, $matches)) {
        list($hours, $comments) = $matches;
    } elseIf (preg_match('|\s*-\s+(.+)|', $line, $matches)) {
        list($comments) = $matches;
    } elseIf (isSet($section)) {
        $comments = $line;
    } else {
        echo $prefix, $line, $nl;
        die("Missing data for previous line!$nl");
    }

    $lines2add[$nb_lines_in_day] = array(
        'hours' => $hours,
        'comments' => $comments,
    );
    if (isSet($task_id)) {
        $lines2add[$nb_lines_in_day]['task_id'] = $task_id;
    }

    echo $prefix, $line, $nl;

    // Compute redmine record:
    // TODO

    ++$nb_lines_in_day;
    ++$nb_data_lines;
}

if (!feof($fd)) {
    echo "Error: fgets() failed!", $nl;
}
@fclose($fd);

// Print some stats:
printf(
    '---%s%d lines ignored (%d empty) / %d lines read (%d with a data).%s',
    $nl, $nb_ignored_lines, $nb_empty_lines, $nb_read_lines, $nb_data_lines, $nl
);

$s = $nb_lines_with_date>1?'s':'';
$time_lap = round((microtime(true) - $start_tic)*1000);
printf(
    '%d lunch line%s, %d date%s found in ~%dms.%s',
    $nb_lunch_lines, $s, $nb_lines_with_date, $s, $time_lap, $nl
);

if (!isSet($argv)) { // if called from the web
    is_readable('Include/tail.php') && include_once 'Include/tail.php';
    exit(0);
}

// Vim editing preferences (phpcs compliant):
// vim: tabstop=4 shiftwidth=4 expandtab
