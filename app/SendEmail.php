<?php

namespace App;

use Illuminate\Support\Facades\Mail;

trait SendEmail
{
    public function sendEmail($params)
    {
        Mail::send('emails.' . $params['view'], compact('params'), function ($message) use ($params) {
            $message->from(config('mail.from.address'), config('mail.from.name'));
            $message->to($params['to'])->subject($params['subject']);
        });

        if (Mail::failures()) {
            return ['status' => false];
        } else {
            return ['status' => true];
        }
    }
}
