#!/usr/bin/env php
<?php
/**
 * Takes a character stream on the input
 * and send it without any tag on the output.
 */
while($line=fgets(STDIN)) echo strip_tags($line);
