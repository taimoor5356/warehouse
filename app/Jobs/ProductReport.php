<?php

namespace App\Jobs;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
// use AWS\CRT\HTTP\Request;
use App\AdminModels\Products;
use Illuminate\Bus\Queueable;
use App\Traits\AllProductReport;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
class ProductReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, AllProductReport;
    public $req;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        //
        $this->req = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        return $this->generateReport($this->req);
    }
}
