@component('mail::message')
# Booking Confirmed

Dear {{ $booking->contact_title }} {{ $booking->contact_first_name }} {{ $booking->contact_last_name }},

Your flight booking has been confirmed.

**Booking Number:** {{ $booking->booking_number }}

---

## Flight Details

@foreach($booking->segments as $segment)
    ### {{ $segment->segment_type === 'outbound' ? 'Outbound' : 'Return' }} Flight

    | | |
    |:---|:---|
    | **Flight** | {{ $segment->airline?->iata_code }}{{ $segment->flight_number }} |
    | **From** | {{ $segment->departureAirport?->city }} ({{ $segment->departureAirport?->iata_code }}) |
    | **To** | {{ $segment->arrivalAirport?->city }} ({{ $segment->arrivalAirport?->iata_code }}) |
    | **Date** | {{ \Carbon\Carbon::parse($segment->departure_datetime)->format('D, d M Y') }} |
    | **Departure** | {{ \Carbon\Carbon::parse($segment->departure_datetime)->format('H:i') }} |
    | **Arrival** | {{ \Carbon\Carbon::parse($segment->arrival_datetime)->format('H:i') }} |
    | **Class** | {{ ucfirst($segment->cabin_class) }} |

@endforeach

---

## Passengers

@foreach($booking->passengers as $passenger)
    - {{ $passenger->title }} {{ $passenger->first_name }} {{ $passenger->last_name }}
    ({{ ucfirst($passenger->passenger_type) }})
@endforeach

---

## Payment Summary

| | |
|:---|---:|
| **Total Amount** | {{ number_format($booking->total_amount, 2) }} {{ $booking->currency_code }} |
| **Amount Paid** | {{ number_format($booking->amount_paid, 2) }} {{ $booking->currency_code }} |
| **Amount Due** | {{ number_format($booking->amount_due, 2) }} {{ $booking->currency_code }} |

---

@if($booking->amount_due > 0)
    > **Important:** Please complete your payment before the ticket time limit to avoid automatic cancellation.
@endif

@component('mail::button', ['url' => route('flight.viewBooking', $booking->uuid)])
View Booking
@endcomponent

Thank you for booking with us!

Best regards,<br>
{{ config('app.name') }}
@endcomponent