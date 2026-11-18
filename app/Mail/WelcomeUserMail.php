<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class WelcomeUserMail extends Mailable
{
    use SerializesModels;

    public function __construct(public User $user, public ?string $plainPassword = null) {}

    public function envelope(): Envelope
    {
       return new Envelope(subject: 'Welcome to Baseline IT Development - Verify your account');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'user'      => $this->user,
                'manager'   => $this->user->manager,
                'verifyUrl' => URL::temporarySignedRoute(
                    'account.verify',
                    now()->addDays(3),
                    ['user' => $this->user->id]
                ),
                'loginUrl'  => route('login'),
                'plainPassword' => $this->plainPassword,
                'roleInfo'  => $this->roleInfo(),
            ],
        );
    }

    // Role-specific text shown in the email
    private function roleInfo(): array
    {
        return match ($this->user->role) {
            'project_manager' => [
                'title'  => 'Project Manager',
                'points' => [
                    'Create and manage projects',
                    'Plan sprints and milestones',
                    'Review team leader reports',
                ],
            ],
            'team_leader' => [
                'title'  => 'Team Leader',
                'points' => [
                    'Assign tasks to your team',
                    'Review code and pull requests',
                    'Report progress to your project manager',
                ],
            ],
            default => [
                'title'  => 'Employee',
                'points' => [
                    'View and update your assigned tasks',
                    'Log your daily progress',
                    'Report to your team leader',
                ],
            ],
        };
    }
}