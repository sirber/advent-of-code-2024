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
        // TODO: Get Last File length (with indexes)

        // TODO: Get First long enough Free Space (with indexes)

        // TODO: Move File

        $lastFileId--;
    }

    return $fileSystem;
}

function checksum(array $fileSystem): int
{
    $result = 0;
    foreach ($fileSystem as $index => $id) {
        if (null === $id) {
            break;
        }

        $result += ($index * $id);
    }

    return $result;
}
