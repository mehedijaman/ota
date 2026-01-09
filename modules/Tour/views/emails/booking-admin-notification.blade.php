<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Tour Booking</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155; margin: 0; padding: 0; background-color: #f1f5f9; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .card { background: #ffffff; border-radius: 16px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); overflow: hidden; }
        .header { background: linear-gradient(135deg, #0f172a 0%, #334155 100%); padding: 25px; }
        .header h1 { color: #ffffff; margin: 0; font-size: 20px; }
        .header .badge { display: inline-block; background: #22c55e; color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 12px; margin-top: 8px; }
        .content { padding: 25px; }
        .booking-info { background: #f8fafc; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
        .booking-info h3 { margin: 0 0 15px; color: #1e293b; font-size: 16px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        .info-grid { display: grid; gap: 12px; }
        .info-row { display: flex; justify-content: space-between; }
        .info-row .label { color: #64748b; font-size: 14px; }
        .info-row .value { color: #1e293b; font-weight: 600; font-size: 14px; }
        .amount { font-size: 24px; color: #ea580c; font-weight: 700; }
        .button { display: inline-block; background: #ea580c; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; }
        .footer { text-align: center; padding: 20px; color: #64748b; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>🎫 New Tour Booking Received</h1>
                <span class="badge">Pending Confirmation</span>
            </div>
            
            <div class="content">
                <div class="booking-info">
                    <h3>Booking Details</h3>
                    <div class="info-grid">
                        <div class="info-row">
                            <span class="label">Booking Number</span>
                            <span class="value">{{ $booking->booking_number }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Tour</span>
                            <span class="value">{{ $booking->tour->name }}</span>
                        </div>
                        @if($booking->departureDate)
                        <div class="info-row">
                            <span class="label">Departure</span>
                            <span class="value">{{ $booking->departureDate->departure_date->format('M d, Y') }}</span>
                        </div>
                        @endif
                        <div class="info-row">
                            <span class="label">Travelers</span>
                            <span class="value">{{ $booking->total_travelers }} ({{ $booking->adults_count }} Adults{{ $booking->children_count ? ', '.$booking->children_count.' Children' : '' }})</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Source</span>
                            <span class="value">{{ ucfirst($booking->booking_source) }}</span>
                        </div>
                    </div>
                </div>

                <div class="booking-info">
                    <h3>Customer Information</h3>
                    <div class="info-grid">
                        <div class="info-row">
                            <span class="label">Name</span>
                            <span class="value">{{ $booking->contact_name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Email</span>
                            <span class="value">{{ $booking->contact_email }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Phone</span>
                            <span class="value">{{ $booking->contact_phone }}</span>
                        </div>
                        @if($booking->contact_address)
                        <div class="info-row">
                            <span class="label">Address</span>
                            <span class="value">{{ $booking->contact_address }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="booking-info">
                    <h3>Payment Summary</h3>
                    <div class="info-grid">
                        <div class="info-row">
                            <span class="label">Subtotal</span>
                            <span class="value">৳ {{ number_format($booking->subtotal) }}</span>
                        </div>
                        @if($booking->discount_amount > 0)
                        <div class="info-row">
                            <span class="label">Discount</span>
                            <span class="value" style="color: #22c55e;">-৳ {{ number_format($booking->discount_amount) }}</span>
                        </div>
                        @endif
                        <div class="info-row" style="border-top: 2px solid #e2e8f0; padding-top: 12px; margin-top: 8px;">
                            <span class="label" style="font-weight: 600;">Total Amount</span>
                            <span class="amount">৳ {{ number_format($booking->total_amount) }}</span>
                        </div>
                    </div>
                </div>

                @if($booking->special_requests)
                <div class="booking-info">
                    <h3>Special Requests</h3>
                    <p style="margin: 0; color: #64748b;">{{ $booking->special_requests }}</p>
                </div>
                @endif

                <p style="text-align: center; margin-top: 25px;">
                    <a href="{{ config('app.url') }}/app/tour-bookings/{{ $booking->id }}" class="button">View in Admin Panel</a>
                </p>
            </div>
            
            <div class="footer">
                <p>This is an automated notification from {{ config('app.name') }}</p>
                <p>Booking received at {{ $booking->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
