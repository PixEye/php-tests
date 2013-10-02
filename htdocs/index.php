<?php
/**
 * Web File Viewer	Copy-right 2002 under the GPL licence.
 *
 * By: Julien MOREAU	<PixEye at pixeye dot net>
 *
 * Last commit of this file: $Id$
 * Local time: $Date: 2012-10-22 12:13:33 +0200 (lun. 22 oct. 2012) $
 *
 * This is PHP (made for PHP 3 at first but works with PHP 4 & PHP 5)
 *
 * See http://web-file-viewer.sourceforge.net/ for details.
 *
 * Possible request parameters (only one at once):
 *	d:	the path of the current directory
 *	f:	the path of the file to display or download
 *	t:	the path of an image file to display the associated thumbnail picture
 *
 * Data you can adapt:
 */
# error_reporting(-1);	# Report all PHP errors
$max_col_number = 4;	# Maximum number of columns in the table

// Icon path directory (without trailing /):
#$icP = 'http://pixeye.online.fr/.Icons';
$icP = '/.Icons';

// Default charset for the web server:
#$charset = 'iso-8859-15';
$charset = 'UTF-8';

if (function_exists('date_default_timezone_set'))
	date_default_timezone_set('Europe/Paris');

/* Colors: purple bg (wheat = #f7dfb5):

$Color = array('text' => '#fff', 'bgcolor' => '#504050',
	'link' => '#80BBFF', 'vlink' => '#f7dfb5', 'alink' => '#fff',
	'shift' => '#403040', 'box' => '#fff', 'highlight' => '#fff',
	'em' => '#504050', 'low' => '#D0D0D0');

// Colors: orange bg:
$Color = array('text' => '#000', 'bgcolor' => '#fb8',
	'link' => '#c13', 'vlink' => '#000', 'alink' => '#fff',
	'shift' => '#c13', 'box' => '#c13', 'highlight' => '#fff',
	'em' => '#000', 'low' => '#eee'); */

// Colors: light grey bg:
$Color = array(	'text' => '#000', 'bgcolor' => '#eee',
		'link' => '#000', 'vlink' => '#777', 'alink' => '#000',
		'shift' => '#aaa', 'box' => '#777', 'highlight' => '#fff',
		'em' => '#000', 'low' => '#eee');

$max_width = 160;		# Maximum width for thumbnails (in pixels)
$max_height = 120;	# Maximum height for thumbnails (in pixels)
$max_x2display = 800;			# Maximum image width to display (in pixels)
$max_size2display = 200;	# Maximum file size to display (in kBytes)
$min_height2target = 350;	# Minimum height for pictures to set a anchor

# Filenames filters (case insensitive regular expresions to exclude):
$filter = array('^\.', '\.ico$', 'css$', '^index\.', '^CVS$', '^Thumbs\.db$');

$confile = '.wfvrc.php';		# Specific directory configuration file (optional)
$globconf = '.wfvgrc.php';	# Global configuration filename

$global_intro = '.global-intro.html';	# Introduction HTML file
$dir_intro = '.dir-intro.html';		# Introduction HTML file
$thumbdir = '.thumbnails';	# Thumbnails directory
#$refresh_delay = 60;		# Number of seconds to wait before refresh

/******** End of what users can change (use configuration files as well) ******/

if (array_key_exists('t', $_REQUEST)) {
	$_REQUEST['t'] = stripSlashes($_REQUEST['t']);
	if (function_exists('exif_thumbnail')) {
		$image = @exif_thumbnail($_REQUEST['t'], $width, $height, $type);
		if ($image!==false && !headers_sent()) {
			header('Content-type: ' .image_type_to_mime_type($type));
			echo $image; exit;
		} else die('No thumbnail available in this picture.');
	} else die('PHP version too old!');
}
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>
<?php

/**
 * Because it does not exist in PHP3:
 */
