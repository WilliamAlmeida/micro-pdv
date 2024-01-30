<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class TenantInviteUserMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public Tenant $tenant,
    )
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            tags: ['invites-tenant-user'],
            subject: "Convite para se juntar à Equipe {$this->tenant->nome_fantasia}!",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $expire_at = now()->addMinutes(15);

        $link = URL::temporarySignedRoute('aceitando.convite.empresa', $expire_at, ['email' => $this->user->email, 'token' => tenant('id')]);

        return new Content(
            markdown: 'mail.tenant-invite-user',
            with: [
                'tenant_name' => $this->tenant->nome_fantasia,
                'link_accept' => $link,
                'expire_at' => $expire_at->format('d/m/Y H:i:s'),
            ]
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
