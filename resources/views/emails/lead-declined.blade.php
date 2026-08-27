<h2>Following up on your project inquiry</h2>
<p>Hi {{ $lead->name }},</p>
<p>Thanks for reaching out to ZeelotWeb about your project. After reviewing the details, it's not a fit we're able to take on right now.</p>
@if($lead->decline_reason)
    <p>{{ $lead->decline_reason }}</p>
@endif
<p>Appreciate you thinking of us, and best of luck with the project.</p>
