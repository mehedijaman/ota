@component('mail::message')
# Your E-Ticket

Dear {{ $booking->contact_title }} {{ $booking->contact_first_name }} {{ $booking->contact_last_name }},

Your tickets have been issued! Please find your e-ticket details below.

**Booking Reference:** {{ $booking->booking_number }}
@if($booking->pnr)
    **PNR:** {{ $booking->pnr }}
@endif

---

## Passenger Tickets

@foreach($booking->passengers as $passenger)
    | {{ $passenger->title }} {{ $passenger->first_name }} {{ $passenger->last_name }} | Ticket:
    **{{ $passenger->ticket_number ?? 'Pending' }}** |
@endforeach

---

## Flight Itinerary

@foreach($booking->segments as $segment)
    ### {{ $loop->first ? '✈️ Outbound' : '✈️ Return' }}

    | | |
    |:---|:---|
    | **Flight** | {{ $segment->airline?->name }} {{ $segment->airline?->iata_code }}{{ $segment->flight_number }} |
    | **Route** | {{ $segment->departureAirport?->city }} ({{ $segment->departureAirport?->iata_code }}) →
    {{ $segment->arrivalAirport?->city }} ({{ $segment->arrivalAirport?->iata_code }}) |
    | **Date** | {{ \Carbon\Carbon::parse($segment->departure_datetime)->format('l, d F Y') }} |
    | **Departure** | {{ \Carbon\Carbon::parse($segment->departure_datetime)->format('H:i') }} |
    | **Arrival** | {{ \Carbon\Carbon::parse($segment->arrival_datetime)->format('H:i') }} |
    | **Class** | {{ ucfirst($segment->cabin_class) }} ({{ $segment->booking_class }}) |
    | **Baggage** | {{ $segment->baggage_allowance }} |

    ---

@endforeach

## Important Information

- Please arrive at the airport at least **3 hours** before international flights and **2 hours** before domestic
flights.
- Carry a valid photo ID and your e-ticket (printed or on mobile).
- Check-in opens **24 hours** before departure.

@component('mail::button', ['url' => route('flight.viewBooking', $booking->uuid)])
View Booking Details
@endcomponent

Have a pleasant journey!

Best regards,<br>
{{ config('app.name') }}
@endcomponent