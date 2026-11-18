<div style="font-family: Arial, sans-serif; max-width: 560px; margin: 0 auto; color: #1e293b;">
    <h2 style="margin-bottom: 4px;">Welcome to Baseline IT Development, {{ $user->name }}!</h2>
    <p style="color: #64748b; margin-top: 0;">TaskFlow Workspace</p>

    <p>Your account has been created. Here are your details:</p>

    <table cellpadding="6" style="border-collapse: collapse; width: 100%; font-size: 14px;">
        <tr>
            <td style="color: #64748b; width: 140px;">Role</td>
            <td><strong>{{ $roleInfo['title'] }}</strong></td>
        </tr>
        @if ($user->designation)
            <tr>
                <td style="color: #64748b;">Designation</td>
                <td>{{ $user->designation }}</td>
            </tr>
        @endif
        <tr>
            <td style="color: #64748b;">Login email</td>
            <td>{{ $user->email }}</td>
        </tr>
        @if ($user->joining_date)
            <tr>
                <td style="color: #64748b;">Joining date</td>
                <td>{{ $user->joining_date->format('d M Y') }}</td>
            </tr>
        @endif
        @if ($manager)
            <tr>
                <td style="color: #64748b;">Reports to</td>
                <td>{{ $manager->name }}@if ($manager->designation) ({{ $manager->designation }})@endif</td>
            </tr>
        @endif
    </table>

    <p style="margin-bottom: 4px;"><strong>What you can do:</strong></p>
    <ul style="margin-top: 0;">
        @foreach ($roleInfo['points'] as $point)
            <li>{{ $point }}</li>
        @endforeach
    </ul>

    <p>
        <a href="{{ $verifyUrl }}"
           style="display: inline-block; background: #4f46e5; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 6px;">
            Verify my account
        </a>
    </p>
    <p style="font-size: 13px; color: #64748b;">This link is valid for 3 days.</p>

    <p style="margin-bottom: 4px;"><strong>Your login details:</strong></p>
<table cellpadding="6" style="border-collapse: collapse; width: 100%; font-size: 14px; background: #f8fafc;">
    <tr>
        <td style="color: #64748b; width: 140px;">Login URL</td>
        <td><a href="{{ $loginUrl }}">{{ $loginUrl }}</a></td>
    </tr>
    <tr>
        <td style="color: #64748b;">Email</td>
        <td>{{ $user->email }}</td>
    </tr>
    @if ($plainPassword)
        <tr>
            <td style="color: #64748b;">Password</td>
            <td><code style="font-size: 15px;">{{ $plainPassword }}</code></td>
        </tr>
    @endif
</table>

<p style="font-size: 13px; color: #64748b;">
    Please verify your account first, then log in with the details above.
</p>
</div>