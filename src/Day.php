<?php
namespace Mintopia\Aoc2024;

use Mintopia\Aoc2024\Helpers\Result;
use Mintopia\Aoc2024\Helpers\Timing;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class Day extends Command
{
    protected const TITLE = null;

    protected InputInterface $input;
    protected OutputInterface $output;
    protected SymfonyStyle $io;

    protected int $dayNumber;
    protected array|string $data = [];

    protected bool $isTest = false;
    protected bool $isBenchmark = false;
    protected bool $hasVisualisation = false;

    public function __construct(string $name = null)
    {
        $className = get_class($this);
        $this->dayNumber = (int) str_replace('Mintopia\Aoc2024\Day', '', $className);
        if ($name === null) {
            $name = "day{$this->dayNumber}";
        }
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription("Advent of Code Day {$this->dayNumber}");
        $this->addOption('test', 't',  InputOption::VALUE_NONE, 'Use test data');
        $this->addOption('benchmark', 'b',  InputOption::VALUE_NONE, 'Benchmark');
        $this->addOption('iterations', 'i',  InputOption::VALUE_OPTIONAL, 'Iterations for benchmark', 100);
        $this->addOption('part1', 'p', InputOption::VALUE_NONE, 'Only execute part 1');
        $this->addOption('noperformance', null, InputOption::VALUE_NONE, 'No performance output');
        if ($this->hasVisualisation) {
            $this->addOption('visualise', null, InputOption::VALUE_NONE, 'Enable visualisation');
        }
    }

    protected function getInputFilename(): string
    {
        if ($this->input->getOption('test')) {
            return "testdata/input/day{$this->dayNumber}.txt";
        } else {
            return "input/day{$this->dayNumber}.txt";
        }
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($this->input, $this->output);

        $title = '';
        if (static::TITLE !== null) {
            $title = ' - ' . static::TITLE;
        }
        $this->io->block("Advent of Code: Day {$this->dayNumber}{$title}", null, 'fg=black;bg=cyan', ' ', true);

        if ($this->input->getOption('test')) {
            $this->isTest = true;
            $this->io->warning('Using test data');
        }
        if ($this->input->getOption('benchmark')) {
            $iterations = $this->input->getOption('iterations');
            $this->io->title("Benchmarking: {$iterations} iterations");
            $this->isBenchmark = true;
            $timings = [];
            for ($i = 0; $i < $iterations; $i++) {
                $timings[] = $this->executeDay();
            }

            if (!$this->input->getOption('noperformance')) {
                $timing = new Timing();
                $timing->getAverages($timings);
                $timing->render($this->io);
            }
        } else {
            $timing = $this->executeDay();

            if (!$this->input->getOption('noperformance')) {
                $timing->render($this->io);
                $this->io->writeln(" Peak Memory Usage: <fg=green>{$this->getMemoryUsage()}</>");
                $this->io->writeln("");
            }
        }
        return Command::SUCCESS;
    }

    protected function getMemoryUsage(): string
    {
        $bytes = memory_get_peak_usage();
        $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.2f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

    protected function executeDay(): Timing
    {
        $timing = new Timing;
        $start = hrtime(true);
        $this->loadData();

        $timing->dataLoading = hrtime(true) - $start;

        if (!$this->isBenchmark) {
            $this->io->title('Part 1');
        }

        $start = hrtime(true);
        $result = $this->part1();
        $timing->part1 = hrtime(true) - $start;

        $this->processResult($result);

        if ($this->input->getOption('part1')) {
            return $timing;
        }

        if (!$this->isBenchmark) {
            $this->io->title('Part 2');
        }

        $start = hrtime(true);
        $result = $this->part2($result);
        $timing->part2 = hrtime(true) - $start;

        $this->processResult($result);
        return $timing;
    }

    protected function processResult(Result $result): void
    {
        if (!$this->isBenchmark) {
            $this->io->writeln([
                '',
                "Answer: <fg=cyan>{$result->value}</>"
            ]);
        }

        if (!$this->input->getOption('test')) {
            return;
        }

        // Get our known result
        [$part1Answer, $part2Answer] = file("testdata/output/day{$this->dayNumber}.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($result->part === Result::PART1) {
            $assert = $part1Answer;
        } else {
            $assert = $part2Answer;
        }

        // Assertion
        if ($result->value == $assert) {
            if (!$this->isBenchmark) {
                $this->io->success("Answer matches expected: {$assert}");
            }
        } else {
            $this->io->error("Answer does not match expected: {$assert}");
        }
    }

    protected function getOptionalOutput(): ?OutputInterface
    {
        if (!$this->isBenchmark) {
            return $this->output;
        }
        return null;
    }

    protected function loadData(): void
    {
        $this->data = $this->getArrayFromInputFile();
    }

    protected function loadGridFromData(int $length = 1): void
    {
        $data = $this->getArrayFromInputFile();
        $this->data = array_map(function ($line) use ($length) {
            return str_split($line, $length);
        }, $data);
    }

    protected function sparseMap(array $data, array $chars = ['.', ' ']): array
    {
        $map = [];
        foreach ($data as $row => $cols) {
            foreach ($cols as $col => $char) {
                if (!in_array($char, $chars)) {
                    if (!isset($map[$row])) {
                        $map[$row] = [];
                    }
                    $map[$row][$col] = $char;
                }
            }
        }
        return $map;
    }

    protected function getArrayFromInputFile(): array
    {
        $inputFilename = $this->getInputFilename();
        return file($inputFilename, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
    }

    protected function getInputFile(): string
    {
        $inputFilename = $this->getInputFilename();
        return file_get_contents($inputFilename);
    }

    abstract protected function part1(): Result;
    abstract protected function part2(Result $part1): Result;
}