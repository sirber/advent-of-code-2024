<?php

/**
 * @link https://adventofcode.com/2024/day/9
 */

enum DefragMode
{
  case FindNextFreeSpace;
  case FindBlockToMove;
  case MoveBlock;
}

main();

function main()
{
  // Data
  $data = file_get_contents(__DIR__ . '/d09.txt');

  // Generate
  $fileSystem = generateFileSystem($data);

  // Defragment
  $fileSystem = defrag($fileSystem);

  // Checksum
  $checksum = checksum($fileSystem);

  // Result
  echo "Result: " . $checksum;
}

function generateFileSystem(string $rawData): string
{
  $parsedData = array_map(fn(string $char) => (int) $char, str_split($rawData));

  $fileSystem = '';
  $fileId = 0;
  foreach ($parsedData as $index => $value) {
    $isFreeSpace = ($index % 2) > 0;

    if ($isFreeSpace) {
      $fileSystem .= str_repeat('.', $value);
    } else {
      $fileSystem .= str_repeat(($fileId % 10), $value);
      $fileId++;
    }
  }

  return $fileSystem;
}

function defrag(string $fileSystem): string
{
  $startIndex = 0;
  $endIndex = strlen($fileSystem);
  $mode = DefragMode::FindNextFreeSpace;
  $arrFileSystem = str_split($fileSystem);

  while (true) {
    if ($startIndex >= $endIndex) {
      break;
    }

    $startBlock = $arrFileSystem[$startIndex];
    $enfBlock = $arrFileSystem[$endIndex];

    switch ($mode) {
      case DefragMode::FindBlockToMove:
        if ($startBlock != '.') {
          $startIndex++;
          continue;
        }

        $mode = DefragMode::FindBlockToMove;
        break;

      case DefragMode::FindBlockToMove:
        if ($startBlock == '.') {
          $endIndex--;
          continue;
        }

        $mode = DefragMode::MoveBlock;
        break;

      case DefragMode::MoveBlock:
        // TODO: move block

        $mode = DefragMode::FindNextFreeSpace;
        break;
    }
  }

  // TODO: 
  // return "0099811188827773336446555566..............";
  return $fileSystem;
}

function checksum(string $fileSystem): int
{
  $parsedData = str_split($fileSystem);

  $result = 0;
  foreach ($parsedData as $index => $value) {
    if ($value == '.') {
      break;
    }

    $result += ($index * $value);
  }

  return $result;
}
