<?php
namespace Mintopia\Aoc2024;

use Mintopia\Aoc2024\Helpers\Result;

class Day3 extends Day
{
    protected const TITLE = 'Mull It Over';

    protected function part1(): Result
    {
        $answer = 0;
        foreach ($this->data as $line) {
            preg_match_all('/mul\((?<a>\d{1,3}),(?<b>\d{1,3})\)/', $line, $matches, PREG_SET_ORDER);
            foreach ($matches as $mul) {
                $answer += $mul['a'] * $mul['b'];
            }
        }
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $answer = 0;
        $do = true;
        foreach ($this->data as $line) {
            preg_match_all("/((?<do>do\(\))|(?<dont>don't\(\)|mul\((?<a>\d{1,3}),(?<b>\d{1,3})\)))/", $line, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                if ($match[0] === 'do()') {
                    $do = true;
                    continue;
                }
                if ($match[0] === "don't()") {
                    $do = false;
                    continue;
                }
                if (!$do || !isset($match['b']) || !isset($match['b'])) {
                    continue;
                }
                $answer += $match['a'] * $match['b'];
            }
        }
        return new Result(Result::PART2, $answer);
    }
}