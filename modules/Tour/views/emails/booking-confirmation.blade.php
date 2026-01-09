<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155; margin: 0; padding: 0; background-color: #f1f5f9; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .card { background: #ffffff; border-radius: 16px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); overflow: hidden; }
        .header { background: linear-gradient(135deg, #ea580c 0%, #f59e0b 100%); padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; }
        .header p { color: rgba(255,255,255,0.9); margin: 10px 0 0; }
        .content { padding: 30px; }
        .booking-number { background: #fff7ed; border: 2px dashed #ea580c; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 25px; }
        .booking-number span { display: block; font-size: 12px; color: #78716c; text-transform: uppercase; letter-spacing: 1px; }
        .booking-number strong { font-size: 28px; color: #ea580c; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .details-table td { padding: 12px 0; border-bottom: 1px solid #e2e8f0; }
        .details-table td:first-child { color: #64748b; font-size: 14px; }
        .details-table td:last-child { text-align: right; font-weight: 600; color: #1e293b; }
        .price-row td { font-size: 18px !important; border-bottom: none !important; padding-top: 20px; }
        .price-row td:last-child { color: #ea580c !important; }
        .info-box { background: #eff6ff; border-radius: 12px; padding: 20px; margin-bottom: 25px; }
        .info-box h3 { margin: 0 0 10px; color: #1e40af; font-size: 16px; }
        .info-box ul { margin: 0; padding: 0 0 0 20px; color: #3b82f6; }
        .info-box li { margin-bottom: 8px; }
        .button { display: inline-block; background: linear-gradient(135deg, #ea580c 0%, #f59e0b 100%); color: #ffffff; text-decoration: none; padding: 14px 30px; border-radius: 10px; font-weight: 600; margin-top: 10px; }
        .footer { text-align: center; padding: 25px; color: #64748b; font-size: 13px; }
        .footer a { color: #ea580c; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>✓ Booking Confirmed!</h1>
                <p>Thank you for choosing {{ config('app.name') }}</p>
            </div>
            
            <div class="content">
                <p>Dear <strong>{{ $booking->contact_name }}</strong>,</p>
                <p>Your tour booking has been successfully received. Our team will review your booking and contact you shortly to confirm the details and payment instructions.</p>
                
                <div class="booking-number">
                    <span>Booking Reference</span>
                    <strong>{{ $booking->booking_number }}</strong>
                </div>

                <table class="details-table">
                    <tr>
                        <td>Tour Package</td>
                        <td>{{ $booking->tour->name }}</td>
                    </tr>
                    <tr>
                        <td>Duration</td>
                        <td>{{ $booking->tour->duration }}</td>
                    </tr>
                    @if($booking->departureDate)
                    <tr>
                        <td>Departure Date</td>
                        <td>{{ $booking->departureDate->departure_date->format('M d, Y') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Adults</td>
                        <td>{{ $booking->adults_count }}</td>
                    </tr>
                    @if($booking->children_count)
                    <tr>
                        <td>Children</td>
                        <td>{{ $booking->children_count }}</td>
                    </tr>
                    @endif
                    @if($booking->infants_count)
                    <tr>
                        <td>Infants</td>
                        <td>{{ $booking->infants_count }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Contact Phone</td>
                        <td>{{ $booking->contact_phone }}</td>
                    </tr>
                    <tr class="price-row">
                        <td>Total Amount</td>
                        <td>৳ {{ number_format($booking->total_amount) }}</td>
                    </tr>
                </table>

                <div class="info-box">
                    <h3>📋 What happens next?</h3>
                    <ul>
                        <li>Our team will verify the booking within 24 hours</li>
                        <li>You will receive a confirmation call/email</li>
                        <li>Payment instructions will be shared after confirmation</li>
                        <li>E-ticket will be sent after payment completion</li>
                    </ul>
                </div>

                @if($booking->special_requests)
                <p><strong>Your Special Requests:</strong><br>{{ $booking->special_requests }}</p>
                @endif

                <p style="text-align: center;">
                    <a href="{{ config('app.url') }}/tour/{{ $booking->tour->slug }}" class="button">View Tour Details</a>
                </p>
            </div>
            
            <div class="footer">
                <p>Questions? Contact us at <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a></p>
                <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
