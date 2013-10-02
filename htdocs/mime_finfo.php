#!/usr/bin/env php
<?php
/* Créé le 30/05/2008 par Julien Moreau (aka PixEye) */

header('Content-type: text/plain; charset="utf-8"');
$mime_file = '/etc/magic.mime';

$nb_param = count($argv)-1;
$cmd = basename($argv[0]);
if ($nb_param==0 or $argv[1]=='-h' or $argv[1]=='--help') {
  echo "$cmd: Détermine le type MIME des fichiers passés en paramètres.\n";
  echo "Usage : $cmd <file_1> [...]\n";
  exit(2);
}

$arg0 = array_shift($argv);
$ret = 1;   // code retour par défaut

/*
$finfo = finfo_open(FILEINFO_MIME); // Retourne le type MIME
foreach($argv as $fname)
    echo "$fname: ".finfo_file($finfo, $fname) . "\n";
finfo_close($finfo);
*/

if (class_exists('finfo')){
  $handle = new finfo(FILEINFO_MIME, $mime_file);
  if (!$handle) {
      echo "Échec de l'ouverture de la base de données fileinfo '$mime_file' !\n";
      exit(3);
  }
  foreach($argv as $fname) {
    if (!file_exists($fname)) {
      echo "File: '$fname' does not exists!\n";
    } elseif (!is_file($fname)) {
      echo "File: '$fname' is not a regular file!\n";
    } else {
      $mime_type = $handle->buffer(file_get_contents($fname));
      if ($mime_type=='application/octet-stream') {
        $tmp = mime_content_type($fname);
        #if (trim($tmp)!='') $mime_type = $tmp;
        if (trim($tmp)!='') $mime_type = "$tmp (was $mime_type)";
        unset($tmp);
      }
      echo "$fname: $mime_type\n";
      $ret = 0;
    }
  }
}

if ($ret>0) {
  echo "$cmd: Détermine le type MIME des fichiers passés en paramètres.\n";
  echo "Usage : $cmd <file_1> [...]\n";
}
exit($ret);
?>
