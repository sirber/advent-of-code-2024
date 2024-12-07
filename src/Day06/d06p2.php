<?php

namespace Sirber\Day06;

/**
 * You need to get the guard stuck in a loop by adding 
 * a single new obstruction. How many different positions 
 * could you choose for this obstruction?
 * 
 * Strategy:
 * - try every "." on the map
 * 
 * @link https://adventofcode.com/2024/day/6#part2
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
  public array $originalLocation = [0, 0];

  public function setDirection(string $direction): void
  {
    $this->direction = $direction;
  }

  public function setOriginalLocation(int $y, int $x): void
  {
    $this->originalLocation = [$y, $x];
  }

  public function setLocation(int $y, int $x): void
  {
    $this->location = [$y, $x];
  }

  public function resetLocation(): void
  {
    $this->location = $this->originalLocation;
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
      $guard->setOriginalLocation($y, $x);
      $guard->setLocation($y, $x);
    }
  }
}

echo "Room is " . count($grid[0]) . " by " . count($grid) . PHP_EOL;
echo "Guard is at: " . join(',', $guard->location) . PHP_EOL;

$freeSpots = getFreeSpots($grid);
echo "Nb free spots: " . count($freeSpots) . PHP_EOL;

$nbLoop = 0;
foreach ($freeSpots as [$y, $x]) {
  $room = $grid; // get a fresh copy
  $room[$y][$x] = 'O';

  $guard->resetLocation();

  // Play
  $canMove = MoveType::OK;
  $visitedStates = [];
  while ($canMove != MoveType::OUT_OF_MAP) {
    // Save the current state
    $state = join(',', [$guard->location[0], $guard->location[1], $guard->direction]);
    if (isset($visitedStates[$state])) {
      // Loop detected
      $nbLoop++;
      break;
    }
    $visitedStates[$state] = true;

    $canMove = canMove($guard, $room, $directions);
    switch ($canMove) {
      case MoveType::OBSTRUCTION:
        $newDirection = turnGuard($guard->direction, $directions);
        $room[$guard->location[0]][$guard->location[1]] = $newDirection;
        $guard->setDirection($newDirection);
        break;

      case MoveType::OUT_OF_MAP:
        break;

      case MoveType::OK:
        moveGuard($guard, $room, $directions);
        break;
    }
  }
}

echo "Result: " . $nbLoop . PHP_EOL;

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

function getFreeSpots(array $room): array
{
  $freeSpots = [];

  foreach ($room as $y => $row) {
    foreach ($row as $x => $cell) {
      if ($cell === '.') {
        $freeSpots[] = [$y, $x];
      }
    }
  }

  return $freeSpots;
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
  if ($value === '#' or $value === 'O') {
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
