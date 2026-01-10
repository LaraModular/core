<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('telescope:prune')->dailyAt('03:00');
Schedule::command('activitylog:clean')->dailyAt('03:00');