function inArray($x, $arr, $strict=false) {
	if (function_exists('in_array')) return in_array($x, $arr, $strict);
	forEach($arr as $e)
		if (($strict && $e===$x) || $e==$x)
			return true;
	return false;
}

/**
 * For a better presentation of errors:
 */
function error ($msg) {
	echo "<div class=\"error\">$msg\n\tPlease, go back.</div>\n\n";
	#echo "</body>\n</html>"; exit;
}

/**
 * For a better presentation of warnings:
 */
function warning ($msg) {
	echo "<div class=\"warning\">$msg</div>\n\n";
}

/**
 * Display information in DEBUG mode:
 */
function comment ($debug_msg, $indent_string='') {
	global $DEBUG;

	if ($DEBUG) echo "$indent_string<!-- $debug_msg -->\n";
}

/**
 * Display variable content in DEBUG mode:
 */
function dispVar ($var_name) {
	global $DEBUG, $$var_name;

	comment("dispVar(): $var_name = '".$$var_name."'");
}

if (!isset($REQUEST_URI)) forEach($_SERVER as $key => $val)
	if (!is_numeric($key)) $$key = $val;
$DEBUG = ($SERVER_NAME=='127.0.0.1');		# Debug mode

dispVar('SERVER_NAME');
dispVar('REQUEST_URI');
$dir_name = $REQUEST_URI;
$dir_name = preg_replace('|\?.*$|', '', $dir_name);		# Hide parameters
dispVar('dir_name');
$dir_name = preg_replace('|^.*://[^/]*/|', '', $dir_name);	# Hide domain name
dispVar('dir_name');
#$dir_name = preg_replace('|/$|', '', $dir_name);
$dir_name = preg_replace('|/[^/]*$|', '', $dir_name);
dispVar('dir_name');
if (!isset($_REQUEST['d'])) $_REQUEST['d'] = '';
$d = stripSlashes($_REQUEST['d']);
if (isset($_REQUEST['f']))
{
	$f = stripSlashes($_REQUEST['f']);
	if (!preg_match('|\/|', $f)) $d = '';
	else $d = preg_replace('|\/[^\/]*$|', '', $f);
}
dispVar('d');
dispVar('PHP_SELF');

$this_file = preg_replace('|^.*/|', '', $PHP_SELF);
dispVar('this_file');

$h1_title = '/'.basename($dir_name);
dispVar('h1_title');
if ($h1_title=='/') $h1_title = "/$HTTP_HOST";
if (!$d) $d = '.'; else { $dir_name.="/$d"; $h1_title.="/$d"; }
$h1_title = strTr($h1_title, '_', ' ');
$h1_title = htmlEntities($h1_title, ENT_NOQUOTES, $charset);
$page_title = basename($h1_title);
dispVar('page_title');
$h1_title = str_replace('/', ' &gt;&nbsp;', $h1_title);

// Read the configuration file:
//	- the global one:
if (is_readable($globconf)) {
	include $globconf;
	comment("Global configuration file: \"$globconf\" read");
}
//	- the directory specific one:
$confile = "$d/$confile";
if (is_readable($confile)) {
	include $confile;
	comment("Configuration file: \"$confile\" read");
}
$nbfilters = count($filter);
if ($DEBUG) {
	for($j = 0; $j<$nbfilters; $j++)
		echo "<!-- filter[$j] = ".$filter[$j]." -->\n";	# Debug display
}

# Supported file extensions (in lowercase here ; uppercase are also supported):
$Ext = array(
	'text'	=> array('txt', 'sh', 'csv', 'conf', 'cfg', 'ini', 'sql', 'btab'),
	'img'	=> array('jpg', 'jpeg', 'gif', 'png')
);

if ($d!='.') $page_title = htmlEntities(strtr(basename($d), '_', ' '), ENT_NOQUOTES, $charset);
dispVar('page_title');

if (!isSet($page_title)) $page_title = $HTTP_HOST;
if (!isSet($h1_title)) $h1_title = $HTTP_HOST;

