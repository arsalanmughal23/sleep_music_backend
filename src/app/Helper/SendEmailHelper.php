<?php

namespace App\Helper;

use Aws\Signature\SignatureV4;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Queue;

class SendEmailHelper
{

    public function sendEmail($to, $subject, $data, $templateName)
    {
        $messageBody = [
            "msg" => "test-mail",
            "data" => [
                "to" => $to,
                "subject" => $subject,
                "data" => $data,
                "projectName" => config('queue.connections.sqs.email_service_project_name'),
                "templateName" => $templateName,
                "from" => config('mail.from.address')
            ]
        ];

        $sqs = Queue::connection('sqs'); // Get the SQS connection

        $queueUrl = config('queue.connections.sqs.queue_url'); // Replace with your actual SQS queue URL

        $messageBody = json_encode($messageBody);

        $sqsClient = $sqs->getSqs(); // Get the underlying AWS SDK SQS client

        $result = $sqsClient->sendMessage([
            'QueueUrl' => $queueUrl,
            'MessageBody' => $messageBody,
        ]);

        // Process the result if needed
        if ($result->hasKey('MessageId')) {
            return "Message sent with ID: " . $result->get('MessageId');
        } else {
            return "Failed to send message";
        }
    }
}