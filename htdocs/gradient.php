<?php
// See:	http://fr.php.net/manual/fr/ref.image.php

function imageGradient($x_size,
                       $y_size,
                       $left_upper='FFFFFF', $right_upper='55AA88',
                       $left_lower='004400', $right_lower='002200') {
  $image = imagecreatetruecolor($x_size, $y_size);
  $right_upper = getColorArray($right_upper);
  $left_upper  = getColorArray($left_upper); 
  $left_lower  = getColorArray($left_lower);
  $right_lower = getColorArray($right_lower);

  // precision:
  if(($x_size*$y_size)<=10000)
   $size = 1;
  else
   $size = 2;

  $left  = $left_upper;
  $right = $left_lower;
  list($r, $g, $b) = $left;
  $delta_x1 = getDeltaArray($left, $right_upper, $x_size);
  $delta_x2 = getDeltaArray($right, $right_lower, $x_size);
 
  for($i=0;$i<$x_size;$i+=$size) {
   list($r, $g, $b) = $left;
   for($j=0;$j<$y_size;$j+=$size) {
     $delta_y = getDeltaArray($left, $right, $y_size);     
     $r = $r - ($delta_y[0]*$size);
     $g = $g - ($delta_y[1]*$size);
     $b = $b - ($delta_y[2]*$size);
     $col = imagecolorallocate($image, round($r), round($g), round($b));
     imagefilledrectangle($image, $i, $j, $i+$size, $j+$size, $col);
   }
   $left[0] = $left[0] - ($delta_x1[0]*$size);
   $left[1] = $left[1] - ($delta_x1[1]*$size);
   $left[2] = $left[2] - ($delta_x1[2]*$size);
   $right[0] = $right[0] - ($delta_x2[0]*$size);
   $right[1] = $right[1] - ($delta_x2[1]*$size);
   $right[2] = $right[2] - ($delta_x2[2]*$size);
  } 
  return $image;
}

function getDeltaArray($col1, $col2, $size) {
  $r_range = $col1[0] - $col2[0];
  $g_range = $col1[1] - $col2[1];
  $b_range = $col1[2] - $col2[2];
 
  if($r_range<0) $r_range*=-1;
  if($g_range<0) $g_range*=-1;
  if($b_range<0) $b_range*=-1;
 
  $delta_r = $r_range/$size;
  $delta_g = $g_range/$size;
  $delta_b = $b_range/$size;

  return array($delta_r, $delta_g, $delta_b);
}

function getColorArray($col) {
   list($r, $g, $b) = sscanf($col, '%2x%2x%2x');
   return array($r, $g, $b);
}

header('Content-Type: image/png');
$img = imageGradient(300, 300); // image dimensions
imagePNG($img);
imageDestroy($img);
?>
