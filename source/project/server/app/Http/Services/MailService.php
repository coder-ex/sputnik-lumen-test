<?php

namespace App\Http\Services;

use App\Jobs\ActivateMailJob;
use App\Mail\VerifyMail;

class MailService
{
    public function sendActivationMail(VerifyMail $verify_mail, string $to, string $link = null)
    {
        dispatch(new ActivateMailJob($verify_mail, $to)); //->onQueue('email');
    }
}
