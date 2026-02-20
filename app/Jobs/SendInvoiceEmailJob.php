<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendInvoiceEmailJob implements ShouldQueue
{
    use Queueable;

    public $invoice;

    /**
     * Create a new job instance.
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = $this->invoice->order->customer->email;
        \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\InvoiceMail($this->invoice));
    }
}
