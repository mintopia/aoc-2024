<?php
namespace Mintopia\Aoc2024;

use Mintopia\Aoc2024\Helpers\Result;

class Day10 extends Day
{
    protected const TITLE = 'Hoof It';

    protected array $trailHeads = [];
    protected array $peaks = [];

    const DIRECTIONS = [
        [1, 0],
        [-1, 0],
        [0, -1],
        [0, 1],
    ];

    protected function loadData(): void
    {
        $this->loadGridFromData();
        foreach ($this->data as $r => $row) {
            foreach ($row as $c => $value) {
                if ($value == 0) {
                    $this->trailHeads[] = [$r, $c];
                } elseif ($value == 9) {
                    $this->peaks[] = [$r, $c];
                }
            }
        }
    }

    protected function getAllPaths(array $start, array $peak): array
    {
        $queue = new \SplQueue();
        $queue->enqueue([$start]);
        $paths = [];

        while ($queue->count() > 0) {
            $path = $queue->dequeue();
            $current = end($path);
            if ($current === $peak) {
                $paths[] = $path;
            }

            foreach (self::DIRECTIONS as [$dr, $dc]) {
                $neighbour = [$current[0] + $dr, $current[1] + $dc];
                if (in_array($neighbour, $path)) {
                    continue;
                }
                if (!isset($this->data[$neighbour[0]][$neighbour[1]])) {
                    continue;
                }
                if ($this->data[$neighbour[0]][$neighbour[1]] != $this->data[$current[0]][$current[1]] + 1) {
                    continue;
                }
                $newPath = $path;
                $newPath[] = $neighbour;
                $queue->enqueue($newPath);
            }
        }

        return $paths;
    }

    protected function part1(): Result
    {
        $score = 0;
        $rating = 0;
        foreach ($this->trailHeads as $start) {
            foreach ($this->peaks as $peak) {
                $paths = $this->getAllPaths($start, $peak);
                if ($paths) {
                    $score++;
                    $rating += count($paths);
                }
            }
        }
        return new Result(Result::PART1, $score, $rating);
    }

    protected function part2(Result $part1): Result
    {
        return new Result(Result::PART2, $part1->carry);
    }
}