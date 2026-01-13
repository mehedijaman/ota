<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6; line-height: 1.6; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 20px; }
        .container { background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, #059669 0%, #10b981 100%); padding: 32px 24px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 24px; font-weight: 700; margin-bottom: 8px; }
        .header p { color: rgba(255, 255, 255, 0.9); font-size: 14px; }
        .success-icon { width: 64px; height: 64px; background-color: rgba(255, 255, 255, 0.2); border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; }
        .success-icon svg { width: 32px; height: 32px; color: #ffffff; }
        .content { padding: 32px 24px; }
        .greeting { font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 16px; }
        .message { color: #64748b; margin-bottom: 24px; }
        .amount-box { background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border: 2px solid #10b981; border-radius: 12px; padding: 24px; text-align: center; margin-bottom: 24px; }
        .amount-box label { display: block; font-size: 12px; color: #059669; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .amount-box .amount { font-size: 36px; font-weight: 700; color: #059669; }
        .amount-box .status { display: inline-block; background-color: #059669; color: #ffffff; padding: 4px 16px; border-radius: 9999px; font-size: 12px; font-weight: 600; margin-top: 8px; }
        .details-card { border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 24px; }
        .details-header { background-color: #f8fafc; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; }
        .details-header h3 { font-size: 14px; font-weight: 600; color: #475569; }
        .details-body { padding: 16px; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #64748b; font-size: 14px; }
        .detail-value { color: #1e293b; font-size: 14px; font-weight: 500; text-align: right; }
        .transaction-id { font-family: monospace; background-color: #f1f5f9; padding: 2px 8px; border-radius: 4px; }
        .info-box { background-color: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 0 8px 8px 0; padding: 16px; margin-bottom: 24px; }
        .info-box h4 { font-size: 14px; font-weight: 600; color: #1e40af; margin-bottom: 8px; }
        .info-box p { font-size: 14px; color: #1e40af; }
        .cta-section { text-align: center; padding: 24px 0; }
        .cta-button { display: inline-block; background: linear-gradient(135deg, #7c3aed 0%, #c026d3 100%); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 10px; font-weight: 600; font-size: 15px; }
        .footer { background-color: #f8fafc; padding: 24px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #64748b; font-size: 13px; margin-bottom: 8px; }
        .footer a { color: #7c3aed; text-decoration: none; }
        @media only screen and (max-width: 600px) {
            .wrapper { padding: 12px; }
            .content { padding: 24px 16px; }
            .detail-row { flex-direction: column; gap: 4px; }
            .detail-value { text-align: left; }
            .amount-box .amount { font-size: 28px; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <!-- Header -->
            <div class="header">
                <div class="success-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #fff;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1>Payment Successful!</h1>
                <p>Your payment has been processed successfully</p>
            </div>

            <!-- Content -->
            <div class="content">
                <p class="greeting">Hello {{ $user?->name ?? $application->contact_name ?? 'Valued Customer' }},</p>
                <p class="message">
                    Great news! We have received your payment for the visa application. Your application is now being processed by our team.
                </p>

                <!-- Amount Box -->
                <div class="amount-box">
                    <label>Amount Paid</label>
                    <div class="amount">৳{{ number_format($application->paid_amount ?? $application->total_amount, 2) }}</div>
                    <div class="status">✓ Payment Confirmed</div>
                </div>

                <!-- Transaction Details -->
                <div class="details-card">
                    <div class="details-header">
                        <h3>Transaction Details</h3>
                    </div>
                    <div class="details-body">
                        <div class="detail-row">
                            <span class="detail-label">Transaction ID</span>
                            <span class="detail-value"><span class="transaction-id">{{ $transactionId }}</span></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Payment Method</span>
                            <span class="detail-value">{{ ucfirst($paymentMethod) }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Payment Date</span>
                            <span class="detail-value">{{ now()->format('d M Y, h:i A') }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Application Number</span>
                            <span class="detail-value"><span class="transaction-id">{{ $application->application_number }}</span></span>
                        </div>
                    </div>
                </div>

                <!-- Application Details -->
                <div class="details-card">
                    <div class="details-header">
                        <h3>Application Summary</h3>
                    </div>
                    <div class="details-body">
                        <div class="detail-row">
                            <span class="detail-label">Visa Service</span>
                            <span class="detail-value">{{ $visa->name }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Destination</span>
                            <span class="detail-value">{{ $visa->country?->name ?? 'N/A' }}</span>
                        </div>
                        @if($application->intended_travel_date)
                        <div class="detail-row">
                            <span class="detail-label">Travel Date</span>
                            <span class="detail-value">{{ $application->intended_travel_date->format('d M, Y') }}</span>
                        </div>
                        @endif
                        <div class="detail-row">
                            <span class="detail-label">Total Travelers</span>
                            <span class="detail-value">{{ $application->total_travelers }} person(s)</span>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="info-box">
                    <h4>What Happens Next?</h4>
                    <p>
                        Our visa experts will now review your documents and submit your application to the embassy. 
                        You will receive updates via email and SMS throughout the process.
                    </p>
                </div>

                <!-- CTA Button -->
                <div class="cta-section">
                    <a href="{{ route('site.visa.confirmation', ['slug' => $visa->slug, 'application' => $application->application_number]) }}" class="cta-button">
                        Track Your Application
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>This is an automated payment receipt. Please keep it for your records.</p>
                <p>
                    <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a> | 
                    <a href="tel:+8801700000000">+880 1700-000000</a>
                </p>
                <p style="margin-top: 16px; font-size: 12px; color: #94a3b8;">
                    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
