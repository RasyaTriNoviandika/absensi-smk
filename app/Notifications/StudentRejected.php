<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AttendanceReminder extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        $channels = ['database'];
        
        if ($notifiable->email) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('⏰ Reminder: Belum Absen Hari Ini')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Kamu belum melakukan absen masuk hari ini.')
            ->line('Batas waktu absen: **07:30 WIB**')
            ->line('Jangan lupa absen untuk menghindari status Alpha.')
            ->action('Absen Sekarang', route('student.dashboard'))
            ->line('Pastikan kamu berada dalam radius sekolah saat absen.')
            ->salutation('Salam, Tim Absensi SMKN 9 Bekasi');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'reminder',
            'message' => 'Jangan lupa absen hari ini! Batas: 07:30 WIB',
            'action_url' => route('student.dashboard'),
        ];
    }
}