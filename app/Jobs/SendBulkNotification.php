<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helper\NotificationHelper;
use App\Models\UserCommunicationMessages;

class SendBulkNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $notificationData = [];
    protected $userCommunicationMessages;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notificationData, $user = null)
    {
        $this->user = $user;
        $this->notificationData = $notificationData;       
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // 
           // $userCommunicationMessages = new (UserCommunicationMessages::class);
           $userCommunicationMessages = UserCommunicationMessages::find($this->notificationData['notification_id']);
            $notifyHelper = new NotificationHelper();

            $notifyHelper->setParameters(["user_id" => $this->notificationData['user_id'], "deep_link" => $this->notificationData['deep_link'],"details"=>""], $this->notificationData['push_title'], $this->notificationData['push_text']);

            $userCommunicationMessages->notify($notifyHelper);
            // $result = $client->sendBulkTemplatedEmail($this->emailData);
        } catch (Exception $e) {
        }
    }
}