// Security check:
if (preg_match('|\.\.|', $d) || preg_match('|^/|', $d)) {
	$alert = 1;
	$page_title = "$d Access denied!";
	$h1_title = 'Unauthorized access detected!';
} else $alert = 0;

dispVar('h1_title');

// Set colors (example: textCol = 'White'):
reset($Color);
while(list($type, $color) = each($Color)) ${$type.'Col'} = $color;
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset?>"/>
    <title><?php echo $page_title?></title>
    <link rel="Shortcut Icon" href="/favicon.ico"/>
    <meta http-equiv="Version" content="$Revision$"/>
<?php
	if (!isset($refresh_delay)) $refresh_delay = 0;
	if ($refresh_delay>0)
	echo "  <meta http-equiv=Refresh content=\"$refresh_delay\"/>\n";
	// Here is the style sheet where you can change the decoration:
?>
    <style type="text/css">
      <!--
	body { background:<?php echo $bgcolorCol?>; color:<?php echo $textCol?>; margin:0; }
	p, div, th, td { font-family:sans-serif; font-size:10px; }

	h1 { background:<?php echo $boxCol?>; color:<?php echo $bgcolorCol?>;
	  padding:5px; font-family:serif; margin:5px 0 0 0; }
	.EndBar { background:<?php echo $shiftCol?>; color:<?php echo $lowCol?>;
	  padding:3px 10px 0 10px; margin:0; vertical-align:middle; }

	img { border:0; }
	a { text-decoration:none; font-weight:bold; }
	a:link { background:transparent; color:<?php echo $linkCol?>; }
	a:visited { background:transparent; color:<?php echo $vlinkCol?>; }
	a:hover { background:<?php echo $shiftCol?>; color:<?php echo $alinkCol?>; }
	.EndBar a:link { background:transparent; color:<?php echo $vlinkCol?>; }
	.EndBar a:visited { background:transparent; color:<?php echo $vlinkCol?>; }
	.EndBar a:hover { background:<?php echo $shiftCol?>; color:<?php echo $alinkCol?>; }

	.center { text-align:center; }
	.right { text-align:right; }
	.w30p { width:30%; }
	.w40p { width:40%; text-align:center; }
	.margin, H2, HR, P, PRE { margin:0 20px; }
	.warning { margin-top:20px; text-align:center;
	  padding-left:3px; padding-right:3px; font-weight:bold; font-size:10px; }
	.error { background:#800000; color:White; margin-top:20px;
	  text-align:center; padding:0 3px; font-weight:bold; font-size:12px; }

	/* rv stands for Reverse Video: */
	strong, .rv { background:<?php echo $highlightCol?>; color:<?php echo $emCol?>;
	  padding:0 3px; font-weight:bold; }
      -->
    </style>
  </head>
  <body>
    <h1><?php echo $h1_title?></h1>

<?php
// Security check:
if ($alert) error("You are NOT allowed to access \"$d\" directory!");

// Icon extension:
if (!isset($_SERVER['HTTP_USER_AGENT'])) $_SERVER['HTTP_USER_AGENT'] = '';
$user_agent = $_SERVER['HTTP_USER_AGENT'];
if (strstr($user_agent, 'MSIE') // Si MSIE < v7 alors GIF sinon PNG
	&& preg_replace('/^.*MSIE (\d+).*$/i', '$1', $user_agent)<7)
		$icE = 'gif'; else $icE = 'png';
if ($DEBUG && strstr($user_agent, 'MSIE')) {
	$v = preg_replace('/^.*MSIE (\d+).*$/i', '$1', $user_agent);
	echo "<!--\n user_agent = '$user_agent'\n MSIE v'$v'\n => icE = '$icE' -->\n";
}
?>
    <table width="95%" class="margin">	<!-- Begin of top table -->
      <tr>
	<td class="w30p">
<?php
# The home link:
$homeLink = "http://$HTTP_HOST";
if ($SERVER_PORT!=80) $homeLink.=":$SERVER_PORT";
$homeLink.=preg_replace('|(index\.php(3)?)?\?.*$|', '', $REQUEST_URI);

