<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrderOfferMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order_pdf;
    protected $to_email;
    protected $status;
    protected $language;
    protected $order_number;
    protected $is_not_production;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order_pdf, $status, $to_email, $language, $order_number, $is_not_production)
    {

        $this->order_pdf         = $order_pdf;
        $this->status            = $status;
        $this->to_email          = $to_email;
        $this->language          = $language;
        $this->order_number      = $order_number;
        $this->is_not_production = $is_not_production;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer, Order $ordermodel)
    {
        try {
            if (@$this->to_email) {
                $language          = $this->language;
                $order_pdf         = $this->order_pdf;
                $status            = $this->status;
                $file_name         = "";
                $mail_body         = "";
                $from_mail         = 'noreply@maskinstyring.com';
                $from_company_name = config("app.app_name") . " AS";
                if (config("app.env") != "production") {
                    $from_mail         = "noreply@avalia.no";
                    $from_company_name = "Avalia Staging";
                }
                if ($this->status == 1) {
                    if ($this->language == 'en') {
                        $file_name = $subject = 'Offer';
                    } else {
                        $file_name = $subject = 'Tilbud';
                    }
                } else {
                    if ($this->language == 'en') {
                        $file_name = $subject = 'Order';
                    } else {
                        $file_name = $subject = 'Ordre';
                    }
                }
                $subject   = $subject . ' ' . $this->order_number;
                $file_name = $file_name . '_' . $this->order_number . ".pdf";
                $mailer->send([], [], function ($message) use ($mail_body, &$subject, &$order_pdf, &$file_name, &$from_mail, &$from_company_name, &$is_not_production) {
                    $message->to($this->to_email)->subject($subject);
                    if ($is_not_production == 1) {
                        $bcc_email[] = "david@processdrive.com";
                        $bcc_email[] = "vitali@avalia.no";
                        $bcc_email[] = "davidms1296@gmail.com";
                        $message->bcc($bcc_email);
                    }
                    $message->attach($order_pdf, ['as' => $file_name]);
                    $message->setBody($mail_body);
                });
            }
        } catch (Exception $e) {
            echo $e;exit;
            return false;
        }
    }
}
