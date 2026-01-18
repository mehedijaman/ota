@component('mail::message')
# Booking Cancelled

Dear {{ $booking->contact_title }} {{ $booking->contact_first_name }} {{ $booking->contact_last_name }},

Your flight booking has been cancelled.

**Booking Number:** {{ $booking->booking_number }}

---

## Cancellation Details

| | |
|:---|:---|
| **Reason** | {{ $booking->cancellation?->reason_category ?? 'Customer Request' }} |
| **Cancelled On** | {{ $booking->cancelled_at?->format('D, d M Y H:i') }} |

---

## Flight Information

@foreach($booking->segments as $segment)
    - **{{ $segment->airline?->iata_code }}{{ $segment->flight_number }}**: {{ $segment->departureAirport?->city }} →
    {{ $segment->arrivalAirport?->city }} on {{ \Carbon\Carbon::parse($segment->departure_datetime)->format('d M Y') }}
@endforeach

---

@if($booking->cancellation && $booking->cancellation->refundable_amount > 0)
    ## Refund Information

    | | |
    |:---|---:|
    | **Original Amount** | {{ number_format($booking->cancellation->booking_amount, 2) }} {{ $booking->currency_code }} |
    | **Cancellation Fee** | {{ number_format($booking->cancellation->total_deduction, 2) }} {{ $booking->currency_code }} |
    | **Refundable Amount** | {{ number_format($booking->cancellation->refundable_amount, 2) }}
    {{ $booking->currency_code }} |

    Your refund will be processed within 7-14 business days.
@endif

---

If you have any questions, please contact our customer support.

Best regards,<br>
{{ config('app.name') }}
@endcomponent