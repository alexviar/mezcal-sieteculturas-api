<?php

namespace App\Jobs;

use App\Models\Purchase;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class PostPurchase implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Purchase $purchase,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $purchase = $this->purchase;
        $pdfData = [
            'purchase' => $purchase,
        ];

        $pdf = Pdf::loadView('orders.purchase', $pdfData);
        $pdf->setPaper('letter', 'landscape');
        $pdfOutput = $pdf->output();

        Mail::send('mails.purchase-customer', $pdfData, function ($mail) use ($pdfOutput, $purchase) {
            $mail->from(config('mail.from.address'));
            $mail->to($purchase->customer_mail);
            $mail->subject("¡Gracias por tu compra!");
            $mail->attachData($pdfOutput, 'purchase_order.pdf');
        });

        $recipients = config('notifications.purchase_recipients');

        foreach ($recipients as $recipient) {
            Mail::send('mails.purchase-seller', $pdfData, function ($mail) use ($pdfOutput, $recipient) {
                $mail->from(config('mail.from.address'));
                $mail->to($recipient);
                $mail->subject("¡Hemos recibido un pedido!");
                $mail->attachData($pdfOutput, 'purchase_order.pdf');
            });
        }
    }
}
