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

function reorderManual(array $manual, array $dependencies)
{
    // Step 1: Build a dependency graph
    $graph = [];
    $inDegree = [];
    $allPages = array_flip($manual); // Flip to map pages for fast lookup

    foreach ($dependencies as [$page, $mustBeBefore]) {
        // Skip dependencies not in the manual
        if (!isset($allPages[$page]) || !isset($allPages[$mustBeBefore])) {
            continue;
        }

        $graph[$page][] = $mustBeBefore;
        $inDegree[$mustBeBefore] = ($inDegree[$mustBeBefore] ?? 0) + 1;
        $inDegree[$page] = $inDegree[$page] ?? 0;
    }

    // Step 2: Perform topological sorting
    $queue = [];
    foreach ($inDegree as $page => $count) {
        if ($count === 0) {
            $queue[] = $page;
        }
    }

    $sortedOrder = [];
    while (!empty($queue)) {
        $current = array_shift($queue);
        $sortedOrder[] = $current;

        if (isset($graph[$current])) {
            foreach ($graph[$current] as $neighbor) {
                $inDegree[$neighbor]--;
                if ($inDegree[$neighbor] === 0) {
                    $queue[] = $neighbor;
                }
            }
        }
    }

    // Step 3: Reorder the manual based on the sorted order
    $reorderedManual = [];
    foreach ($sortedOrder as $page) {
        if (in_array($page, $manual)) {
            $reorderedManual[] = $page;
        }
    }

    // Append remaining pages not in dependencies
    foreach ($manual as $page) {
        if (!in_array($page, $reorderedManual)) {
            $reorderedManual[] = $page;
        }
    }

    return $reorderedManual;
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
