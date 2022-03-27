<?php

namespace Tonystore\LaravelRoundRobin\Services;


use Exception;
use Illuminate\Support\Collection;

class RoundRobin
{
    /**
     * Teams to get Schedule
     *
     * @var Collection
     */
    protected Collection $teams;
    /**
     * Schedule
     *
     * @var Collection
     */
    protected Collection $schedule;
    /**
     * Shuffle
     *
     * @var boolean
     */
    protected bool $shuffle = true;
    /**
     * @var int|null
     */
    protected $seed = null;
    /**
     * @var int|null How many rounds to generate
     */
    protected $rounds = null;
    /**
     * Get the teams to generate schedule
     *
     * @param array $teams
     */
    public function __construct(array $teams)
    {
        $this->teams = collect($teams);
        $this->schedule = collect();
    }
    public static function addTeams(array $teams): RoundRobin
    {
        if (empty($teams) || count($teams) < config('round-robin.min_teams')) {
            throw new Exception('You need at least 2 teams to generate the calendar.');
        }
        $instance = new static($teams);
        return $instance;
    }
    /**
     * Generate schedule
     *
     * @return Collection
     */
    public function schedule(): Collection
    {
        if ($this->teams->isEmpty() || $this->teams->count() < config('round-robin.min_teams')) {
            throw new Exception('You need at least 2 teams to generate the calendar.');
        }
        $this->checkForOdd();
        $this->doShuffle();
        $this->buildSchedule();
        $this->cleanSchedule();

        return $this->schedule;
    }
    /**
     * Generate the schedule in a single function.
     *
     * @param array $teams
     * @param integer|null $rounds
     * @param boolean $shuffle
     * @param boolean $doubleRound
     * @return Collection
     */
    public static function makeSchedule(array $teams, int $rounds = null, bool $shuffle = true, int $seed = null, bool $doubleRound = false): Collection
    {
        $instance = static::addTeams($teams);
        if (!is_null($rounds)) {
            $instance->rounds = $rounds;
        }
        if (!$shuffle) {
            $instance->doNotShuffle();
        }
        if (!is_null($seed)) {
            $instance->seed = $seed;
        }
        if ($doubleRound) {
            $instance->doubleRound();
        }
        return $instance->schedule();
    }
    /**
     * Check if there are odd numbers of teams.
     *
     * @return void
     */
    protected function checkForOdd(): void
    {
        if ($this->teams->count() % 2 === 1) {
            $this->teams->push(null);
        }
    }
    /**
     * Shuffle teams.
     *
     * @return void
     */
    protected function doShuffle(): void
    {
        if ($this->shuffle) {
            $this->teams = collect($this->teams->shuffle($this->seed));
        }
    }
    public function doubleRound()
    {
        $this->rounds = (($count = $this->teams->count()) % 2 === 0 ? $count - 1 : $count) * 2;

        return $this;
    }

    /**
     * Generate Schedule
     *
     * @return RoundRobin
     */
    protected function buildSchedule(): RoundRobin
    {
        $teamsCount = $this->teams->count();
        $halfTeamCount = $teamsCount / 2;
        $rounds = $this->rounds ?? $teamsCount - 1;
        for ($round = 1; $round <= $rounds; $round += 1) {
            $fase = $round > ($teamsCount - 1) ? config('round-robin.way_phase') :  config('round-robin.one_phase');
            $this->schedule[$round] = collect();
            $this->teams->each(function ($team, $index) use ($halfTeamCount, $round, $fase) {
                if ($index >= $halfTeamCount) {
                    return false;
                }
                $team1 = $team;
                $team2 = $this->teams[$index + $halfTeamCount];
                $matchup = $round % 2 === 0 ? collect(['phase' =>  $fase, 'round' => $round, 'local' => $team1, 'visitor' => $team2]) : collect(['phase' =>  $fase, 'round' => $round, 'local' => $team2, 'visitor' => $team1]);
                $this->schedule[$round]->push($matchup);
            });
            $this->rotate();
        }
        return $this;
    }
    /**
     * Rotate array items according to the round-robin algorithm.
     *
     * @return RoundRobin
     */
    protected function rotate(): RoundRobin
    {
        $teamsCount = $this->teams->count();
        if ($teamsCount < 3) {
            return $this;
        }
        $lastIndex = $teamsCount - 1;
        $factor = (int) ($teamsCount % 2 === 0 ? $teamsCount / 2 : ($teamsCount / 2) + 1);
        $topRightIndex = $factor - 1;
        $topRightItem = $this->teams[$topRightIndex];
        $bottomLeftIndex = $factor;
        $bottomLeftItem = $this->teams[$bottomLeftIndex];
        for ($i = $topRightIndex; $i > 0; $i -= 1) {
            $this->teams[$i] = $this->teams[$i - 1];
        }
        for ($i = $bottomLeftIndex; $i < $lastIndex; $i += 1) {
            $this->teams[$i] = $this->teams[$i + 1];
        }
        $this->teams[1] = $bottomLeftItem;
        $this->teams[$lastIndex] = $topRightItem;

        return $this;
    }
    /**
     * Eliminate all matches where they have an empty team.
     *
     * @return RoundRobin
     */
    protected function cleanSchedule(): RoundRobin
    {
        $this->schedule = $this->schedule->transform(function ($rondas, $key) {
            return $rondas->filter(function ($ronda) {
                return !is_null($ronda->get('local')) && !is_null($ronda->get('visitor'));
            })->values();
        })->values();
        return $this;
    }
    /**
     * Shuffle teams
     *
     * @return RoundRobin
     */
    public function shuffle($seed = null)
    {
        $this->shuffle = true;
        $this->seed = $seed;

        return $this;
    }
    /**
     * Do not shuffle teams
     *
     * @return RoundRobin
     */
    public function doNotShuffle()
    {
        $this->shuffle = false;

        return $this;
    }
}
