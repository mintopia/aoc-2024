<?php
namespace Mintopia\Aoc2024;

use Mintopia\Aoc2024\Helpers\Result;

class Day6 extends Day
{
    protected array $vectors = [
        [-1, 0],
        [0, 1],
        [1, 0],
        [0, -1]
    ];

    protected array $start;
    protected function loadData(): void
    {
        $this->loadGridFromData();
        foreach ($this->data as $r => $row) {
            foreach ($row as $c => $col) {
                if ($col === '^') {
                    $this->start = [$r, $c];
                    break;
                }
            }
        }
    }

    protected const TITLE = 'Guard Gallivant';

    protected function part1(): Result
    {
        [$visited, $loop] = $this->traverse($this->data, $this->start, 0);
        $answer = count($visited);
        return new Result(Result::PART1, $answer, $visited);
    }

    protected function part2(Result $part1): Result
    {
        $answer = 0;
        $visited = $part1->carry;
        $checked = [];
        $history = [];
        foreach ($visited as $key => $position) {
            if (in_array($position, $checked)) {
                break;
            }
            if ($position === $this->start) {
                $history[$key] = $position;
                continue;
            }
            $checked[] = $position;
            $guard = end($history);
            $history[$key] = $position;

            $direction = explode('.', $key)[2];

            $grid = $this->data;
            $grid[$position[0]][$position[1]] = '#';
            [$path, $loop] = $this->traverse($grid, $guard, $direction, $history);
            if ($loop) {
                $answer++;
            }
        }
        return new Result(Result::PART2, $answer);
    }

    protected function traverse(array $grid, array $start, int $direction, array $history = []): ?array
    {
        $position = $start;
        $history[] = $position;
        $loop = false;
        do {
            $row = $position[0] + $this->vectors[$direction][0];
            $col = $position[1] + $this->vectors[$direction][1];
            if (!isset($grid[$row][$col])) {
                break;
            }
            if ($grid[$row][$col] === '#') {
                $direction++;
                if ($direction > 3) {
                    $direction = 0;
                }
            } else {
                $position = [$row, $col];
                $key = "{$row}.{$col}.{$direction}";
                if (array_key_exists($key, $history)) {
                    // Loop
                    $loop = true;
                    break;
                }
                if (!in_array($position, $history)) {
                    $history[$key] = $position;
                }
            }
        } while (true);
        return [$history, $loop];
    }
}