<?php
namespace Mintopia\Aoc2024;

use Mintopia\Aoc2024\Helpers\Result;

class Day4 extends Day
{
    protected const TITLE = 'Ceres Search';

    protected function loadData(): void
    {
        $this->loadGridFromData();
    }

    protected function countMas(int $row, int $column): int
    {
        $letter = $this->data[$row][$column];
        if ($letter !== 'A') {
            return 0;
        }

        $deltas = [
            [-1, -1],
            [-1, 1],
            [1, -1],
            [1, 1],
        ];

        $letters = [];
        foreach ($deltas as [$deltaRow, $deltaCol]) {
            if (!isset($this->data[$row + $deltaRow][$column + $deltaCol])) {
                return 0;
            }
            $letter = $this->data[$row + $deltaRow][$column + $deltaCol];
            if ($letter !== 'M' && $letter !== 'S') {
                return 0;
            }
            $letters[] = $letter;
        }
        if ($letters[0] !== $letters[3] && $letters[1] !== $letters[2]) {
            return 1;
        }
        return 0;
    }

    protected function countXmas(int $row, int $column): int
    {
        $letter = $this->data[$row][$column];
        if ($letter !== 'X') {
            return 0;
        }

        $count = 0;

        $directions = [
            [-1, -1],
            [-1, 0],
            [-1, 1],
            [0, -1],
            [0, 1],
            [1, -1],
            [1, 0],
            [1, 1],
        ];
        $toFind = ['M', 'A', 'S'];
        foreach ($directions as [$deltaRow, $deltaCol]) {
            $r = $row;
            $c = $column;
            foreach ($toFind as $target) {
                $r += $deltaRow;
                $c += $deltaCol;
                if (!isset($this->data[$r][$c])) {
                    continue 2;
                }
                if ($this->data[$r][$c] !== $target) {
                    continue 2;
                }
            }
            $count++;
        }
        return $count;
    }

    protected function part1(): Result
    {
        $answer = 0;
        for ($row = 0; $row < count($this->data); $row++) {
            for ($column = 0; $column < count($this->data[$row]); $column++) {
                $answer += $this->countXmas($row, $column);
            }
        }
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $answer = 0;
        for ($row = 0; $row < count($this->data); $row++) {
            for ($column = 0; $column < count($this->data[$row]); $column++) {
                $answer += $this->countMas($row, $column);
            }
        }
        return new Result(Result::PART2, $answer);
    }
}