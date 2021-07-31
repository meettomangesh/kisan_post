<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Helper\EmailHelper;

class OrderDeliveryDay extends Command
{
    use Dispatchable;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orderdeliveryday';

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
        $this->comment(PHP_EOL . "orderdeliveryday started at :" . $startTime . PHP_EOL);

        $orderData = $this->getOrderDeliveryDay();
        foreach($orderData as $order) {
            //
        }
        
        $endTime = date('Y-m-d H:i:s');
        $this->comment(PHP_EOL . "orderdeliveryday ended at :" . $endTime . PHP_EOL);
        Log::info('command orderdeliveryday Call.', ['startTime' => $startTime, 'endTime' => $endTime, 'comment' => '']);
    }

    /**
     * This function is used to notify customer about there order delivery day
     * @return array $response
     */
    public function getOrderDeliveryDay()
    {
        $response = DB::select('call getOrderDeliveryDay()');
        return $response;
    }
}