# The parent directory link:
if (!$alert && $d!='.') {
	if (preg_match('|\/|', $d)) $parentLink = '?d='.
		urlencode(preg_replace('|\/[^\/]*$|', '', $d));
	else {
		$parentLink = $homeLink;
		#$parentLink=preg_replace('|index\.php.*$|', '', $parentLink);
		#$parentLink='?d=.';
	}
	echo "\t<a href=\"$parentLink\">&lt;= Parent directory</a>";
	echo "&nbsp;|\n";
	echo "\t<a href=\"$homeLink\">Home</a>";
} else echo "\t&nbsp;";
?></td>
	<td class="w40p">Directory revision date: <span class="rv"><?php
		$stat_id = stat($d);	# [9] = [10]
		echo date('Y/m/d', $stat_id[9]).'</span> ';
		echo date('G:i:s T.', $stat_id[9]);
	?></td>
	<td class="w30p"><div class="right">
	<?php
	if ($refresh_delay>0) {
		$unit = 'second';
		if ($refresh_delay>=3600 && $refresh_delay%3600==0) {
			$refresh_delay/=3600; $unit = 'hour';
		} elseIf ($refresh_delay>=60 && $refresh_delay%60==0) {
			$refresh_delay/=60; $unit = 'minute';
		}
		if ($refresh_delay>1) $unit.='s'; else $refresh_delay = '';
		echo "(&nbsp;Auto refresh every $refresh_delay $unit&nbsp;)";
	} else	echo '&nbsp;';
	?></div></td>
      </tr>
    </table>				<!-- End of top table -->

    <div class="center"><a id="t"></a>
