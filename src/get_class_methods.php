<?php
include_once 'Include/head.php';

class Test {

  function getAttributeHolder() {
    return $this;
  }

  function getNames() {
    return Array('titi', 'toto', 'tutu');
  }

  function getUser() {
    return $this;
  }

  function logMessage($msg, $level) {
    printf("\t<p>%s - %s: <b>$msg</b></p>\n", date('c'), strtoupper($level));
  }

  function run() {
    $Attributes = Array('username');

    $Methods = get_class_methods($this->getUser()->getAttributeHolder()); sort($Methods);
    $this->logMessage('Methods for user->getAttributeHolder(): '.implode(', ', $Methods), 'debug');

    $Attributes = $this->getUser()->getAttributeHolder()->getNames();
    $this->logMessage('Attributes found: '.implode(', ', $Attributes), 'debug');

    printf("\t<p>That's all folks!</p>\n");
  }
}

$T = new Test;
$T->run();

include_once 'Include/tail.php';
?>
