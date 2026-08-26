<h2>You've been invited to join ZeelotWeb</h2>
<p>You've been invited to join as <strong>{{ ucfirst(str_replace('_', ' ', $invitation->role)) }}</strong>.</p>
<p>Click the link below to create your account:</p>
<p><a href="{{ route('register', ['invitation' => $invitation->token]) }}">Accept invitation &amp; create account</a></p>
<hr>
<p>This link expires on {{ $invitation->expires_at->format('F j, Y') }}.</p>
