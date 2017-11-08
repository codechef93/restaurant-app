<?php

namespace App\Events;

use Carbon\Carbon;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Order;

class NewOrderReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $store_slack;
    public $waiter_slack;
    public $order;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public function __construct($store_slack, $order)
    {

        $this->store_slack = $store_slack;
        $this->waiter_slack = $order['waiter_slack'];
        $this->order = $order;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('new-order-chef.'.$this->store_slack),
            new PrivateChannel('new-order-waiter.'.$this->store_slack.'.'.$this->waiter_slack)
        ];
    }

    public function broadcastWith()
    {
        return [
            'store_slack' => $this->store_slack,
            'order_slack' => $this->order['order_slack'],
            'order_number' => $this->order['order_number'],
            'order_type' => $this->order['order_type'],
            'created_at' => ($this->order['created_at'] != null)?Carbon::parse($this->order['created_at'])->format(config("app.date_time_format")):null
        ];
    }
}
