<?php
namespace Mintopia\Aoc2024;

use Mintopia\Aoc2024\Helpers\Result;

class Day5 extends Day
{
    protected array $updates = [];
    protected array $rules = [];

    protected function loadData(): void
    {
        $inputFilename = $this->getInputFilename();
        $data = file($inputFilename, FILE_IGNORE_NEW_LINES);

        $rules = true;
        foreach ($data as $line) {
            if ($line === '') {
                $rules = false;
                continue;
            }
            if ($rules) {
                $this->rules[] = explode('|', $line);
            } else {
                $this->updates[] = explode(',', $line);
            }
        }
    }

    protected const TITLE = 'Print Queue';

    protected function part1(): Result
    {
        $answer = 0;
        $wrong = [];
        foreach ($this->updates as $update) {
            foreach ($this->rules as $rule) {
                $pos1 = array_search($rule[0], $update);
                $pos2 = array_search($rule[1], $update);
                if ($pos1 === false || $pos2 === false) {
                    continue;
                }
                if ($pos1 < $pos2) {
                    continue;
                }
                $wrong[] = $update;
                continue 2;
            }
            $mid = ceil(count($update) / 2);
            $answer += $update[$mid - 1];
        }
        return new Result(Result::PART1, $answer, $wrong);
    }

    protected function part2(Result $part1): Result
    {
        $wrong = $part1->carry;
        $answer = 0;
        foreach ($wrong as $update) {
            for ($i = 0; $i < count($this->rules); $i++) {
                $rule = $this->rules[$i];
                $pos1 = array_search($rule[0], $update);
                $pos2 = array_search($rule[1], $update);
                if ($pos1 === false || $pos2 === false) {
                    continue;
                }
                if ($pos1 < $pos2) {
                    continue;
                }
                // Wrong, re-order, reset rules to 0
                $update[$pos1] = $rule[1];
                $update[$pos2] = $rule[0];
                $i = 0;
            }
            $mid = ceil(count($update) / 2);
            $answer += $update[$mid - 1];
        }
        return new Result(Result::PART2, $answer);
    }
}