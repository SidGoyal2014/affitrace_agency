<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Mail\EuropaMail;

class MailController extends Controller
{

	protected $mailService;

	public function __construct(\App\Services\MailService $mailService)
	{
		$this->mailService = $mailService;
	}

	public function index()
	{
		$this->mailService->sendTestMail();
		return;
	}//index


	public function contact_email(Request $request)
	{
		$this->mailService->sendContactEmail($request);
		return redirect('contact')->with('success', 'email has been sent');
	}//contact_email




}//MailController
