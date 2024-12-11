<?php
namespace Mintopia\Aoc2024;

use Mintopia\Aoc2024\Helpers\Result;

class Day9 extends Day
{
    protected const TITLE = 'Disk Fragmenter';

    protected array $disk = [];
    protected array $spaces = [];
    protected array $files = [];

    protected function loadData(): void
    {
        parent::loadData();
        $this->data = str_split($this->data[0]);
        for ($i = 0; $i < count($this->data); $i += 2) {
            $index = $i / 2;
            $start = count($this->disk);
            $this->files[] = (object)[
                'start' => $start,
                'index' => $index,
                'size' => $this->data[$i],
            ];
            for ($j = 0; $j < $this->data[$i]; $j++) {
                $this->disk[] = $index;
            }
            if (isset($this->data[$i + 1])) {
                $start = count($this->disk);
                $this->spaces[$start] = (int)$this->data[$i + 1];
                for ($j = 0; $j < $this->data[$i + 1]; $j++) {
                    $this->disk[] = '.';
                }
            }
        }
    }

    protected function part1(): Result
    {
        $disk = $this->disk;
        foreach ($this->spaces as $offset => $length) {
            for ($i = 0; $i < $length; $i++) {
                do {
                    $fileBlock = array_pop($disk);
                } while ($fileBlock === '.');
                $disk[$offset + $i] = $fileBlock;
            }
        }
        $answer = 0;
        foreach (array_values($disk) as $position => $fileId) {
            $answer += ($position * $fileId);
        }
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $answer = 0;
        $spaces = $this->spaces;
        $files = $this->files;
        usort($files, function ($alpha, $bravo) {
            return $bravo->index <=> $alpha->index;
        });
        $moved = [];
        foreach ($files as $file) {
            if ($this->isTest) {
                $this->displayDisk($files, $spaces);
            }
            ksort($spaces);
            if (in_array($file, $moved)) {
                continue;
            }
            foreach ($spaces as $start => $space) {
                if ($start >= $file->start) {
                    continue 2;
                }
                if ($file->size <= $space) {
                    $moved[] = $file;
                    // Move this file
                    $spaces[$file->start] = $file->size;
                    $file->start = $start;
                    unset($spaces[$start]);
                    if ($file->size < $space) {
                        $spaces[$start + $file->size] = $space - $file->size;
                    }
                    continue 2;
                }
            }
        }

        if ($this->isTest) {
            $this->displayDisk($files, $spaces);
        }
        $answer = 0;
        $disk = $this->getDisk($files, $spaces);
        foreach ($disk as $position => $value) {
            if ($value === '.') {
                continue;
            }
            $answer += $position * $value;
        }
        return new Result(Result::PART2, $answer);
    }

    protected function getDisk(array $files, array $spaces): array
    {
        $disk = [];
        foreach ($files as $file) {
            for ($i = 0; $i < $file->size; $i++) {
                $disk[$file->start + $i] = $file->index;
            }
        }

        foreach ($spaces as $start => $size) {
            for ($i = 0; $i < $size; $i++) {
                $disk[$start + $i] = '.';
            }
        }
        ksort($disk);
        return $disk;
    }
    protected function displayDisk(array $files, array $spaces): void
    {
        $disk = $this->getDisk($files, $spaces);
        echo implode('', $disk) . "\r\n";
    }
}