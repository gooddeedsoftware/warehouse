<?php

namespace App\Jobs;

use App\Models\WarehouseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WarehouseNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order_type;
    protected $order_id;
    protected $language;
    protected $user_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($language, $order_type = false, $order_id = false, $user_id = false)
    {
        $this->order_type = $order_type;
        $this->order_id   = $order_id;
        $this->language   = $language;
        $this->user_id    = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer, WarehouseOrder $WarehouseOrder)
    {

        try {
            if ($this->order_type && $this->order_id) {
                $WarehouseOrder::notifyWarehouseOrderStatus($this->order_type, $this->order_id, $this->language, $this->user_id);
            }
        } catch (Exception $e) {

        }
    }
}
