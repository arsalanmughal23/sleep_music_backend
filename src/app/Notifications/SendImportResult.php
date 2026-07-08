<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendImportResult extends Notification
{
    use Queueable;

    /** @var $ignored_rows array */
    private $ignored_rows;

    private $total_imported = 0;

    /**
     * Create a new notification instance.
     *
     * @param $ignored_rows
     * @param $total_imported
     */
    public function __construct($ignored_rows, $total_imported)
    {
        $this->ignored_rows   = $ignored_rows;
        $this->total_imported = $total_imported;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->greeting("Hello " . $notifiable->details->full_name . ",")
            ->subject("Import Task Result");

        if (count($this->ignored_rows) > 0) {
            $message->line('Following Records did not make it')
                ->line("Song Name: Reason");

            foreach ($this->ignored_rows as $row) {
                $message->line($row['name'] . ": " . $row['reason']);
            }

            $message->attachData($this->arrayToCsvString($this->ignored_rows), "failed.csv");
        } else {
            $message->line("Media import has been completed");
        }

        $message->line('Imported Media: ' . $this->total_imported);

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    private function arrayToCsvString($array, $delimiter = ',', $enclosure = '"', $escape_char = "\\")
    {
        $f = fopen('php://memory', 'r+');
        if (count($array) > 0) {
            array_unshift($array, array_keys($array[0]));
        }
        foreach ($array as $item) {
            fputcsv($f, $item, $delimiter, $enclosure, $escape_char);
        }
        rewind($f);
        return stream_get_contents($f);
    }
}
