<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emails;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($emails)
    {
        $this->emails = $emails;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Email subject
        $subject = "Новые обновления на вашем аккаунте";

        // Email content
        $content = "<h2>Новые поступления на склад</h2>";
        $content .= "<h4>Дата: " . date('Y-m-d') . "</h4>";
        $content .= "<h4>Время: " . date('G:i') . "</h4>";
        $content .= "<p><a href='https://jolldas.kz/'>www.jolldas.kz</a></p>";

        $headers = "From: serv@jolldas.kz \r\n" .
                   "MIME-Version: 1.0" . "\r\n" . 
                   "Content-type: text/html; charset=UTF-8" . "\r\n";

        // Send the email
        if (mail($this->emails, $subject, $content, $headers)) {
            $status = 'Ваша заявка принята. Спасибо!';
        }
        else {
            $status = 'Произошла ошибка.';
        }
    }
}
