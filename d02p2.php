<?php

/**
 * The engineers are trying to figure out which reports are safe. 
 * The Red-Nosed reactor safety systems can only tolerate levels that are 
 * either gradually increasing or gradually decreasing. So, a report only 
 * counts as safe if both of the following are true:
 * 
 * - The levels are either all increasing or all decreasing.
 * - Any two adjacent levels differ by at least one and at most three.
 * - Tolerance: 1
 * 
 * @link https://adventofcode.com/2024/day/2#part2
 */

// Get the Data
$data = file_get_contents('./d02.txt');
$lines = explode("\n", $data);

// Validate each line
$nbSafe = 0;
foreach ($lines as $line) {
  $numbers = preg_split('/\s+/', trim($line));
  if (count($numbers) <= 2) {
    continue;
  }

  // Cleanup
  $nbDiffs = count($numbers) - 1;
  for ($i = 0; $i < $nbDiffs; $i++) {
    // Remove same level
    if ((int) $numbers[$i] == (int) $numbers[$i + 1]) {
      unset($numbers[$i + 1]);
      break;
    }

    // Remove high difference
    $diff = abs((int) $numbers[$i] - (int) $numbers[$i + 1]);
    if ($diff > 3) {
      unset($numbers[$i + 1]);
      break;
    }

    // Remove level that prevent increasing or decreasing
    if ($i > 0) {
      // Current, previous, and next comparison
      if (
        ((int) $numbers[$i - 1] < (int) $numbers[$i] && (int) $numbers[$i] > (int) $numbers[$i + 1]) ||
        ((int) $numbers[$i - 1] > (int) $numbers[$i] && (int) $numbers[$i] < (int) $numbers[$i + 1])
      ) {
        unset($numbers[$i]);
        break;
      }
    }
  }

  $numbers = array_values($numbers); // regenerate the index

  // Count
  $nbIncreasing = 0;
  $nbDecreasing = 0;
  $nbDiffInRange = 0;
  $nbDiffs = count($numbers) - 1;
  for ($i = 0; $i < $nbDiffs; $i++) {
    if ((int) $numbers[$i] < (int) $numbers[$i + 1]) {
      $nbIncreasing++;
    }

    if ((int) $numbers[$i] > (int) $numbers[$i + 1]) {
      $nbDecreasing++;
    }

    $diff = abs((int) $numbers[$i] - (int) $numbers[$i + 1]);
    if ($diff >= 1 && $diff <= 3) {
      $nbDiffInRange++;
    }
  }

  // Validate
  $check1 = ($nbDiffs == $nbIncreasing || $nbDiffs == $nbDecreasing);
  $check2 = ($nbDiffs == $nbDiffInRange);
  if ($check1 && $check2) {
    $nbSafe++;
  }
}

echo "Result: $nbSafe";
