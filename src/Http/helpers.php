<?php

use Tonystore\LaravelRoundRobin\Services\RoundRobin;

if (!function_exists('schedule')) {
    function schedule(array $teams, int $rounds = null, bool $shuffle = true, int $seed = null, bool $doubleRound = false)
    {
        return RoundRobin::makeSchedule($teams, $rounds, $shuffle, $seed, $doubleRound);
    }
}
