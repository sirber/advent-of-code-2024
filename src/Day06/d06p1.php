<?php

namespace Sirber\Day06;

/**
 * @link https://adventofcode.com/2024/day/6
 */

// Data
$data = file_get_contents('./day06.txt');
$lines = explode("\n", $data);

$directions = [
  '^' => [-1, 0], // Move up
  '>' => [0, 1],  // Move right
  'v' => [1, 0],  // Move down
  '<' => [0, -1], // Move left
];

// Guard
class Guard
{
  public string $direction = '^';
  public array $location = [0, 0];

  public function setDirection(string $direction): void
  {
    $this->direction = $direction;
  }

  public function setLocation(int $y, int $x): void
  {
    $this->location = [$y, $x];
  }
}

enum MoveType
{
  case OK;
  case OBSTRUCTION;
  case OUT_OF_MAP;
}

$guard = new Guard();

// Grid
$grid = [];
foreach ($lines as $y => $line) {
  $row = str_split($line);
  foreach ($row as $x => $value) {
    $grid[$y][$x] = $value;

    // Find guard location
    if ($value == $guard->direction) {
      $guard->setLocation($y, $x);
    }
  }
}

echo "Room is " . count($grid[0]) . " by " . count($grid) . PHP_EOL;
echo "Guard is at: " . join(',', $guard->location) . PHP_EOL;

// Play
$canMove = MoveType::OK;
while ($canMove != MoveType::OUT_OF_MAP) {
  echo '[' . join(',', $guard->location) . '] ' . $guard->direction . PHP_EOL;

  $canMove = canMove($guard, $grid, $directions);
  switch ($canMove) {
    case MoveType::OBSTRUCTION:
      $newDirection = turnGuard($guard->direction, $directions);
      $room[$guard->location[0]][$guard->location[1]] = $newDirection;
      $guard->setDirection($newDirection);
      break;

    case MoveType::OUT_OF_MAP:
      markAsVisited($guard->location, $grid);
      break;

    case MoveType::OK:
      markAsVisited($guard->location, $grid);
      moveGuard($guard, $grid, $directions);
      break;
  }
}

echo "Result: " . findInRoom($grid, 'X') . PHP_EOL;

function findInRoom(array $room, string $type = '.'): int
{
  $count = 0;

  foreach ($room as $row) {
    $count += count(
      array_filter($row, fn($cell) => $cell == $type)
    );
  }

  return $count;
}

function canMove(Guard $guard, array $room, array $directions): MoveType
{
  $newLocation = getNewLocation($guard, $directions);
  $value = $room[$newLocation[0]][$newLocation[1]] ?? null;

  // Check for out of map
  if (null === $value) {
    return MoveType::OUT_OF_MAP;
  }

  // Check for obstruction
  if ($value === '#') {
    return MoveType::OBSTRUCTION;
  }

  return MoveType::OK;
}

function moveGuard(Guard $guard, array &$room, array $directions): void
{
  $currentDirection = $directions[$guard->direction];

  // Move to the new location
  $newLocation = getNewLocation($guard, $directions);
  $guard->setLocation(...$newLocation);

  // Update direction in the room
  $room[$newLocation[0]][$newLocation[1]] = $currentDirection;
}

function markAsVisited(array $location, array &$room): void
{
  $room[$location[0]][$location[1]] = 'X';
}

function getNewLocation(Guard $guard, array $directions): array
{
  $direction = $directions[$guard->direction];

  return array_map(function ($a, $b) {
    return $a + $b;
  }, $guard->location, $direction);
}

function turnGuard(string $currentDirection, array $directions): string
{
  $keys = array_keys($directions);
  $index = array_search($currentDirection, $keys);

  return $keys[($index + 1) % 4];
}
