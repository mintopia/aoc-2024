<?php
namespace Mintopia\Aoc2024;

use Mintopia\Aoc2024\Helpers\Result;

class Day1 extends Day
{
    protected const TITLE = 'Historian Hysteria';

    protected function splitLines(array $data): array
    {
        $alpha = [];
        $bravo = [];
        foreach ($data as $line) {
            $parts = explode(' ', $line);
            $alpha[] = $parts[0];
            $bravo[] = end($parts);
        }
        sort($alpha);
        sort($bravo);
        return [$alpha, $bravo];
    }

    protected function part1(): Result
    {
        $answer = 0;
        [$alpha, $bravo] = $this->splitLines($this->data);
        foreach ($alpha as $index => $a) {
            $answer += abs($a - $bravo[$index]);
        }
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $answer = 0;
        [$alpha, $bravo] = $this->splitLines($this->data);
        $count = array_count_values($bravo);
        foreach ($alpha as $a) {
            $answer += $a * ($count[$a] ?? 0);
        }
        return new Result(Result::PART2, $answer);
    }
}