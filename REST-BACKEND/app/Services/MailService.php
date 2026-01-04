<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\EuropaMail;

class MailService
{
    public function sendTestMail()
    {
        $mailData = [
            'title' => 'Mail from Europa Mail',
            'subject' => 'Contact Mail from Europa Mail',
            'body' => 'This is for testing email using smtp.'
        ];

        Mail::to('ranjangoyal2004@gmail.com')->send(new EuropaMail($mailData));
    }

    public function sendContactEmail($request)
    {
        $body = "Hello Admin,";
        $body .= "<br />";
        $body .= "A new lead generated  from the contact page is given below:";
        $body .= "<br />";
        $body .= "<strong>Name: </strong>" . $request->name;
        $body .= "<br />";
        $body .= "<strong>Email: </strong>" . $request->email;
        $body .= "<br />";
        $body .= "<strong>Phone: </strong>" . $request->phone;
        $body .= "<br />";
        $body .= "<strong>Message: </strong>" . $request->message;

        $mailData = [
            'title' => 'Contact page email from Europa Mail',
            'subject' => $request->subject,
            'body' => html_entity_decode($body)
        ];

        Mail::to('ranjangoyal2004@gmail.com')->send(new EuropaMail($mailData));
    }
}
