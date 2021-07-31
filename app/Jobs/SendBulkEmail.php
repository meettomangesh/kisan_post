<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Aws\Ses\SesClient;

class SendBulkEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user;
    protected $emailData = [];
    protected $client;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($emailData, $user = null)
    {
        $this->user = $user;
        $this->emailData = $emailData;
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $client = new SesClient([
                //'credentials' => new Credentials(config('services.ses.username'), config('services.ses.password')),
                'credentials' => [
                    'key' => config('services.ses.key'),
                    'secret' => config('services.ses.secret'),
                ],
                // 'profile' => 'default',
                'version' => '2010-12-01',
                'region'  => config('services.ses.region')
            ]);

            $result = $client->sendBulkTemplatedEmail($this->emailData);
        } catch (Exception $e) {
        }
    }
}
