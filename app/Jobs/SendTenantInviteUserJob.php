<?php

namespace App\Jobs;

use Throwable;
use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Mail\TenantInviteUserMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;

class SendTenantInviteUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected User $user)
    {
        $this->user = $user;
    }

    public $uniqueFor = 60;
    public function uniqueId(): string
    {
        return $this->user->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user)->send(new TenantInviteUserMail($this->user, tenant()));
    }

    public function failed(Throwable $exception): void
    {
        info('falha');
    }
}