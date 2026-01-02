<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ObatPrb;

class KlaimObatNotification extends Notification
{
    use Queueable;

    protected $obat;

    /**
     * Create a new notification instance.
     */
    public function __construct(ObatPrb $obat)
    {
        $this->obat = $obat;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('Obat telah diklaim oleh apotek.')
                    ->action('Lihat Detail', url('/'))
                    ->line('Terima kasih!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Obat ' . $this->obat->nama_obat . ' telah diklaim oleh apotek ' . $this->obat->diagnosaPrb->patient->nama_pasien,
            'obat_id' => $this->obat->id_obat,
            'pasien' => $this->obat->diagnosaPrb->patient->nama_pasien,
        ];
    }
}