<?php
namespace rib\utils;
//  Отправка почты
class Mailer {
	private $message;
	private $subject;
	private $recipient;	
	private $header;

	public function __construct($recipient, $subject, $message)
	{
		$this->message = $message;
		$this->subject = $subject;
		$this->recipient = $recipient;
	}
	public function sendEmail() {
		$this->setHeader();
		@mail($this->recipient,$this->subject,$this->message,$this->header);	
	}
	private function setHeader() {
		$this->header = 'MIME-Version: 1.0' . "\r\n";
		$this->header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$this->header .= 'From: crm <noreply@russipoteka.ru>' . "\r\n";
	}



}