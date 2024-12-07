<?php

/**
 * @link https://adventofcode.com/2024/day/7
 */

// Data
$data = file_get_contents(__DIR__ . '/d07.txt');
$lines = explode("\n", trim($data));

$lineRegex = '/(\d+)/';
$operations = ['+', '*'];

$result = 0;

foreach ($lines as $line) {
  // Get numbers and cleanup
  $matches = [];
  preg_match_all($lineRegex, $line, $matches);
  $numbers = array_map(fn($match) => (int) $match, $matches[0]);

  $total = array_shift($numbers); // First number is the target result

  // Generate equations
  $equations = generateExpressions($numbers, $operations);

  // Calculate equations
  foreach ($equations as $equation) {
    $evaluated = evaluateLeftToRight($equation);
    if ($evaluated === $total) {
      $result += $evaluated;
      echo ".";
    }
  }
}

echo "\n\nResult: $evaluated\n";

/**
 * Generate all possible expressions using numbers and operators
 */
function generateExpressions(array $numbers, array $operations, $index = 0, $current = ""): array
{
  $results = [];

  // If we've reached the last number, add the current expression to the results
  if ($index == count($numbers)) {
    $results[] = $current;
    return $results;
  }

  foreach ($operations as $operation) {
    // Add the first number if it's the first iteration
    $newExpression = $current . ($index > 0 ? $operation : "") . $numbers[$index];
    $results = array_merge($results, generateExpressions($numbers, $operations, $index + 1, $newExpression));
  }

  return array_unique($results);
}

/**
 * Evaluate the expression strictly left-to-right
 */
function evaluateLeftToRight(string $expression): int
{
  // Split expression into tokens (numbers and operators)
  $tokens = preg_split('/([+\-*\/])/', $expression, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

  // Start evaluating strictly left-to-right
  $result = (int)$tokens[0]; // Start with the first number
  for ($i = 1; $i < count($tokens); $i += 2) {
    $operator = $tokens[$i];
    $nextNumber = (int)$tokens[$i + 1];

    // Apply the operation
    if ($operator === '+') {
      $result += $nextNumber;
    } elseif ($operator === '*') {
      $result *= $nextNumber;
    }
  }

  return $result;
}
