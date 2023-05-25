<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductReportMail extends Mailable
{
    use Queueable, SerializesModels;
    public $fileName;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($fileName)
    {
        //
        $this->fileName = $fileName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('All Products Report')->view('admin.reports.all_product_report_template')->attach(public_path('reports/'.$this->fileName));
    }
}