<?php  # The main target file (big image or text file):
  if (!$alert && isSet($f)) {
	$f = stripSlashes($f);
	$name = preg_replace('|.*\/|', '', $f);
	$file = $f; $basename = basename($f);
	$ext = preg_replace('|^.*\.([^\.]*)$|', "\\1", $basename);
	$sufx = preg_replace('|\.[^\.\/]*$|', '', $basename);

	# Get the file size:
	$stats = stat($file); $byte_file_size = $stats[7]; $unit = 'B';
	$file_size = $byte_file_size;
	if ($file_size<0) $file_size+= 2.0 * PHP_INT_MAX; // fix for files larger than 2GB
	if ($file_size>1024) {
		$file_size = (int)($file_size/1024);
		$unit = 'k';
	}

	$extl = strToLower($ext);
	if (inArray($extl, $Ext['img'])) {
		# Get the images width & height:
		$size = @getImageSize($file);
		if (is_array($size)) {
			$width = $size[0]; $height = $size[1];
			$reduce = ' '.$size[3]; $type = $size[2];

			$comment = $width."x$height $file_size$unit";

			# Retreive EXIF information if available (JPEG only):
			$exif_data = '';
			if ($type==2 && function_exists('read_exif_data')) {
				$exif = @read_exif_data($file);
				$exif_data = '/ ';
				$EmData = array('ApertureValue', 'ExposureTime', 'FocalLength', 'Make',
					'DateTime', 'Model', 'ExifImageWidth', 'ExifImageLength', 'ISOSpeedRatings',
					'ShutterSpeedValue');
				while(list($k, $v) = each($exif)) {
						if (is_array($v)) $v = implode('&nbsp;; ', $v);
						else $v = str_replace(' ', '&nbsp;', $v);
						$addon = "$k:&nbsp;$v&nbsp;/\n    ";
						forEach($EmData as $emData)
							if ($k==$emData)
								{ $addon = "<b>$addon</b>"; break; }
						$addon = preg_replace('|f/([0-9.]+)|', '<b>f/\\1</b>', $addon);
						$v = trim($v);
						if ($v!='' && $k!='FileName' && $k!='ModeArray' &&	// Filter
								$k!='ImageInfo' && strpos($k, 'UndefinedTag')===false &&
								ctype_print($v))
							$exif_data.=$addon;
				}
			}

			$alt = preg_replace('|[-_]|', ' ', $sufx);
			$title = "$name ($comment)";
			$f = str_replace(' ', '%20', $f);
			$f = str_replace('&', '&amp;', $f);

			echo "  <img alt=\"$alt\"\n    src=\"$f\"\n";
			echo '    style="border:outset 1px #BBB"';
			echo "    title=\"$title\" width=\"$width\" height=\"$height\"/><br/>\n";
		} else $title = 'Image does not exist!';
		echo "  $title<br/>\n";
		echo "  $exif_data<br/>\n  <br/>\n";
	} elseIf (inArray($extl, $Ext['text'])) {
		echo "</div>\n";
		$fcontent = join( '', file( $file ) );

		if (1 || preg_match('|<html|i', $fcontent) || preg_match('|#|', $fcontent)
			|| preg_match('|---|', $fcontent) || preg_match("|;\n|", $fcontent)) {
			# Program file, do not change it but use fix font
			$align = 'Left';
			$fcontent = htmlEntities($fcontent, ENT_NOQUOTES, $charset);
			$fcontent = "<pre>$fcontent</pre>";
		} else {
			// Text plain file
			$align = 'justify';
			$fcontent = preg_replace('|<([^/aubi])|i', '&lt;\\1', $fcontent);
			$fcontent = htmlEntities($fcontent, ENT_NOQUOTES, $charset);
			$fcontent = preg_replace("/\n/", "<br/>\n", $fcontent);
			$fcontent = preg_replace('/([">]) /', '\\1&nbsp;', $fcontent);
			$fcontent = preg_replace('/ ([":;!])/', '&nbsp;\\1', $fcontent);
			$fcontent = "<p align=\"$align\">\n$fcontent</p>";
		}
?>
<p class="right">[&nbsp;<a href="<?php echo $file?>"
  >Show/Download this file as plain text</a>&nbsp;]</p>

<?php
		echo "$fcontent\n\n<hr width=\"30%\"/>\n\n";
		echo "<div class=\"center\">\n";
	} else {
?>
<p class="center">[&nbsp;<a href="<?php echo $file?>"
	>Download <?php echo $basename?></a>&nbsp;]</p>
<?php
	}
  }
  # End of the main target file section

  # Insert the global introduction file if it exists:
  if (isSet($global_intro) && file_exists($global_intro)) {
	echo "      <!-- Begin of global intro file -->\n";
	readfile($global_intro);
	echo "      <!-- End of global intro file -->\n\n";
  }

  # Insert the introduction file if it exists in the current directory:
  $dir_intro = "$d/$dir_intro";
  if (isSet($dir_intro) && file_exists($dir_intro)) {
	echo "      <!-- Begin of intro file of current directory -->\n";
	readfile($dir_intro);
	echo "      <!-- End of intro file of current directory -->\n\n";
  }
?>
      <table width="99%"> <!-- Main table: -->
