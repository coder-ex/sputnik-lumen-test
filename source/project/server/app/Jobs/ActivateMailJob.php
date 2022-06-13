<?php

namespace App\Jobs;

use App\Mail\VerifyMail;
use Illuminate\Support\Facades\Mail;

class ActivateMailJob extends Job
{
    private $verifyMail;
    private $to;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(VerifyMail $verifyMail, string $to)
    {
        $this->verifyMail = $verifyMail;
        $this->to = $to;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->to)->send($this->verifyMail);
    }
}
