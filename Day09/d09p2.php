<?php

/**
 * @link https://adventofcode.com/2024/day/9#part2
 */

main();

function main(): void
{
    $data = file_get_contents(__DIR__ . '/d09.txt');
    $fileSystem = generateFileSystem($data);
    $fileSystem = defrag($fileSystem);
    $checksum = checksum($fileSystem);

    // Result
    echo "Result: " . $checksum;
}

function generateFileSystem(string $rawData): array
{
    $parsedData = array_map(fn(string $char) => (int) $char, str_split($rawData));

    $fileSystem = [];
    $fileId = 0;
    foreach ($parsedData as $index => $value) {
        $isFreeSpace = ($index % 2) > 0;

        for ($i = 0; $i < $value; $i++) {
            $fileSystem[] = $isFreeSpace ? null : $fileId;
        }

        if (!$isFreeSpace) {
            $fileId++;
        }
    }

    return $fileSystem;
}

// Defrag Whole Files
function defrag(array $fileSystem): array
{
    $lastFileId = $fileSystem[array_key_last(array_filter($fileSystem, fn($block) => null !== $block))];

    while ($lastFileId >= 0) {
        echo "$lastFileId: ";

        $fileBlocks = array_keys(array_filter($fileSystem, fn($fileId) => $fileId === $lastFileId));
        $fileLen = count($fileBlocks);
        echo "($fileLen) ";

        $freeBlocksAt = findFirstXConsecutiveNulls($fileSystem, $fileLen);
        $lastFileId--;
        if (!$freeBlocksAt || $freeBlocksAt > $fileBlocks[0]) {
            echo "skipped\n";
            continue;
        }

        $i = 0;
        foreach ($fileBlocks as $index) {
            $fileSystem[$freeBlocksAt + $i] = $lastFileId;
            $fileSystem[$index] = null;
            $i++;
        }

        echo "moved at $freeBlocksAt\n";
    }

    return array_values($fileSystem); // resets index
}

function checksum(array $fileSystem): int
{
    $result = 0;
    foreach ($fileSystem as $index => $id) {
        if (null === $id) {
            continue;
        }

        $result += ($index * $id);
    }

    return $result;
}

function findFirstXConsecutiveNulls(array $arr, int $x): ?int
{
    $count = 0;

    foreach ($arr as $key => $value) {
        if (is_null($value)) {
            $count++;
            if ($count === $x) {
                // Return the starting index of the sequence
                return $key - $x + 1;
            }
        } else {
            $count = 0; // Reset count if a non-null value is found
        }
    }

    // Return null if no such sequence exists
    return null;
}
