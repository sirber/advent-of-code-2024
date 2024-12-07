<?php

/**
 * For each of the incorrectly-ordered updates, use the page 
 * ordering rules to put the page numbers in the right order.
 * 
 * @link https://adventofcode.com/2024/day/5#part2
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
        echo "> is valid. skip!" . PHP_EOL;
        continue;
    }

    $orderedManual = reorderManual($manual, $pageOrders);
    $isValid = validateManual($orderedManual, $pageOrders);
    if (!$isValid) {
        throw new Exception("Could not validate reordered manual");
    }

    $result += sumMiddlePages($orderedManual);
}

echo "Result: $result";

function reorderManual(array $manual, array $dependencies): array
{
    // Create a quick lookup map for direct dependencies
    $dependencyMap = [];
    foreach ($dependencies as [$before, $after]) {
        $dependencyMap[$before][] = $after;
    }

    // Define a simple comparison function
    usort($manual, function ($a, $b) use ($dependencyMap) {
        // If $a must come before $b
        if (isset($dependencyMap[$a]) && in_array($b, $dependencyMap[$a])) {
            return -1;
        }

        // If $b must come before $a
        if (isset($dependencyMap[$b]) && in_array($a, $dependencyMap[$b])) {
            return 1;
        }

        // Otherwise, keep the original order
        return 0;
    });

    return $manual;
}

function validateManual(array $manual, array $pageOrders): bool
{
    echo "Manual: " . join(',', $manual) . PHP_EOL;

    foreach ($pageOrders as [$page, $mustBeBefore]) {
        $pageIndex = array_search($page, $manual);
        $beforeIndex = array_search($mustBeBefore, $manual);

        if ($pageIndex !== false && $beforeIndex !== false && $pageIndex > $beforeIndex) {
            return false; // Invalid order
        }
    }

    return true;
}

function sumMiddlePages(array $manual): int
{
    $middleIndex = round(count($manual) / 2, PHP_ROUND_HALF_DOWN);
    $sum = $manual[$middleIndex];

    echo "Middle of '" . join(',', $manual) . "' is $sum" . PHP_EOL;

    return $sum;
}
