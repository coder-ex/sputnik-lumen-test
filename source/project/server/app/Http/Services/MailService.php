<?php

namespace App\Http\Services;

use App\Jobs\ActivateMailJob;
use App\Mail\VerifyMail;

class MailService
{
    public function sendActivationMail(VerifyMail $verifyMail, string $to, string $link = null)
    {
        dispatch(new ActivateMailJob($verifyMail, $to)); //->onQueue('email');
    }
}
