<?php

namespace App\Jobs;

use App\Mail\VerifyMail;
use Illuminate\Support\Facades\Mail;

class ActivateMailJob extends Job
{
    private $verify_mail;
    private $to;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(VerifyMail $verify_mail, string $to)
    {
        $this->verify_mail = $verify_mail;
        $this->to = $to;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(/*VerifyMail $verify_mail, string $to*/)
    {
        Mail::to($this->to)->send($this->verify_mail);
    }
}
