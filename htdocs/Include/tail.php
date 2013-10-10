<?php
/**
 * Created on the 2005-04-19 by Julien Moreau aka PixEye
 * Last commit of this file: $Id$
 */

if (isSet($argv)) return; // Do nothing if CLI
include_once 'head.php';
require_once 'functions.php';
?>
    </div>
<?php
$Php_resources = Array();
$real_mem = FALSE;
if (function_exists('memory_get_usage'))
	$Php_resources['mem_used'] =
		bytes2human(memory_get_usage($real_mem)).' used';

if (function_exists('memory_get_peak_usage'))
	$Php_resources['max_mem_used'] =
		bytes2human(memory_get_peak_usage($real_mem)).' max used';

if (isSet($_SERVER['REQUEST_TIME'])) {
	$time_lap = round((microtime(true) - $start_time)*1000);
	$Php_resources['time_lap'] = "~$time_lap ms";
}

if (count($Php_resources)) {
	$php_resources = implode(', ', $Php_resources); unset($Php_resources);
	echo "    <div class=\"discreet\" id=\"php_resources\">$php_resources.</div>\n";
}

if (!isSet($copyRight))
	$copyRight='&copy;&nbsp;2005-'.date('Y').
		' <a href="http://pixeye.net/">PixEye</a>.';
?>
    <div id="foot"><?php echo $copyRight?></div>
  </body>
</html>
