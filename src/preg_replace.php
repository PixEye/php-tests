<?php
/**
 * Created on 2005-10-24 by Julien Moreau aka PixEye
 */

include_once 'Include/head.php';

// @link https://groups.google.com/forum/?fromgroups#!topic/symfony-devs/KEvOUflP0qs

// Example #1: multi-line text:
$i1lre = '|\s+|'; // in one line regExp
$text = "Hi,
  this originally is a
  multi-line text.";
$r = preg_replace($i1lre, ' ', $text);

if (isSet($argc)) echo "  $r", PHP_EOL; else
echo "  <pre>$r</pre>", PHP_EOL, PHP_EOL;

// Example #2: LDAP filters to hide a objectClass:
$filter = '(&(objectClass=person)(uid=jmoreau))';
$pattern = '/(objectClass)=(\{\w*\})?[^)]*/';
$r = preg_replace($pattern, '\1=\2...', $filter);

if (isSet($argc)) echo "  $r", PHP_EOL; else
echo "  <pre>$r</pre>", PHP_EOL, PHP_EOL;

// Example #3: email senders:
$EMailSender = 'Julien Moreau <jmoreau@example.com>';
if (isSet($argc)) echo "  EmailSender before: '", $EMailSender, "'", PHP_EOL; else
echo "  <p>EmailSender before: '", htmlEntities($EMailSender), "'.</p>\n";

$Match = array();
if (preg_match('/^.*<(.+)>$/', $EMailSender, $Match))
  $EMailSender = $Match[1];

if (isSet($argc)) echo "  EmailSender  after: '", $EMailSender, "'", PHP_EOL; else
echo "  <p>EmailSender after [<b>using preg_match()</b>]: '",
  htmlEntities($EMailSender), "'.</p>\n";

// Example #4: output of a router:
$text = 'terminal length 0
Premium-Slave-HSRP>show hardware
Cisco IOS Software, 1841 Software (C1841-ADVIPSERVICESK9-M), Version 12.4(3a), RELEASE SOFTWARE (fc2)
Technical Support: http://www.cisco.com/techsupport
Copyright (c) 1986-2005 by Cisco Systems, Inc.
Compiled Thu 29-Sep-05 19:12 by hqluong

ROM: System Bootstrap, Version 12.3(8r)T8, RELEASE SOFTWARE (fc1)

Premium-Slave-HSRP uptime is 6 days, 17 hours, 42 minutes
System returned to ROM by reload at 18:02:54 UTC Thu Jan 17 2013
System image file is "flash:c1841-advipservicesk9-mz.124-3a.bin"


This product contains cryptographic features and is subject to United
States and local country laws governing import, export, transfer and
use. Delivery of Cisco cryptographic products does not imply
third-party authority to import, export, distribute or use encryption.
Importers, exporters, distributors and users are responsible for
compliance with U.S. and local country laws. By using this product you
agree to comply with applicable laws and regulations. If you are unable
to comply with U.S. and local laws, return this product immediately.

A summary of U.S. laws governing Cisco cryptographic products may be found at:
http://www.cisco.com/wwl/export/crypto/tool/stqrg.html

If you require further assistance please contact us by sending email to
export@cisco.com.

Cisco 1841 (revision 5.0) with 118784K/12288K bytes of memory.
Processor board ID FCZ092622N0
6 FastEthernet interfaces
1 Virtual Private Network (VPN) Module
DRAM configuration is 64 bits wide with parity disabled.
191K bytes of NVRAM.
31360K bytes of ATA CompactFlash (Read/Write)

Configuration register is 0x2102

Premium-Slave-HSRP>';

$flags = 's';
$words = 'show\s+hardware';
$pregexp = '|.*\W'.$words.'\W+([\w-]+).*|'.$flags;
# $pregexp = '|.*\W'.$words.'[\s\W]+([\w-]+).*|'.$flags;
$r = preg_replace($pregexp, '$1', $text);

if (isSet($argc)) echo "  Manufacturer: $r", PHP_EOL; else
echo "  Manufacturer: <pre>$r</pre>", PHP_EOL, PHP_EOL;

// The end:
include_once 'Include/tail.php';
