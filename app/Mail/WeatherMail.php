<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeatherMail extends Mailable
{
    use Queueable, SerializesModels;

    private string $city;
    private int $temperature;
    private int $humidity;
    private string $description;

    /**
     * Create a new message instance.
     */
    public function __construct(string $c, int $t, int $h, string $d)
    {
        $this->city = $c;
        $this->temperature = $t;
        $this->humidity = $h;
        $this->description = $d;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Weather for ' . $this->city,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.weather', // resources/views/emails/weather.blade.php
            with: [
                'city' => $this->city,
                'temperature' => $this->temperature,
                'humidity' => $this->humidity,
                'description' => $this->description,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
