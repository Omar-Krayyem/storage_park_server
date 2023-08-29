<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class AcceptRequest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('storagepark.lb@gmail.com', 'Storage Park'),
            subject: 'Accept Request',
        );
    }

    public function build(): void
    {
        $companyname = 'John Doe'; // Replace with actual guest's name
        $email = 'john@example.com'; // Replace with actual email
        $password = 'random123';

        $messageContent = "Dear $companyname,\n\n";
        $messageContent .= "We trust this email finds you well.\n\n";
        $messageContent .= "We are pleased to inform you that your registration for access to our warehouse facilities has been accepted.\n\n";
        $messageContent .= "Here are your login details:\n";
        $messageContent .= "Username: $email\n";
        $messageContent .= "Password: $password\n\n";
        $messageContent .= "Thank you for using our service.\n";  

        $this->view('emails.accept-request') 
             ->text('emails.accept-request-plain')
             ->with(['messageContent' => $messageContent]);
    }
    
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
