<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class BadgeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $details = NULL;

    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

       

        return $this->view($this->details['blade'])
            ->subject($this->details['subject'])
            ->from('dcp@mti.bme.hu', 'DisCo Player - DCP')
            ->attachFromStorage(
                $this->details['digitalbadgepath'],
                'badge_' . $this->details['id'] . '.png',
                ['mime' => 'application/png']
            )
            ->with('details', $this->details);
    }
}
