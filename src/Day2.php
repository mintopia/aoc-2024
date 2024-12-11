<?php
namespace Mintopia\Aoc2024;

use Mintopia\Aoc2024\Helpers\Result;

class Day2 extends Day
{
    protected const TITLE = 'Red-Nosed Reports';

    protected function isReportSafe(string $report): bool
    {
        $levels = explode(' ', $report);
        for ($i = 1; $i < count($levels); $i++) {
            $current = $levels[$i];
            $prev = $levels[$i - 1] ?? null;
            $next = $levels[$i + 1] ?? null;

            if ($next) {
                if ($current < $prev && $current < $next) {
                    return false;
                }
                if ($current > $next && $current > $prev) {
                    return false;
                }
            }
            $diff = abs($current - $prev);
            if ($diff < 1 || $diff > 3) {
                return false;
            }
        }
        return true;
    }
    protected function part1(): Result
    {
        $safe = 0;
        foreach ($this->data as $report) {
            if ($this->isReportSafe($report)) {
                $safe++;
            }
        }
        return new Result(Result::PART1, $safe);
    }

    protected function isDampenerSafe(string $report): bool
    {
        $levels = explode(' ', $report);
        for ($i = 0; $i < count($levels); $i++) {
            $toCheck = $levels;
            unset($toCheck[$i]);
            if ($this->isReportSafe(implode(' ', $toCheck))) {
                return true;
            }
        }
        return false;
    }

    protected function part2(Result $part1): Result
    {
        $safe = 0;
        foreach ($this->data as $report) {
            if ($this->isReportSafe($report)) {
                $safe++;
            } elseif ($this->isDampenerSafe($report)) {
                $safe++;
            }
        }
        return new Result(Result::PART2, $safe);
    }
}