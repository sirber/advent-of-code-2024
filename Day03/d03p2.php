<?php

/**
 * There are two new instructions you'll need to handle:
 * 
 * - The do() instruction enables future mul instructions.
 * - The don't() instruction disables future mul instructions.
 * 
 * Only the most recent do() or don't() instruction applies. At 
 * the beginning of the program, mul instructions are enabled.
 * 
 * @link https://adventofcode.com/2024/day/3#part2
 */

// Get the data
$data = file_get_contents('./d03.txt');

// Find all mul(x,y), do() and don't()
$regex = '/mul\((\d+),(\d+)\)|do\(\)|don\'t\(\)/m';
$matches = [];
preg_match_all($regex, $data, $matches);
$count = count($matches[0]);

echo "Found: $count" . PHP_EOL;

// Iterate over all the matches
$mulEnabled = true;
$result = 0;
foreach ($matches[0] as $index => $match) {
  // Handle mul(x, y)
  if (preg_match('/mul\((\d+),(\d+)\)/', $match, $mulMatches)) {
    if ($mulEnabled) {
      // Calculate the product of the numbers
      $num1 = (int)$mulMatches[1];
      $num2 = (int)$mulMatches[2];
      $mulResult = $num1 * $num2;
      $result += $mulResult;
    }
  }

  // Handle do() instruction (enables mul instructions)
  elseif ($match === 'do()') {
    $mulEnabled = true;
  }

  // Handle don't() instruction (disables mul instructions)
  elseif ($match === "don't()") {
    $mulEnabled = false;
  }
}

echo "Result: $result" . PHP_EOL;