<?php
  for($i = 0; $i<$max_col_number; $i++) echo "\t<colgroup width=\"1*\"/>\n";
  $tr_closed = 1;

  // Read filenames to sort them first:
  dispVar('alert');
  dispVar('d');
  if (!$alert && $dir = @opendir($d))
  {
	$nb_td = 0;
	$Files = array();
	$Dirs = array();
	while($file = readdir($dir))
	{
		if (is_dir("$d/$file"))
			$Dirs[] = $file;
		else	$Files[] = $file;
	}
	closedir($dir);

	if (function_exists('natcasesort'))
	{
		natCaseSort($Files);
		natCaseSort($Dirs);
	} else {
		sort($Files);
		sort($Dirs);
	}
	$Files = array_merge($Dirs, $Files); // directories first

	# And then display them:
	$L = count($Files);
	forEach($Files as $i => $name)
	{
		$basename = $name;
		if ($d=='.') $file = $name; else $file = "$d/$name";
		$name = htmlEntities($name, ENT_NOQUOTES, $charset);
		dispVar('file');
		$isDir = is_dir($file);

		# Is this file readable?
		if (!is_readable($file)) continue;
		$perms = filePerms($file); // 33188 = readable | 33184 = not readable
		dispVar('perms');
		comment("File $i/$L: \"$basename\", mod=$perms", "\n\t");
		if ($perms==33184 || $perms==33152) continue;

		for($j = 0; $j<$nbfilters; $j++) {
			comment("Filter $j/$nbfilters: ".$filter[$j]." sur $basename");
			if (preg_match('|'.$filter[$j].'|i', $basename)) continue 2;
		}
		#comment('After filtering', "\t");

		$ext = ''; $sep = '.';
		$link = urlencode($file);
		$link = str_replace('%2F', '/', $link);
		$link = str_replace('+', '%20', $link);
		$link = str_replace('&', '&amp;', $link);
		if ($isDir) {	# Directory case:
			$link = "?d=$link"; $file = "$file/"; $sep='/';
			$byte_file_size = 0;
		} else {		# Simple file case:
			if (preg_match('|\.|', $basename)) {
				 $ext = preg_replace('|^.*\.([^\.]*)$|', '\\1', $basename);
				$name = preg_replace('|^(.*)\.[^\.]*$|', '\\1', $basename);
			}

			# Get the file size:
			$stats = stat($file); $byte_file_size = $stats[7]; $unit = 'B';
			$file_size = $byte_file_size;
			if ($file_size<0) $file_size+= 2.0 * PHP_INT_MAX; // fix for files larger than 2GB
			if ($file_size>1024) { $file_size = round($file_size/1024, 1); $unit = 'k'; }
			if ($file_size>1024) { $file_size = round($file_size/1024, 1); $unit = 'M'; }
			if ($file_size>1024) { $file_size = round($file_size/1024, 1); $unit = 'G'; }
		}

		# If we need a new line:
		if (!$nb_td) { echo '	<tr>'; $tr_closed = 0; } else echo '  ';
		echo '<td class="center"><br/>';

		$extl = strtolower($ext);
		if (inArray($extl, $Ext['text'])) $link = "?f=$link#t";

		if ($isDir)
			$name = "<img\n\t\tsrc=\"$icP/folder.$icE".
				'" alt="" title=""/>'.
				"<br/>\n\t\t$name";

		# Is this a showable image file?
		if ($ext=='url') {
			$fid = fopen($file, 'r');
			$link = fgetss($fid, 512);
			fclose($fid);

			$link = trim($link);
			$link = preg_replace('@&([^a])@', '&amp;\1', $link);
			echo "<a target=\"$name\"\n\thref=\"$link\">";
			/* $name = "<img\n\t\tsrc=\"$icP/link.$icE".
				'" alt="[External link]" '.
				'title="External link"/>'.
				"<br/>\n\t\t$name"; */
		} elseIf (inArray($extl, $Ext['img'])) {
			$tlink = "?t=$link";
			$link = str_replace(' ', '%20', $link);
			$link = "?f=$link";

			# Get the images width & height:
			if ($byte_file_size) {
				$size = getImageSize($file);
				$width = $size[0]; $height = $size[1];
				$reduce = ' '.$size[3]; #$type = $size[2];
				$comment = $width."x$height";
			} else $comment = '<b>File empty!</b>';

			if ($height>$min_height2target) $link.='#t';

			$title = $comment;

			#$alt = 'Loading...';
			$sufx = preg_replace('|\.[^\.\/]*$|', '', $basename);
			$alt = preg_replace('|[-_]|', ' ', $sufx);

			# Get the thumbnail if it exists:
			$thumb = "$d/$thumbdir/$basename";

			#dispVar('reduce');
			# If image file is empty or too big:
			if (is_readable($thumb)) {
				comment('Use file thumbnail.');
				if ($width>$max_x2display) $link = $file;
				$file = $thumb; $reduce = '';
			} elseIf ($byte_file_size && ($extl=='jpg' || $ext=='jpeg') &&
					function_exists('exif_thumbnail') &&
					@exif_thumbnail($file, $w, $h, $t)!==false) {
				comment('Use EXIF thumbnail.');
				if ($width>$max_x2display) $link = $file;
				$file = $tlink; $reduce = '';
			} elseIf ($byte_file_size && $width<=$max_x2display &&
					($byte_file_size/1024)<$max_size2display) {
				comment('Reduce the picture size with HTML...');
				if ($width>$max_x2display) $link = $file;
				#$file = $link;
				$reduce = '';
			} else {
				comment('Thumbnail not available.');
				$file = str_replace(' ', '%20', $file);
				if ($byte_file_size) echo "<a\n\t  href=\"$file\">";
				echo $name;
				if ($byte_file_size) echo '</a>';
				echo "$sep$ext<br/>\n";
				echo "\t  ($comment&nbsp;- $file_size&nbsp;$unit)</td>";

				if (++$nb_td>=$max_col_number) {
					# End of line:
					echo "</tr>\n"; $tr_closed = 1; $nb_td = 0;
				} else echo "\n\t";
				continue;
			}

			if (!is_readable($thumb) &&
				($width>$max_width || $height>$max_height)) {
				if ($width-$max_width<$height-$max_height)
					$reduce = " height=\"$max_height\"";
				else
					$reduce = " width=\"$max_width\"";
			}

			$link = str_replace(' ', '%20', $link);
			$link = str_replace('&', '&amp;', $link);
			$file = str_replace(' ', '%20', $file);
			echo "($comment)<br/>\n";
			echo "\t  <a href=\"$link\"><img alt=\"$alt\"\n";
			echo "\t  style=\"border:outset 1px #BBB\" src=\"$file\"\n";
			echo "\t  title=\"\"$reduce/><br/>";
		} else echo "<a href=\"$link\">";

		echo "$name</a>";
		if ($isDir || isSet($ext)) echo "$sep$ext";
		if (!$isDir) echo "<br/>($file_size$unit)";

		echo '<br/></td>';

		if (++$nb_td>=$max_col_number) {
			echo "</tr>\n"; $tr_closed = 1; $nb_td = 0;
		} else	echo "\n\t";
	}
	if (!$tr_closed) { echo "</tr>\n"; $tr_closed = 1; }
  } elseIf (!$dir) error("\"$d\" directory cannot be read!");
