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

test();
main();

function main(): void
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

function test(): void
{
  // Generate
  $data = '2333133121414131402';
  $result = '00...111...2...333.44.5555.6666.777.888899';
  if ($result != generateFileSystem($data)) {
    throw new Exception('generateFileSystem() is broken');
  }

  // Defragment
  $data = '00...111...2...333.44.5555.6666.777.888899';
  $result = '0099811188827773336446555566..............';
  if ($result != defrag($data)) {
    throw new Exception('defrag() is broken');
  }

  // Checksum
  $data = '0099811188827773336446555566..............';
  $result = 1928;
  if ($result != checksum($data)) {
    throw new Exception('checksum() is broken');
  }
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
