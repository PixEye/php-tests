<?php
// Report all PHP errors
error_reporting(-1);

// Set the charset & the time zone:
$charset = 'UTF-8';
setLocale(LC_TIME, 'Europe/Paris');
if (function_exists('date_default_timezone_set'))
	date_default_timezone_set('Europe/Paris');

// Compute time data:
$today=date('Ymd');
$begin=$today-1; $end=$today+2;
$dow=1;		// Day to start with (1 for Monday, 0 for Sunday)

$yearNow=date('Y');
$monthNow=date('n');
if (isSet($_REQUEST['lg'])) $lg = $_REQUEST['lg']; else $lg = 'fr';
if (isSet($_REQUEST['month'])) $month = $_REQUEST['month'];
else {
	if (isSet($_REQUEST['day'])) {
		$day=$_REQUEST['day'];
		$month = substr($day, 0, 6);
	} else $month=$yearNow*100+$monthNow;
}
if (!isSet($day)) $day = '';

$locale=$lg.'_'.strtoupper($lg);
setlocale (LC_ALL, $locale.'@euro', $locale, $lg);

$y2d=floor($month/100); $m2d=fmod($month, 100);

// Use the PEAR library:
@include_once 'Calendar/Month/Weekdays.php';
if (!class_exists('Calendar_Month_Weekdays'))
	die("Cannot find the PEAR library!\n");
$Month=new Calendar_Month_Weekdays($y2d, $m2d, $dow);
$Month->build();

// Previous & next buttons:
$m=$y2d*100+($m2d);
$tstamp1=mktime(0, 0, 0, $m2d, 1, $y2d);
if ($m2d==1) $previousM=($y2d-1)*100+12; else $previousM=$m-1;
if ($m2d==12) $nextM=($y2d+1)*100+1; else $nextM=$m+1;
if ($m2d>9) $addon=''; else $addon='0';
$previousY=($y2d-1).$addon.$m2d; $nextY=($y2d+1).$addon.$m2d;

if ($m2d==$monthNow && $y2d==$yearNow) $addon='success '; else $addon='';

$tstamp1=mktime(0, 0, 0, $m2d, 1, $y2d);
$month2d=ucfirst(strftime('%B', $tstamp1));
$cal_title=strftime('%B %Y', $tstamp1);
$h1_title='<a href="http://pear.php.net/package/Calendar">PEAR</a> calendar';
$title=$cal_title.' - '.strip_tags($h1_title);

include_once 'Include/head.php';
?>
		<h2 class="success center">Today is: <?php echo ucfirst(strftime('%A %e %B %Y'))?></h2>
		<h3 class="error"><?php echo $begin?> &lt;= selected &lt;= <?php echo $end?></h3>
		<h4>lg="<?php echo $lg?>", month=<?php echo $month?>, m2d=<?php echo $m2d?>, y2d=<?php echo $y2d?></h4>
    <div id="monthCalendar">
			<table summary="Calendar of <?php echo $month2d?> <?php echo $y2d?>" width="90%" class="bordered">
	<thead>
	  <tr>
			<th colspan="4" class="<?php echo $addon?>right">
				<a href="?month=<?php echo $previousM?>">&lt;</a>
				<?php echo $month2d?> <a href="?month=<?php echo $nextM?>">&gt;</a></th>
			<th colspan="3" class="<?php echo $addon?>left">
				<a href="?month=<?php echo $previousY?>">&lt;</a>
				<?php echo $y2d?> <a href="?month=<?php echo $nextY?>">&gt;</a></th>
	  </tr>
	  <!-- Week days: -->
	  <tr>
<?php for($i=1; $i<=7; $i++)
	    echo "\t    <th width=\"50\">".
		strftime('%A', mktime(0, 0, 0, 1, $i+$dow, 2006))."</th>\n";
?>
	  </tr>
	</thead>
	<tbody>
<?php
$m*=100;
while ($Day = $Month->fetch()) {
    if ($Day->isFirst()) echo "\t  <tr style=\"height:50px; vertical-align:middle\">\n";
    if ($Day->isEmpty()) {
	echo "\t    <td>&nbsp;</td>\n";
    } else {
	$num=$Day->thisDay();
	$ymd=$m+$num;
	    if ($ymd==$day) $addon=' wanted'; // wanted
	elseif ($ymd==$today) $addon=' success'; // today
	elseif ($ymd>=$begin && $ymd<=$end) $addon=' error'; // selected
	elseif ($dow==0 or $dow==6) $addon=' discreet'; // week-end
	else	$addon='';
	echo "\t    <td class=\"center bordered$addon\">$num</td>\n";
    }
    if ($Day->isLast()) echo "\t  </tr>\n";
    $dow=fmod(++$dow, 7);
}
?>
	</tbody>
      </table>
    </div>
<?php include_once 'Include/tail.php' ?>
