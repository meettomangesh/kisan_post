<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Helper\EmailHelper;
use App\Models\PromoCodes;

class ExpirationOfPromoCodes extends Command
{
    use Dispatchable;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expirationofpromocodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $startTime = date('Y-m-d H:i:s');
        $this->comment(PHP_EOL . "expirationofpromocodes started at :" . $startTime . PHP_EOL);

        $promoCodes = new PromoCodes();
        $promoCodes->markPromoCodeAsExpired();
        
        $endTime = date('Y-m-d H:i:s');
        $this->comment(PHP_EOL . "expirationofpromocodes ended at :" . $endTime . PHP_EOL);
        Log::info('command expirationofpromocodes Call.', ['startTime' => $startTime, 'endTime' => $endTime, 'comment' => '']);
    }
}
