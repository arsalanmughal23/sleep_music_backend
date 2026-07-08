<?php

namespace App\Jobs;

use App\Models\Package;
use App\Models\Transaction;
use App\Models\Usersubscription;
use App\Models\WebhookLog;
use App\Repositories\Admin\TransactionRepository;
use App\Repositories\Admin\UsersubscriptionRepository;
use Google\Cloud\PubSub\PubSubClient;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class AndroidUserSubscriptionWebHook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

    }
}
