<?php

/**
 * This time, you'll need to figure out exactly how often each number from the left 
 * list appears in the right list. Calculate a total similarity score by adding up 
 * each number in the left list after multiplying it by the number of times that 
 * number appears in the right list.
 * 
 * @link https://adventofcode.com/2024/day/1
 */

// Get the Data
$data = file_get_contents('./d01.txt');
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

$score = 0;
foreach ($list1 as $number1) {
  $nbItems = count(
    array_filter($list2, function ($number2) use ($number1) {
      return $number1 == $number2;
    })
  );

  $score += $number1 * $nbItems;
}

echo "Answer: $score";
