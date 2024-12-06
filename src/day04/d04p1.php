<?php

/**
 * She only has to find one word: XMAS.
 * 
 * This word search allows words to be horizontal, vertical, diagonal, 
 * written backwards, or even overlapping other words. It's a little 
 * unusual, though, as you don't merely need to find one instance of 
 * XMAS - you need to find all of them.
 * 
 * @link https://adventofcode.com/2024/day/4
 */

// Const
$word = "XMAS";
$directions = [
  [0, 1],   // Horizontal right
  [0, -1],  // Horizontal left
  [1, 0],   // Vertical down
  [-1, 0],  // Vertical up
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

// Find XMAS
$result = 0;
for ($col = 0; $col < $cols; $col++) {
  for ($row = 0; $row < $rows; $row++) {
    foreach ($directions as $dir) {
      $dx = $dir[0];
      $dy = $dir[1];
      $found = true;

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
      }

      if ($found) {
        $result++;
      }
    }
  }
}

echo "Result: $result";
