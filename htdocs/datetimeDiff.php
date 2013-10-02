<?php
include_once 'Include/head.php';
if(!isSet($argv)) echo "\t<pre>";

$rounded = true;

echo "date('c') : ", var_export(date('c')), PHP_EOL;
/*
if(function_exists('date_default_timezone_get'))
  echo 'date_default_timezone_get() = ', var_export(date_default_timezone_get()), PHP_EOL;
if(ini_get('date.timezone'))
  echo 'date.timezone = ', var_export(ini_get('date.timezone')), PHP_EOL;
 */

function doPlural($nb, $str){ return $nb>1?$str.'s':$str; }

// see http://fr2.php.net/manual/fr/dateinterval.format.php
function format_interval($interval, $rounded=true)
{
	if(is_int($interval)) {
		# $interval = new DateInterval('PT'.$interval.'S'); // 3600s are not converted to hours :(
		$nb_sec = $interval;
		$d1 = new DateTime(); $d2 = new DateTime();
		$d2->add(new DateInterval('PT'.abs($nb_sec).'S'));
		$interval = $d2->diff($d1);
		$interval->invert = ($nb_sec<0)?1:0;
		unset($d1, $d2, $nb_sec);
	}

	$format = array();
	if($interval->y !== 0) {
		$format[] = '%y '.doPlural($interval->y, 'year');
	}
	if($interval->m !== 0) {
		$format[] = '%m '.doPlural($interval->m, 'month');
	}
	if($interval->d !== 0) {
		$format[] = '%d '.doPlural($interval->d, 'day');
	}
	if($interval->h !== 0) {
		$format[] = '%h '.doPlural($interval->h, 'hour');
	}
	if($interval->i !== 0) {
		$format[] = '%i '.doPlural($interval->i, 'minute');
	}
	if($interval->s !== 0) {
		if(!count($format)) {
			return 'less than a minute ago';
		} else {
			$format[] = '%s '.doPlural($interval->s, 'second');
		}
	}

	if(!$rounded && count($format)>1) {
		// We keep only the 2 biggest parts:
		$format = array_shift($format).' and '.array_shift($format);
	} elseIf(count($format)>1) {
		$format = 'more than '.array_shift($format);
	} else {
		$format = array_shift($format);
	}

	if($interval->invert)
		$format = "in $format";
	else
		$format = "$format ago";

	// Prepend 'since ' or whatever you like
	return $interval->format($format);
}

class Instant extends DateTime {
	public function __toString() { return $this->format('Y-m-d H:i:s P'); }
	public function toStamp() { return $this->getTimestamp(); }
	public function __invoke() { return $this->getTimestamp(); }
}

$datetime1 = new Instant('1978-05-20 17:00:00');
$datetime2 = new Instant('5000 seconds ago');	// 5000 seconds = 1 hour + 23 minutes + 20 seconds
#$datetime3 = new Instant('in 1 hour');		// does not work
$now = new Instant();
echo "Instant #1: '$datetime1'", PHP_EOL;
echo "Instant #2: '$datetime2'", PHP_EOL;
echo "Instant #3: -3600 seconds", PHP_EOL;

$interval = $datetime1->diff($now); // OOP PHP way; equivalent of date_diff() call
#echo $interval->format('%R%a '), doPlural($interval->d, 'day'), PHP_EOL;
// try { $interval = $datetime2-$datetime1; } catch(Exception $e) { die($e.PHP_EOL); } // Python style does not work in PHP
echo 'Age #1    : ', format_interval($interval, $rounded), PHP_EOL;

$interval = $now() - $datetime2(); // thanks to __invoke() :)
echo 'Age #2    : ', format_interval($interval, $rounded), PHP_EOL;

$interval = -3600;	# $now->toStamp() - $datetime3->toStamp();
echo 'Age #3    : ', format_interval($interval, false);

if(!isSet($argv)) echo '</pre>';
echo PHP_EOL;
include_once 'Include/tail.php';
