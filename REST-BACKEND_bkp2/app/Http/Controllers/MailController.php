<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Mail\EuropaMail;

class MailController extends Controller{


    public function index(){

        $mailData = [
            'title' => 'Mail from Europa Mail',
			'subject' => 'Contact Mail from Europa Mail',
            'body' => 'This is for testing email using smtp.'
        ];
         
        Mail::to('ranjangoyal2004@gmail.com')->send(new EuropaMail($mailData));
        return;


    }//index


	public function contact_email(Request $request){

		$body = "Hello Admin,";
		$body .= "<br />";
		$body .= "A new lead generated  from the contact page is given below:";
		$body .= "<br />";
		$body .= "<strong>Name: </strong>".$request->name;
		$body .= "<br />";
		$body .= "<strong>Email: </strong>".$request->email;
		$body .= "<br />";
		$body .= "<strong>Phone: </strong>".$request->phone;
		$body .= "<br />";
		$body .= "<strong>Message: </strong>".$request->message;

        $mailData = [
            'title' => 'Contact page email from Europa Mail',
			'subject' => $request->subject,
            'body' => html_entity_decode($body)
        ];

        Mail::to('ranjangoyal2004@gmail.com')->send(new EuropaMail($mailData));
        return redirect('contact')->with('success', 'email has been sent');

	
	}//contact_email




}//MailController
