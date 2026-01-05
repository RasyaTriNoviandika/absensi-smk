<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;

class StudentApproved extends Notification
{
    use Queueable;

    protected $student;

    public function __construct(User $student)
    {
        $this->student = $student;
    }

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
            ->subject('🎉 Akun Anda Telah Disetujui!')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Selamat! Akun Anda telah disetujui oleh admin.')
            ->line('Anda sekarang dapat login dan melakukan absensi.')
            ->line('**Username:** ' . $notifiable->username)
            ->action('Login Sekarang', route('login'))
            ->line('Pastikan Anda berada dalam radius sekolah saat melakukan absensi.')
            ->salutation('Salam, Tim Absensi SMKN 9 Bekasi');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'approval',
            'message' => 'Akun Anda telah disetujui! Silakan login.',
            'action_url' => route('login'),
        ];
    }
}