<?php
namespace Mintopia\Aoc2024;

use Mintopia\Aoc2024\Helpers\Result;

class Day8 extends Day
{
    protected array $nodes = [];
    protected function loadData(): void
    {
        $this->loadGridFromData();
        foreach ($this->data as $r => $row) {
            foreach ($row as $c => $char) {
                if ($char === '.') {
                    continue;
                }
                if (!array_key_exists($char, $this->nodes)) {
                    $this->nodes[$char] = [];
                }
                $this->nodes[$char][] = [$r, $c];
            }
        }
    }

    protected const TITLE = 'Resonant Collinearity';

    protected function part1(): Result
    {
        $antiNodes = [];
        $allNodes = [];
        foreach ($this->nodes as $locations) {
            if (count($locations) <= 1) {
                continue;
            }
            foreach ($locations as [$row, $col]) {
                foreach ($locations as [$row2, $col2]) {
                    if ([$row, $col] === [$row2, $col2]) {
                        // Same node, do nothing
                        continue;
                    }

                    $rowDelta = $row2 - $row;
                    $colDelta = $col2 - $col;

                    $antiNode = [$row2, $col2];
                    if (!in_array($antiNode, $allNodes)) {
                        $allNodes[] = $antiNode;
                    }
                    $first = true;
                    do {
                        $antiNode = [$antiNode[0] + $rowDelta, $antiNode[1] + $colDelta];
                        if (!isset($this->data[$antiNode[0]][$antiNode[1]])) {
                            break;
                        }
                        if ($first) {
                            $first = false;
                            if (!in_array($antiNode, $antiNodes)) {
                                $antiNodes[] = $antiNode;
                            }
                        }
                        if (!in_array($antiNode, $allNodes)) {
                            $allNodes[] = $antiNode;
                        }
                    } while (true);
                }
            }
        }
        $answer = count($antiNodes);
        $answer2 = count($allNodes);
        return new Result(Result::PART1, $answer, $answer2);
    }

    protected function part2(Result $part1): Result
    {
        return new Result(Result::PART2, $part1->carry);
    }
}