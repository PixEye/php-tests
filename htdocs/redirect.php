<?php
/**
 * Created on: 2011-04-06 by Julien Moreau
 *
 * Last commit:
 * $Id$
 *
 * @filesource
 */

if (FALSE===stripos($_SERVER['HTTP_HOST'], 'mydomain'))
	$new_addr = '/';
else
	$new_addr = 'http://www.example.com/';

if (!headers_sent())
	header("Location: $new_addr", TRUE, 301); // 301=Moved Permanently
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <meta http-equiv="refresh" content="0; URL=<?php echo $new_addr ?>" />
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <title>Redirection</title>
 </head>
 <body>
  <h1>Address has changed permanently!</h1>
  <p>Please go <a href="<?php echo $new_addr ?>">there</a> &amp; update your bookmark.</p>
 </body>
</html>
