<?php
namespace Mintopia\Aoc2024;

use Mintopia\Aoc2024\Helpers\Result;

class Day7 extends Day
{
    protected array $equations = [];
    protected array $combinationLookup = [];
    protected function loadData(): void
    {
        parent::loadData();
        foreach ($this->data as $line) {
            [$result, $numbers] = explode(': ', $line);
            $this->equations[] = (object)[
                'result' => $result,
                'numbers' => explode(' ', $numbers),
            ];
        }
    }

    protected function solve(object $equation, array $operators, $required = []): ?int
    {
        $positions = count($equation->numbers) - 1;
        $opsMatrix = $this->getCombinations($operators, $positions);
        foreach ($opsMatrix as $operations) {
            if ($required && count(array_intersect($operations, $required)) === 0) {
                continue;
            }
            $result = $equation->numbers[0];
            for ($i = 1; $i < count($equation->numbers); $i++) {
                $num = $equation->numbers[$i];
                switch ($operations[$i - 1]) {
                    case '*':
                        $result *= $num;
                        break;
                    case '+':
                        $result += $num;
                        break;
                    case '||':
                        $result = (int)"{$result}{$num}";
                        break;
                    default:
                        break;
                }
                if ($result > $equation->result) {
                    continue 2;
                }
            }
            if ($result == $equation->result) {
                return $result;
            }
        }
        return null;
    }

    protected function getCombinations($operators, $length): array {
        $key = implode('.', $operators);
        if (!isset($this->combinationLookup[$key][$length])) {
            $this->combinationLookup[$key][$length] = $this->getCombination($operators, $length);
        }
        return $this->combinationLookup[$key][$length];
    }

    protected function getCombination($operators, $length, $current = [], $results = []): array
    {
        if (count($current) === $length) {
            $results[] = $current;
            return $results;
        }
        foreach ($operators as $op) {
            $stack = $current;
            $stack[] = $op;
            $results = $this->getCombination($operators, $length, $stack, $results);
        }
        return $results;
    }

    protected const TITLE = 'Bridge Repair';

    protected function part1(): Result
    {
        $answer = 0;
        $unsolved = [];
        foreach ($this->equations as $equation) {
            if ($this->solve($equation, ['*', '+']) !== null) {
                $answer += $equation->result;
            } else {
                $unsolved[] = $equation;
            }
        }
        return new Result(Result::PART1, $answer, $unsolved);
    }

    protected function part2(Result $part1): Result
    {
        $answer = $part1->value;
        foreach ($part1->carry as $equation) {
            if ($this->solve($equation, ['*', '+', '||'], ['||']) !== null) {
                $answer += $equation->result;
            }
        }
        return new Result(Result::PART2, $answer);
    }
}