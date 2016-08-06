<?php

namespace App\Console\Commands;

use App\Events\Version;
use Illuminate\Console\Command;

class VersionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:version';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Broadcast a version message';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        event(new Version);
    }
}
