<?php

/**
 * It seems like the goal of the program is just to multiply some numbers. It 
 * does that with instructions like mul(X,Y), where X and Y are each 1-3 digit 
 * numbers. For instance, mul(44,46) multiplies 44 by 46 to get a result of 2024. 
 * Similarly, mul(123,4) would multiply 123 by 4.
 * 
 * However, because the program's memory has been corrupted, there are also many 
 * invalid characters that should be ignored, even if they look like part of a mul 
 * instruction. Sequences like mul(4*, mul(6,9!, ?(12,34), or mul ( 2 , 4 ) do nothing.
 * 
 * @link https://adventofcode.com/2024/day/3
 */

// Get the data
$data = file_get_contents('./d03.txt');

// Find all mul(x,y)
$regex = '/mul\((\d+),(\d+)\)/m';
$matches = [];
preg_match_all($regex, $data, $matches);
$count = count($matches[0]);

echo "Found: $count" . PHP_EOL;

$numbers = [];
for ($i = 0; $i < $count; $i++) {
  $numbers[] = [$matches[1][$i], $matches[2][$i]];
}

// Calculate
$result = array_reduce($numbers, function (int $inc, $match) {
  $mul = ((int) $match[0] * (int) $match[1]);

  return $inc + $mul;
}, 0);

echo "Result: $result";
