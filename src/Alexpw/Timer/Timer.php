<?php
/* vim: set shiftwidth=4 expandtab softtabstop=4: */

namespace Alexpw\Timer;

class Timer
{
    protected $timers;
    protected $maxNameLength = 0;
    protected $total = 0;

    public function __construct()
    {
        $this->timers = new \SplObjectStorage;
    }

    public function start($name)
    {
        $this->maxNameLength = max(strlen($name), $this->maxNameLength);

        $run = new Runner;
        $run->name         = $name;
        $run->startMemory  = memory_get_usage();
        $run->startTime    = microtime(true);
        return $run;
    }

    public function end(Runner $run)
    {
        $end         = microtime(true);
        $mem         = memory_get_usage() - $run->startMemory;
        $run->time   = bcsub($end, $run->startTime, 6) * 1000;
        $run->mem    = $this->memoryToString($mem);
        $this->total = bcadd($this->total, $run->time);

        $this->timers->attach($run);
    }

    public function __toString()
    {
        return $this->render();
    }

    public function render()
    {
        $marginLength = 3;
        $margin = str_repeat(' ', $marginLength);

        $maxNameLength = $this->maxNameLength + 7;
        $dashes = str_repeat('-', $maxNameLength + 35 + $marginLength);

        $out = '';
        $out .= $margin .
                str_pad('timer', $maxNameLength) .
                str_pad("time (ms)", 12) .
                str_pad("perc ", 12) .
                str_pad("mem", 6, ' ', STR_PAD_LEFT) .
                "\n";

        $out .= "$dashes\n";

        foreach ($this->timers as $run) {

            $perc = number_format(
                ($run->time * 100) / $this->total,
                2,
                '.',
                ''
            );
            $out .= $margin .
                    str_pad($run->name, $maxNameLength, ' ') .
                    str_pad($run->time, 10) .
                    str_pad($perc, 6, ' ', STR_PAD_LEFT) .
                    str_pad($run->mem, 14, ' ', STR_PAD_LEFT) .
                    "\n";
        }
        $out .= "$dashes\n";
        return $out;
    }

    protected function memoryToString($mem)
    {
        $abs = abs($mem);
        if ($abs < 1024) {
            return "$mem Bytes";
        } else if ($abs < 1048576) {
            return round($mem / 1024, 2)." KB";
        } else {
            return round($mem / 1048576, 2)." MB";
        }
    }
}
