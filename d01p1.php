<?php

/**
 * Within each pair, figure out how far apart the two numbers are; you'll need to 
 * add up all of those distances. For example, if you pair up a 3 from the left 
 * list with a 7 from the right list, the distance apart is 4; if you pair up a 9 
 * with a 3, the distance apart is 6.
 * 
 * @link https://adventofcode.com/2024/day/1
 */

// Get the Data
$data = file_get_contents('./d01p1.txt');
$lines = explode("\n", $data);

// Split in two list
$list1 = [];
$list2 = [];
foreach ($lines as $line) {
  $numbers = preg_split('/\s+/', $line);
  if (count($numbers) < 2) {
    continue;
  }

  $list1[] = (int) $numbers[0];
  $list2[] = (int) $numbers[1];
}

// Sort
sort($list1);
sort($list2);

// Calculate distance
$distance = 0;
while (true) {
  $num1 = array_pop($list1);
  $num2 = array_pop($list2);

  if (null == $num1) {
    break;
  }

  $distance += abs($num1 - $num2);
}

echo "Distance: $distance";
