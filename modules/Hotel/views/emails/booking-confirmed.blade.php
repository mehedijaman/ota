@component('mail::message')
# Booking Confirmed!

Dear {{ $booking->contact_name }},

Your hotel booking has been confirmed. Here are your booking details:

**Booking Number:** {{ $booking->booking_number }}

---

## Hotel Details

**{{ $booking->hotel->name }}**
{{ $booking->hotel->full_address }}

@if($booking->hotel->phone)
    **Phone:** {{ $booking->hotel->phone }}
@endif

---

## Stay Details

| | |
|---|---|
| **Check-in** | {{ $booking->check_in_date->format('l, F d, Y') }} (from {{ $booking->hotel->check_in_time }}) |
| **Check-out** | {{ $booking->check_out_date->format('l, F d, Y') }} (until {{ $booking->hotel->check_out_time }}) |
| **Nights** | {{ $booking->nights }} |
| **Rooms** | {{ $booking->total_rooms }} |
| **Guests** | {{ $booking->total_adults + $booking->total_children }} |

---

## Room Details

@foreach($booking->rooms as $room)
    - **{{ $room->room?->name }}**
    - Rate Plan: {{ $room->ratePlan?->name }}
    - Guests: {{ $room->adults }} adult(s){{ $room->children ? ", {$room->children} child(ren)" : '' }}
@endforeach

---

## Payment Summary

| | |
|---|---|
| Room Charges | ৳{{ number_format($booking->room_total, 2) }} |
@if($booking->tax_amount > 0)
    | Taxes & Fees | ৳{{ number_format($booking->tax_amount, 2) }} |
@endif
@if($booking->discount_amount > 0)
    | Discount | -৳{{ number_format($booking->discount_amount, 2) }} |
@endif
| **Total** | **৳{{ number_format($booking->total_amount, 2) }}** |
@if($booking->amount_paid > 0)
    | Amount Paid | ৳{{ number_format($booking->amount_paid, 2) }} |
@endif
@if($booking->amount_due > 0)
    | **Amount Due** | **৳{{ number_format($booking->amount_due, 2) }}** |
@endif

---

## Important Information

- Please present this confirmation and a valid ID at check-in
- Early check-in and late check-out are subject to availability
- For any changes or cancellations, please contact us

@component('mail::button', ['url' => route('site.hotel.bookingConfirmation', [$booking->hotel->slug, $booking])])
View Booking
@endcomponent

Thank you for choosing {{ $booking->hotel->name }}!

Best regards,
{{ config('app.name') }}
@endcomponent