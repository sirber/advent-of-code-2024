<?php

/**
 * @link https://adventofcode.com/2024/day/9
 */

enum DefragMode: int
{
  const FindNextFreeSpace = 0;
  const FindBlockToMove = 1;
  const MoveBlock = 2;
}

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

// Defrag Blocks
function defrag(array $fileSystem): array
{
  $startIndex = 0;
  $endIndex = count($fileSystem) - 1;
  $mode = DefragMode::FindNextFreeSpace;

  while (true) {
    if ($startIndex >= $endIndex) {
      break;
    }

    $startBlock = $fileSystem[$startIndex];
    $endBlock = $fileSystem[$endIndex];

    switch ($mode) {
      case DefragMode::FindNextFreeSpace:
        if ($startBlock !== null) {
          $startIndex++;
          break;
        }

        $mode = DefragMode::FindBlockToMove;
        break;

      case DefragMode::FindBlockToMove:
        if ($endBlock === null) {
          $endIndex--;
          break;
        }

        $mode = DefragMode::MoveBlock;
        break;

      case DefragMode::MoveBlock:
        $fileSystem[$startIndex] = $endBlock;
        $fileSystem[$endIndex] = null;

        $mode = DefragMode::FindNextFreeSpace;
        break;

      default:
        throw new Exception("unknown mode: " . $mode);
    }
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
