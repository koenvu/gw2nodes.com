<?php

namespace App\Console\Commands;

use App\Node;
use GW2Treasures\GW2Api\GW2Api;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class ApiBuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if there is a new build available.';

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
        $gw2 = app()->make(GW2Api::class);

        $build = $gw2->build()->get();
        $maxBuild = Node::max('build_id');

        $servers = Node::select('server')->pluck('server')->unique()->filter(function ($elem) {
            return preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $elem) === 1;
        });

        Node::whereIn('server', $servers)->where('is_permanent', '=', 0)->where('build_id', '<', $build)->delete();

        if ($build > $maxBuild) {
            $this->info('New build! '.$build);

            Node::where('is_permanent', '=', 1)->update(['build_id' => $build]);
            Mail::raw('New build available: '.$build, function ($message) {
                $message->to('gw2nodes@ehlo-localhost.com');
                $message->from('root@gw2nodes.com');
                $message->subject('GW2 Updated');
            });
            Cache::tags(['server', 'node'])->flush();
        }
    }
}
