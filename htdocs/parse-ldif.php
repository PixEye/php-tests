#!/usr/bin/env php
<?php
/**
 * Read and parse an ldif-file into Net_LDAP_Entry objects
 * and print out the DNs. Store the entries for later use.
 */

#require 'Net/LDAP/LDIF.php';
require 'Net/LDAP2/LDIF.php';

if (!isSet($argc)) $argc = 1;
$nb_params = $argc-1;
if (1!=$nb_params or '-h'==$argv[1] or '--help'==$argv[1])
{
	$cmd = basename($argv[0]);
	fprintf(STDERR, "Usage: $cmd <LDIF file full path>%s", PHP_EOL);
	exit(1);
}

$input_filename = $argv[1];
if (!file_exists($input_filename))
{
	fprintf(STDERR, "File: '$input_filename' does not exist!%s", PHP_EOL);
	exit(2);
}

if (!is_readable($input_filename))
{
	fprintf(STDERR, "Cannot read file: '$input_filename'!%s", PHP_EOL);
	exit(3);
}

$entries = array();
$options = array('onerror' => 'die');
#$ldif = new Net_LDAP_LDIF($input_filename, 'r', $options);
#$ldif = new Net_LDAP2_LDIF($input_filename, 'r', $options);
#$ldif = new Net_LDAP_LDIF($input_filename);
$ldif = new Net_LDAP2_LDIF($input_filename);

do {
	$entry = $ldif->read_entry();
	$dn    = $entry->dn();
	echo " done building entry: $dn\n";
	array_push($entries, $entry);
} while (!$ldif->eof());

$ldif->done();

/* write those entries to another file
$ldif = new Net_LDAP_LDIF('test.out.ldif', 'w', $options);
$ldif->write_entry($entries);
$ldif->done();
 */
