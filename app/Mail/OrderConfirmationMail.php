<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $products;

    public function __construct($order, $products)
    {
        $this->order = $order;
        $this->products = $products;
    }

    public function build()
    {
        return $this->view('emails.order_confirmation')
                    ->subject('Order Confirmation - Thank You for Your Purchase!')
                    ->with([
                        'order' => $this->order,
                        'products' => $this->products,
                    ]);
    }
}

