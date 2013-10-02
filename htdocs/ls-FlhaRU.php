<?php
/**
 * PHP equivalent to the shell command line:
 *  /bin/ls -FlhaU --time-style=long-iso /tmp
 *
 * @author Julien Moreau (aka PixEye)
 * @since  2009-09-08
 */

/**
 * some global data:
 */
$restricted = 'restricted'; // keyword to mean that a directory has a restricted access
$start_path = '/tmp'; // start path (to be browsed if it is a directory)
$recursive = TRUE;  // recursive mode (or not)

setLocale(LC_ALL, 'fr_FR'); // for printf('%f ...

/**
 * Convert a byte size into a human readable amount using the units: K, M, G & T
 *
 * @param int the size in bytes
 */
function to_human_size($size_in_bytes, $html_output = TRUE)
{
	if (!is_numeric($size_in_bytes))
		throw new exception("The size '$size_in_bytes' is not a number!");

	$size_in_bytes+= 0; // cast string to number

	if (!$size_in_bytes) return '0';
	if ($size_in_bytes<0) $size_in_bytes+= pow(2, PHP_INT_SIZE*8); // fix for files > 2GB

	/**
	 * @see http://fr.php.net/manual/fr/function.number-format.php
	 */
	$Units = array('', 'K', 'M', 'G', 'T');
	$unit = floor(log($size_in_bytes, 2) / 10);
	#print('<!-- '.__FUNCTION__."($size_in_bytes) unit='$unit' -->");
	return sprintf('%01.1f%s',
		$size_in_bytes/pow(1024, $unit),
		$Units[$unit]);
}

/**
 * Display a file in a formated line
 *
 * @param string the full path name of the file
 * @param string a name used for display purpose only
 */