?>
      </table>
      <br/>
    </div>

    <table cellspacing="0" width="100%" class="EndBar">	<!-- Footer: -->
      <tr class="box">
	<td>
	  <a href="http://sourceforge.net/projects/web-file-viewer/"><img
	    src="<?php echo $icP?>/WFV_powered.png" width="57" height="20"
	    alt="Powered by Web File Viewer" title="Powered by Web File Viewer"
	    /></a>
	  <a href="http://validator.w3.org/check/referer"><img height="20"
	    src="<?php echo $icP?>/v-xhtml-11.<?php echo $icE?>" width="57"
	    alt="W3C XHTML 1.1 certified" title="W3C: Valid XHTML 1.1!"/></a>
	  <a href="http://jigsaw.w3.org/css-validator/"><img
	    width="57" height="20" alt="W3C CSS valid" title="W3C: Valid CSS!"
	    src="<?php echo $icP?>/vcss.<?php echo $icE?>"/></a>
	</td>
	<td class="right"><a title="Web File Viewer"
	  href="http://web-file-viewer.sourceforge.net/">WFV</a>
	  revision date: <?php $stat_id = stat($this_file);
	  echo date('Y/m/d G:i:s T.', $stat_id[9]); 	# [9] = [10]
      ?></td></tr>
    </table>
  </body>
</html>
<?php // Vim editing preferences:
# vim: tabstop=8 shiftwidth=8 noet
?>
