<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Queue;
use App\Helper;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $to, $subject, $data, $templateName, $sendEmailHelper;
    
    /**
     * SendEmail constructor.
     * @param $to
     * @param $subject
     * @param $data
     * @param $templateName
     */
    public function __construct($to, $subject, $data, $templateName)
    {
        $this->to = $to; 
        $this->subject = $subject; 
        $this->data = $data; 
        $this->templateName = $templateName;
        $this->sendEmailHelper = new Helper\SendEmailHelper();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sendEmailHelper->sendEmail($this->to, $this->subject, $this->data, $this->templateName);
    }

}