function display_file($full_path, $display_name)
{
	global $argv;
	$mode = 0;

	// optionnal part:
	$Stats = @stat($full_path);

	if (false===$Stats)
	{
		echo $display_name, PHP_EOL;
		return ;
	}

	extract($Stats);
	/* Get: $dev, $ino, $mode, $nlink, $uid, $gid, $rdev,
		$size, $atime, $mtime, $ctime, $blksize, $blocks */

	/**
	 * @see http://www.php.net/manual/en/function.fileperms.php
	 */
	// file type:
	if (($mode & 0xC000) == 0xC000) {
		$fType = 's'; // Socket
	} elseif (($mode & 0xA000) == 0xA000) {
		$fType = 'l'; // Symbolic Link
	} elseif (($mode & 0x8000) == 0x8000) {
		$fType = '-'; // Regular
	} elseif (($mode & 0x6000) == 0x6000) {
		$fType = 'b'; // Block special
	} elseif (($mode & 0x4000) == 0x4000) {
		$fType = 'd'; // Directory
	} elseif (($mode & 0x2000) == 0x2000) {
		$fType = 'c'; // Character special
	} elseif (($mode & 0x1000) == 0x1000) {
		$fType = 'p'; // FIFO pipe
	} else {
		$fType = 'u'; // Unknown
	}
	// Owner rights:
	$llmod = (($mode & 0x0100) ? 'r' : '-');
	$llmod.= (($mode & 0x0080) ? 'w' : '-');
	$llmod.= (($mode & 0x0040) ?
		 (($mode & 0x0800) ? 's' : 'x' ) :
		 (($mode & 0x0800) ? 'S' : '-'));
	// Group rights:
	$llmod.= (($mode & 0x0020) ? 'r' : '-');
	$llmod.= (($mode & 0x0010) ? 'w' : '-');
	$llmod.= (($mode & 0x0008) ?
		 (($mode & 0x0400) ? 's' : 'x' ) :
		 (($mode & 0x0400) ? 'S' : '-'));
	// World rights:
	$llmod.= (($mode & 0x0004) ? 'r' : '-');
	$llmod.= (($mode & 0x0002) ? 'w' : '-');
	$llmod.= (($mode & 0x0001) ?
		 (($mode & 0x0200) ? 't' : 'x' ) :
		 (($mode & 0x0200) ? 'T' : '-'));

	/* optionnal block:
	ksort($Stats);
	forEach($Stats as $k => $v)
		if (is_numeric($k)) unset($Stats[$k]);
		else $Stats[$k] = "$k = '$v'";
	$stats = implode(', ', $Stats);
	printf("<!-- %s -->\n", $stats); */

	/* An example:
	 atime = '0', blksize = '1024', blocks = '4', ctime = '1252481136',
	 dev = '2054', gid = '1000', ino = '8177', mode = '16832',
	 mtime = '1252481136', nlink = '2', rdev = '0', size = '2048',
	 uid = '1000'
	 */

	/*
	printf('Owner of %s = ', __FILE__); print_r(posix_getpwuid(fileowner(__FILE__)));
	printf('Group of %s = ', __FILE__); print_r(posix_getgrgid(filegroup(__FILE__)));

	Owner of /home/jmoreau/web/Tests/example.php = Array (
		[name] => jmoreau
		[passwd] => x
		[uid] => 1000
		[gid] => 1000
		[gecos] => Julien Moreau
		[dir] => /home/jmoreau
		[shell] => /bin/bash
	)
	Group of /home/jmoreau/web/Tests/example.php = Array (
		[name] => mygroup
		[passwd] => x
		[members] => Array ()
		[gid] => 1000
	)
	*/

#	if ($fType!='-' && $fType!='d') $fType = "<b>$fType</b>";
	if ($fType=='-' or $fType=='d')
		$fType = "<span class=\"discreet\">$fType</span>";
	$Owner = posix_getpwuid($uid); $owner = $Owner['name']; unset($Owner);
	$Group = posix_getgrgid($gid); $group = $Group['name']; unset($Group);

	if (!isSet($mtime))
	{
		if (!isSet($ctime))
		{ echo $llmode, $display_name, PHP_EOL; return; }

		$mtime = $ctime;
	}

	$file_mtime = date('Y-m-d H:i', $mtime); // --time-style=long-iso option
	if (!isSet($size) or !is_numeric($size)) $size = fileSize($full_path);
	if (is_numeric($size)) $size = to_human_size($size); // -h option

	if (!isSet($argv))
		$file_mtime = "<span class=\"discreet\">$file_mtime</span>";

	$llInfo = sprintf('%s<span class="discreet">%s %2d %-8s %-8s</span> %6s %s',
		$fType, $llmod, $nlink, $owner, $group, $size, $file_mtime);

	if ($mtime!=$ctime) {
		$file_ctime = date('Y-m-d H:i', $ctime); // --time-style=long-iso
		$display_name.= " (c: $file_ctime)";
	}

	if (isSet($argv))
		printf("%s %s%s", strip_tags($llInfo), $display_name, PHP_EOL);
	else
		printf("%s %s\n", $llInfo, $display_name);
}

/**
 * Open the folder & recursive list files inside:
 *
 * @param string the directory to browse
 */
function list_files_in_dir($dir) {
	global $start_path, $recursive, $restricted;

	if (is_dir($dir)) {
		if ($dh = @opendir($dir)) {
			$full_path = $dir;
			$display_name = "$dir/";
			display_file($full_path, $display_name);
			if (!$recursive && $dir!=$start_path) return; // no recursive
			while (($file_name = readdir($dh)) !== false) {
				if ($file_name=='.' or $file_name=='..' or $file_name=='')
					continue;

				$full_path = "$dir/$file_name";
				$display_name = $full_path;
				if (is_dir($full_path)) {
					if ($recursive) list_files_in_dir($full_path); // recursive
				} else
					display_file($full_path, $display_name);
			}
			closedir($dh);
		} else { // unable to open the current directory
			$full_path = $dir;
			$display_name = "$dir/ <span class=\"discreet\">($restricted)</span>";
			display_file($full_path, $display_name);
		}
	} else {
		$full_path = $dir;
		$display_name = $dir;
		display_file($full_path, $display_name);
	}
	#	printf("<li>'%s' is not a directory!</li>\n", $dir);
}

if (!isSet($argv))
{
	include_once 'Include/head.php';
	print("<pre>\n");
} elseIf(isSet($argv[1])) $start_path = $argv[1];

list_files_in_dir($start_path);
if (isSet($argv)) exit(0);

print("</pre>\n");
?>
	<p><sup>*</sup> &laquo;&nbsp;<?php echo $restricted?>&nbsp;&raquo; means that
		there is a restricted access.</p>
<?php
if (!isSet($argv)) include_once 'Include/tail.php';
