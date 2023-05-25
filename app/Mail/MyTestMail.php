<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\InvoicesMerged;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MyTestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $id;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($detail, $id)
    {
        //
        $this->details = $detail;
        $this->id = $id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $detail = $this->details;
        $id = $this->id;
        return $this->subject('Mail form warehousesystemdev')->view('admin.invoices.emailtemplate', compact('detail', 'id'));
    }
}
