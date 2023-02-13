<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\User;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
        $content = "<!DOCTYPE html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'><title>Новые обновления на вашем аккаунте</title></head><body>";
        $content .= "<h1>Cargo Jolldas</h1>";
        $content .= "<h2>Новые поступления на склад</h2>";
        $content .= "<h3>Дата прибытия: ".date('Y-m-d')."<br>Время прибытия: ".date('G:i')."</h3>";
        $content .= "<p><a href='https://jolldas.kz/'>www.jolldas.kz</a></p>";
        $content .= "</body></html>";

        $headers = "From: serv@jolldas.kz \r\n" .
                   "MIME-Version: 1.0" . "\r\n" . 
                   "Content-type: text/html; charset=UTF-8" . "\r\n";

        // Send the email
        if (mail($this->user->email, $subject, $content, $headers)) {
            $status = 'Ваша заявка принята. Спасибо!';
        }
        else {
            $status = 'Произошла ошибка.';
        }
    }
}
