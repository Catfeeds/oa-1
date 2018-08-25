<?php

namespace App\Console\Commands;

use App\Console\Components\BaseCommand;

use App\Models\Gm\RequestLog;
class TestCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test';

    public function handle()
    {
        dd(123);
    }
}
