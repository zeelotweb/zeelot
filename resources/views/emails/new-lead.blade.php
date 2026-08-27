<h2>You have a new project inquiry!</h2>
<p><strong>Name:</strong> {{ $lead->name }}</p>

@if($lead->is_pro_bono)
    <p><strong>Type:</strong> Pro Bono Application</p>
@else
    <p><strong>Packages selected:</strong></p>
    <ul>
        @foreach($lead->packages as $package)
            <li>{{ $package->name }} — ${{ number_format($package->price, 2) }}</li>
        @endforeach
    </ul>
    <p><strong>Subtotal:</strong> ${{ number_format($lead->packagesSubtotal(), 2) }}</p>
    @if($lead->discount_amount > 0)
        <p><strong>Discount applied:</strong> -${{ number_format($lead->discount_amount, 2) }}</p>
    @endif
    <p><strong>Total:</strong> ${{ number_format($lead->total(), 2) }}</p>
@endif

<p><strong>Message:</strong></p>
<p>{{ $lead->message }}</p>

<hr>
<p><a href="{{ url('/admin/leads') }}">View in Dashboard</a></p>
