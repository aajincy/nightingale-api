<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $viaItems           = [];
        $notificationTriat  = "ResetPasswordNotification";
        $notification       = \App\Models\SystemNotifications::where('notification_triat',$notificationTriat)->first();
        $user  = auth()->user();
        if($user->isStaff()){
            $userNotificationStatus = $user->staff->notifications()->where('system_notification_id',$notification)->first();
            if($notification && $notification->email == true){
                if($userNotificationStatus && $userNotificationStatus->email == true){
                    $viaItems[] = 'mail';
                }
            }
            if($notification && $notification->sms == true){
                if($userNotificationStatus && $userNotificationStatus->sms == true){
                    $viaItems[] = 'sms';
                }
            }
            if($notification && $notification->push_notification == true){
                if($userNotificationStatus && $userNotificationStatus->push_notification == true){
                    $viaItems[] = 'pushNotification';
                }
            }
            if($notification && $notification->in_app_notification == true){
                if($userNotificationStatus && $userNotificationStatus->in_app_notification == true){
                    $viaItems[] = 'inAppNotification';
                }
            }
        }
        return $viaItems;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Your Password was reset.')
                    ->line('Your Password was reset.')
                    ->line('Thank you for using our application!');
    }
    public function toinAppNotification($notifiable)
    {
        return (new MailMessage)
                    ->subject('Your Password was reset.')
                    ->line('Your Password was reset.')
                    ->line('Thank you for using our application!');
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
