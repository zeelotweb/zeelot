<h2>Your quote from ZeelotWeb</h2>
<p>Hi {{ $quote->lead->name }},</p>
<p>Here's the scope and pricing for your project:</p>

<table cellpadding="6" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%;">
    @foreach($quote->lineItems as $item)
        <tr>
            <td>
                <strong>{{ $item->title }}</strong>
                @if($item->description)
                    <br><span>{{ $item->description }}</span>
                @endif
            </td>
            <td style="text-align: right; white-space: nowrap;">${{ number_format($item->amount, 2) }}</td>
        </tr>
    @endforeach
    <tr>
        <td style="text-align: right;"><strong>Total</strong></td>
        <td style="text-align: right;"><strong>${{ number_format($quote->total(), 2) }}</strong></td>
    </tr>
</table>

@if($quote->note)
    <p>{{ $quote->note }}</p>
@endif

<p><a href="{{ url('/quotes/'.$quote->token) }}">Review &amp; respond to this quote</a></p>

@if($quote->valid_until)
    <hr>
    <p>This quote is valid until {{ $quote->valid_until->format('F j, Y') }}.</p>
@endif
