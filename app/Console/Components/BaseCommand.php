<?php

namespace App\Console\Components;

use Illuminate\Console\Command;
use Log;

abstract class BaseCommand extends Command
{
    const EXIT_CODE_ERROR = 0;
    const EXIT_CODE_NORMAL = 1;

    protected static $category = 'console';

    protected $beginTime;

    protected static function logCategory()
    {
        $path = storage_path(sprintf('/logs/%s.log', static::category()));
        Log::useDailyFiles($path, 3, 'warning');
    }

    protected static function category()
    {
        return static::$category;
    }

    public function info2($msg)
    {
        Log::info($msg);
        $this->info($msg);
    }

    public function error2($msg)
    {
        Log::error($msg);
        $this->error($msg);
    }

    public function handle()
    {
        $this->beginTime = microtime(true);

        $this->beforeAction();

        $this->init();

        $this->handle2();

        $this->afterAction();
    }

    public function handle2()
    {
        return true;
    }

    /**
     * 添加指定 log 路径
     */
    public function beforeAction()
    {
        static::logCategory();
    }

    public function afterAction()
    {
        $this->info2(sprintf("Ended handle %s. Duration %.3fs.", static::$category, microtime(true) - $this->beginTime));
    }

    protected function init()
    {
        return true;
    }
}
