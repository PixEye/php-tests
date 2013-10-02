#!/usr/bin/env php
<?php
/* Créé le 26/05/2008 par Julien Moreau (aka PixEye) */

#include_once 'Include/head.php';
#include_once 'Include/tail.php';

header('Content-type: text/plain; charset="utf-8"');

$arg0 = array_shift($argv);
$cmd = basename($arg0);
echo "$cmd: Détermine le type MIME des fichiers passés en paramètres.\n";

foreach($argv as $arg)
  echo "$arg\t: '".mime_content_type($arg)."'\n";
?>
