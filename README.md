# **laravel-round-robin**

[![Latest Stable Version](https://poser.pugx.org/tonystore/livewire-interactions/v)](https://packagist.org/packages/tonystore/livewire-interactions)  [![Total Downloads](http://poser.pugx.org/tonystore/livewire-interactions/downloads)](https://packagist.org/packages/tonystore/livewire-interactions)  [![License](http://poser.pugx.org/tonystore/livewire-interactions/license)](https://packagist.org/packages/tonystore/livewire-interactions)  [![PHP Version Require](http://poser.pugx.org/tonystore/livewire-interactions/require/php)](https://packagist.org/packages/tonystore/livewire-permission)

Laravel package to generate sweepstakes using the Round Robin algorithm. Supports any number of teams, as long as they are greater than a minimum value specified in the configuration file. Built with Laravel Collections for better handling of arrays.
## **REQUIREMENTS**

-   [PHP >= 7.2](http://php.net)
-   [Laravel 7 | 8 | 9 ](https://laravel.com)

## **INSTALLATION VIA COMPOSER**

### Step 1: Composer

Run this command line in console.
``` bash
composer require tonystore/laravel-round-robin
```
### Step 2: Publish Config File
#### Publish Config File
``` bash
php artisan vendor:publish --provider="Tonystore\LaravelRoundRobin\LaravelRoundRobinProvider" --tag=round-robin
``` 
In your configuration file you can define the following:

``` php
<?php

return [
     /**
     *----------------------------------------------------------------------
     * Minimum number of teams to generate a schedule
     *----------------------------------------------------------------------
     */
    'min_teams' => 2,
    /**
     *----------------------------------------------------------------------
     * Custom name for the first phase of the schedule.
     *----------------------------------------------------------------------
     */
    'one_phase' => 'one',
    /**
     *----------------------------------------------------------------------
     * Custom name for the return phase of the schedule.
     *----------------------------------------------------------------------
     */
    'way_phase' => 'way',
]
``` 

## **Usage**
If you pass it an array of equipment, it will return an array, which contains an array of objects. For each object you will have the home team, away team and the round it belongs to:
```php
['BSC','CSE','LDU','IDV'];

[
  [
    {
      "phase": "one",
      "round": 1,
      "local": "BSC",
      "visitor": "IDV"
    },
    {
      "phase": "one",
      "round": 1,
      "local": "LDU",
      "visitor": "CSE"
    }
  ],
  [
    {
      "phase": "one",
      "round": 2,
      "local": "IDV",
      "visitor": "LDU"
    },
    {
      "phase": "one",
      "round": 2,
      "local": "BSC",
      "visitor": "CSE"
    }
  ],
  [
    {
      "phase": "one",
      "round": 3,
      "local": "CSE",
      "visitor": "IDV"
    },
    {
      "phase": "one",
      "round": 3,
      "local": "BSC",
      "visitor": "LDU"
    }
  ],
]
```
You can use this package in several ways:
```php
$teams = ['BSC','CSE','CUMBAYA','U. CATOLICA','LDU','AUCAS','GUALACEO','ORENSE','CITY','TU','D. CUENCA','MUSHUCRUNA','IDV','DELFIN','MACARA','9 DE OCTUBRE'];

$schedule = new RoundRobin($teams);
$schedule->schedule();
```
With static function:
```php
$teams = ['BSC','CSE','CUMBAYA','U. CATOLICA','LDU','AUCAS','GUALACEO','ORENSE','CITY','TU','D. CUENCA','MUSHUCRUNA','IDV','DELFIN','MACARA','9 DE OCTUBRE'];

$schedule = RoundRobin::addTeams($teams)->schedule();
```
OR
```php
$teams = ['BSC','CSE','CUMBAYA','U. CATOLICA','LDU','AUCAS','GUALACEO','ORENSE','CITY','TU','D. CUENCA','MUSHUCRUNA','IDV','DELFIN','MACARA','9 DE OCTUBRE'];

$schedule = RoundRobin::makeSchedule($teams, null, true, null, false);
```

With a helper function:
```php
$teams = ['BSC','CSE','CUMBAYA','U. CATOLICA','LDU','AUCAS','GUALACEO','ORENSE','CITY','TU','D. CUENCA','MUSHUCRUNA','IDV','DELFIN','MACARA','9 DE OCTUBRE'];

schedule($teams, null, true, null, false);
```

To generate the schedule without shuffling the teams:
```php
$teams = ['BSC','CSE','CUMBAYA','U. CATOLICA','LDU','AUCAS','GUALACEO','ORENSE','CITY','TU','D. CUENCA','MUSHUCRUNA','IDV','DELFIN','MACARA','9 DE OCTUBRE'];

$schedule = RoundRobin::addTeams($teams)->doNotShuffle()->schedule();
```
To generate the calendar I shuffle the teams with a seed:
```php
$teams = ['BSC','CSE','CUMBAYA','U. CATOLICA','LDU','AUCAS','GUALACEO','ORENSE','CITY','TU','D. CUENCA','MUSHUCRUNA','IDV','DELFIN','MACARA','9 DE OCTUBRE'];

$schedule = RoundRobin::addTeams($teams)->shuffle(12)->schedule();
```
Any of the available options will generate a collection of ready-made rounds, which you can manipulate at will.

To generate the schedule without shuffling the teams:
```php
$teams = ['BSC','CSE','CUMBAYA','U. CATOLICA','LDU','AUCAS','GUALACEO','ORENSE','CITY','TU','D. CUENCA','MUSHUCRUNA','IDV','DELFIN','MACARA','9 DE OCTUBRE'];

$schedule = RoundRobin::addTeams($teams)->schedule();

$schedule->first(); //It will return the first round available on the connection.

$schedule->last(); //It will return the last round available on the connection.
```

In the same way you can use all the options available in Laravel Collections. Additionally we added a collection to convert rounds to objects, you can use it in the following way.
```php
$teams = ['BSC','CSE','CUMBAYA','U. CATOLICA','LDU','AUCAS','GUALACEO','ORENSE','CITY','TU','D. CUENCA','MUSHUCRUNA','IDV','DELFIN','MACARA','9 DE OCTUBRE'];

$schedule = RoundRobin::addTeams($teams)->schedule()->toObject();

$schedule[0][0]->local; //It will return the name of the home team, of the first game, of the first available round.
```

There is also the option to generate round-trip rounds. Example:
```php
$teams = ['BSC','CSE','CUMBAYA','U. CATOLICA','LDU','AUCAS','GUALACEO','ORENSE','CITY','TU','D. CUENCA','MUSHUCRUNA','IDV','DELFIN','MACARA','9 DE OCTUBRE'];

$schedule = RoundRobin::addTeams($teams)->doubleRound()->schedule();
```


You can filter the calendar by the available phases. Example:

```php
$teams = ['BSC','CSE','CUMBAYA','U. CATOLICA','LDU','AUCAS','GUALACEO','ORENSE','CITY','TU','D. CUENCA','MUSHUCRUNA','IDV','DELFIN','MACARA','9 DE OCTUBRE'];

$schedule = RoundRobin::addTeams($teams)->doubleRound()->schedule(); 

$firstLeg = $schedule->firstLeg(); //Will return only for the first leg.

$secondLeg = $schedule->secondLeg(); //Will return only for the second leg.

```

# **Coming soon: Laravel Tournaments**
