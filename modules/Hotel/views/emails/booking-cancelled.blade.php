@component('mail::message')
# Booking Cancelled

Dear {{ $booking->contact_name }},

We're sorry to inform you that your hotel booking has been cancelled.

**Booking Number:** {{ $booking->booking_number }}

---

## Cancellation Details

| | |
|---|---|
| **Cancelled On** | {{ $booking->cancelled_at?->format('F d, Y H:i') }} |
@if($booking->cancellation)
    | **Reason** | {{ $booking->cancellation->reason }} |
@endif

---

## Original Booking

**{{ $booking->hotel->name }}**
{{ $booking->hotel->city?->name }}, {{ $booking->hotel->country?->name }}

| | |
|---|---|
| **Check-in** | {{ $booking->check_in_date->format('l, F d, Y') }} |
| **Check-out** | {{ $booking->check_out_date->format('l, F d, Y') }} |
| **Rooms** | {{ $booking->total_rooms }} |

---

## Refund Information

@if($booking->cancellation && $booking->cancellation->refund_amount > 0)
    A refund of **৳{{ number_format($booking->cancellation->refund_amount, 2) }}** will be processed to your original
    payment method within 5-10 business days.

    @if($booking->cancellation->penalty_amount > 0)
        A cancellation penalty of ৳{{ number_format($booking->cancellation->penalty_amount, 2) }} has been applied per the
        cancellation policy.
    @endif
@else
    Based on the cancellation policy, no refund is applicable for this cancellation.
@endif

---

If you did not request this cancellation or have any questions, please contact us immediately.

@component('mail::button', ['url' => route('site.hotel.index')])
Book Another Hotel
@endcomponent

We hope to serve you again in the future.

Best regards,
{{ config('app.name') }}
@endcomponent