<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCreationNotification extends Notification
{
    use Queueable;
    protected $user;
    protected $token;
    

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user,$token)
    {
        $this->user = $user;
        $this->token=$token;
        
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $token = $this->token;
        $email=$this->user->email;
        $en = Crypt::encryptString($token);
        $url= "http://ec2-65-0-211-237.ap-south-1.compute.amazonaws.com/ResetPassword?token=$en&email=$email"; 
       
       $mailer = new MailMessage();
        return $mailer
        ->subject("Welcome to Nightingale")
        ->greeting("Hi {$this->user->first_name}")
        ->line("You are being granted Admin privliges for the Nightingale app.")
        ->line("Please follow this link to complete the registration process")
        ->action("Login now ", $url)
        ;
        // ->view("emails.user_create",
        

        
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
