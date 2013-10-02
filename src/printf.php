<?php
if (isSet($_REQUEST['argv']) && is_numeric($_REQUEST['argv']))
  $argv = $_REQUEST['argv'];

include_once 'Include/head.php';

$x = 0.1;
if (isSet($_SERVER['HTTP_HOST'])) print("\t<pre>");
printf("%%06d &gt; '%06d'\n", ++$x);
printf("%%06e &gt; '%06e'\n", ++$x);
printf("%%06f &gt; '%06f'\n", ++$x);
printf("%%06g &gt; '%06g'\n", ++$x);
printf("%%-6s &gt; '%-6s'\n", ++$x);
printf("%%6s  &gt; '%6s'",    ++$x);
if (isSet($_SERVER['HTTP_HOST'])) print("</pre>\n");

include_once 'Include/tail.php';
