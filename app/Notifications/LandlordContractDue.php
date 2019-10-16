<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\LandlordContract;

class LandlordContractDue extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(LandlordContract $landlordContract)
    {
        $this->landlordContract = $landlordContract;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
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
        $building = $this->landlordContract->building;
        $landlords = $this->landlordContract->landlords;
        return [
            'content' => '合約即將到期! 物件編號:'.$building->building_code.
                        ' 地址:'.$building->location.
                        ' 房東姓名:'.implode(',', $landlords->pluck('name')->toArray()).
                        ' 到期日為:'.$this->landlordContract->commission_end_date
        ];
    }
}
