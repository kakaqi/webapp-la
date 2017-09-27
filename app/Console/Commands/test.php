<?php
/**
 * Created by PhpStorm.
 * User: chensongjian
 * Date: 2017/7/13
 * Time: 13:34
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
class test extends Command
{
    protected $signature = 'test';
    protected $description = 'this is a test1';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        echo '123';
    }
}