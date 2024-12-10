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
  echo "\ngenerateFileSystem\n";

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
  echo "\ndefrag\n";

  $startIndex = 0;
  $endIndex = strlen($fileSystem) - 1;
  $mode = DefragMode::FindNextFreeSpace;
  $arrFileSystem = str_split($fileSystem);

  while (true) {
    if ($startIndex >= $endIndex) {
      break;
    }

    $startBlock = $arrFileSystem[$startIndex];
    $endBlock = $arrFileSystem[$endIndex];

    switch ($mode) {
      case DefragMode::FindNextFreeSpace:
        if ($startBlock != '.') {
          $startIndex++;
          break;
        }

        $mode = DefragMode::FindBlockToMove;
        break;

      case DefragMode::FindBlockToMove:
        if ($endBlock == '.') {
          $endIndex--;
          break;
        }

        $mode = DefragMode::MoveBlock;
        break;

      case DefragMode::MoveBlock:
        $arrFileSystem[$startIndex] = $endBlock;
        $arrFileSystem[$endIndex] = '.';

        $mode = DefragMode::FindNextFreeSpace;
        break;

      default:
        throw new Exception("unknown mode: " . $mode);
    }
  }

  return join($arrFileSystem);
}

function checksum(string $fileSystem): int
{
  echo "\nchecksum\n";

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
