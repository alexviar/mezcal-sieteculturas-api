<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Schedule::command('queue:work --tries=1 --stop-when-empty')
  ->withoutOverlapping()
  ->everyThirtySeconds()
  ->appendOutputTo(storage_path('logs/queue.log'));

Schedule::command("purchases:clean-receipts")->everyFifteenMinutes();
