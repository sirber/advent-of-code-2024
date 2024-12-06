<?php

namespace Sirber\Day05;

/**
 * The Elf has for you both the page ordering rules and the 
 * pages to produce in each update (your puzzle input), but 
 * can't figure out whether each update has the pages in 
 * the right order.
 * 
 * The first section specifies the page ordering rules, one 
 * per line. The first rule, 47|53, means that if an update 
 * includes both page number 47 and page number 53, then 
 * page number 47 must be printed at some point before 
 * page number 53. (47 doesn't necessarily need to be 
 * immediately before 53; other pages are allowed to 
 * be between them.)
 * 
 * The second section specifies the page numbers of each 
 * update. Because most safety manuals are different, 
 * the pages needed in the updates are different too. 
 * The first update, 75,47,61,53,29, means that the 
 * update consists of page numbers 75, 47, 61, 53, 
 * and 29.
 * 
 * Determine which updates are already in the correct order. 
 * What do you get if you add up the middle page number from 
 * those correctly-ordered updates?
 * 
 * @link https://adventofcode.com/2024/day/5
 */

// Data
$data = file_get_contents('./d05.txt');
$lines = explode("\n", $data);

$pageOrders = [];
$manuals = [];
foreach ($lines as $line) {
  // Order
  if (strpos($line, '|')) {
    $pageOrders[] = explode('|', $line);
    continue;
  }

  // Manual
  if (strpos($line, ',')) {
    $manuals[] = explode(',', $line);
    continue;
  }
}

echo "Page Orders: " . count($pageOrders) . PHP_EOL;
echo "Manuals: " . count($manuals) . PHP_EOL;

// Validate Manuals
$result = 0;
foreach ($manuals as $manual) {
  $isValid = validateManual($manual, $pageOrders);

  if ($isValid) {
    $result += sumMiddlePages($manual);
  }
}

echo "Result: $result";

function validateManual(array $manual, array $pageOrders): bool
{
  echo "Manual: " . join(',', $manual) . PHP_EOL;

  foreach ($manual as $index => $page) {
    if ($index === 0) {
      continue;
    }

    $pagesBefore = array_slice($manual, 0, $index);

    $isPageValid = validatePageOrder($page, $pagesBefore, $pageOrders);
    if (!$isPageValid) {
      return false;
    }
  }

  return true;
}

function validatePageOrder(int $page, array $pagesBefore, array $pageOrders): bool
{
  $currentPageOrders = array_filter($pageOrders, function ($order) use ($page) {
    return $order[0] == $page;
  });

  $currentPageMustBeBefore = array_map(function ($order) {
    return $order[1];
  }, $currentPageOrders);

  $isCorrect = true;
  foreach ($pagesBefore as $testPage) {
    $isPageFound = array_search($testPage, $currentPageMustBeBefore);
    if ($isPageFound !== false) {
      $isCorrect = false;
    }
  }

  echo "Page $page is " . ($isCorrect ? "correct" : "incorrect") . PHP_EOL;

  return $isCorrect;
}

function sumMiddlePages(array $manual): int
{
  $middlePages = array_slice($manual, 1, -1);
  $sum = array_sum($middlePages);

  echo "Sum of '" . join(',', $middlePages) . "' is $sum" . PHP_EOL;

  return $sum;
}
