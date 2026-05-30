<h2>You have a new project inquiry!</h2>
<p><strong>Name:</strong> {{ $lead->name }}</p>
<p><strong>Budget:</strong> ${{ number_format($lead->budget) }}+</p>
<p><strong>Message:</strong></p>
<p>{{ $lead->message }}</p>

<hr>
<p><a href="{{ url('/admin/leads') }}">View in Dashboard</a></p>
