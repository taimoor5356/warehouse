<?php

use Illuminate\Support\Facades\DB;
  
function getInvoicesJobsDetails($jobName1, $jobName2) 
{
    $job = DB::table('jobs');
    if ($job->count() > 0) {
        $jobs = $job->get();
        foreach ($jobs as $key => $jobType) {
            $jobTypeName = json_decode($jobType->payload);
            if ($jobTypeName->displayName == $jobName1 || $jobTypeName->displayName == $jobName2) {
                return 'exists';
            }
        }
    }
}