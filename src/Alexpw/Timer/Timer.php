<?php
/* vim: set shiftwidth=4 expandtab softtabstop=4: */

namespace Alexpw\Timer;

/**
 * A basic timer for benchmarking.
 */
class Timer
{
    protected $runs;
    protected $max_label_length = 0;
    protected $total = 0;

    public function __construct()
    {
        $this->runs = new \SplObjectStorage;
    }

    /**
     * @param string $label
     */
    public function start($label)
    {
        $this->max_label_length = max(strlen($label), $this->max_label_length);

        $run = new Run;
        $run->label        = $label;
        $run->start_memory = memory_get_usage();
        $run->start_time   = microtime(true);
        return $run;
    }

    /**
     * @param Run $run
     */
    public function end(Run $run)
    {
        $end         = microtime(true);
        $mem         = memory_get_usage() - $run->start_memory;
        $run->time   = bcsub($end, $run->start_time, 6) * 1000;
        $run->memory = $this->memoryToString($mem);
        $this->total = bcadd($this->total, $run->time);

        $this->runs->attach($run);
    }

    /**
     * @return SplObjectStorage
     */
    public function getRuns()
    {
        return $this->runs;
    }

    /**
     * Render as a string for the console.
     * @return string
     */
    public function render()
    {
        $max_label_length = $this->max_label_length + 5;
        $dashes = str_repeat('-', $max_label_length + 39) . "\n";
        $margin = str_repeat(' ', 2);

        $header = sprintf(
            "%s %-{$max_label_length}s %-12s %-12s %s\n",
            $margin, 'label', 'time (ms)', 'perc', 'memory'
        );

        $body = '';
        foreach ($this->runs as $run) {
            $body .= sprintf(
                "%s %-{$max_label_length}s %-10s %6s %14s\n",
                $margin,
                $run->label,
                $run->time,
                number_format(($run->time * 100) / $this->total, 2, '.', ''),
                $run->memory
            );
        }
        return $header . $dashes . $body . $dashes;
    }

    /**
     * Convert memory to a human readable string for the console.
     * @param int $mem In bytes
     * @return string
     */
    protected function memoryToString($mem)
    {
        $abs = abs($mem);
        if ($abs < 1024) {
            return "$mem Bytes";
        } elseif ($abs < 1048576) {
            return round($mem / 1024, 2)." KB";
        } else {
            return round($mem / 1048576, 2)." MB";
        }
    }
}
