<?php

namespace Sirber\Day04;

/**
 * Looking for the instructions, you flip over the word search to find that 
 * this isn't actually an XMAS puzzle; it's an X-MAS puzzle in which you're 
 * supposed to find two MAS in the shape of an X.
 * 
 * @link https://adventofcode.com/2024/day/4#part2
 */

// Const
$word = "MAS";
$directions = [
  [1, 1],   // Diagonal down-right
  [-1, -1], // Diagonal up-left
  [1, -1],  // Diagonal down-left
  [-1, 1],  // Diagonal up-right
];
$wordLength = strlen($word);

// Data
$data = file_get_contents('./d04.txt');
$lines = explode("\n", $data);

$grid = array_map(function (string $line) {
  return str_split($line);
}, $lines);

$rows = count($grid);
$cols = count($grid[0]);

// Find All MAS
$charALocations = []; // gonna store x,y of each "A" of every "MAS"
for ($col = 0; $col < $cols; $col++) {
  for ($row = 0; $row < $rows; $row++) {
    foreach ($directions as $dir) {
      $dx = $dir[0];
      $dy = $dir[1];
      $found = true;

      $charALocation = '';
      for ($k = 0; $k < $wordLength; $k++) {
        $newRow = $row + $k * $dx;
        $newCol = $col + $k * $dy;

        // Check bounds
        if ($newRow < 0 || $newRow >= $rows || $newCol < 0 || $newCol >= $cols) {
          $found = false;
          break;
        }

        // Check character
        if ($grid[$newRow][$newCol] !== $word[$k]) {
          $found = false;
          break;
        }

        // Check for A
        if ('A' == $word[$k]) {
          $charALocation = $newRow . '-' . $newCol;
        }
      }

      if ($found) {
        $charALocations[] = $charALocation;
      }
    }
  }
}

// Count similar locations
$occurrences = array_count_values($charALocations);
$result = array_reduce($occurrences, function (int $inc, int $count) {
  if ($count > 1) {
    $inc++;
  }

  return $inc;
}, 0);

echo "Result: $result";
