<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visa Application Confirmation</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6; line-height: 1.6; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 20px; }
        .container { background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, #7c3aed 0%, #c026d3 100%); padding: 32px 24px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 24px; font-weight: 700; margin-bottom: 8px; }
        .header p { color: rgba(255, 255, 255, 0.9); font-size: 14px; }
        .success-icon { width: 64px; height: 64px; background-color: rgba(255, 255, 255, 0.2); border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; }
        .success-icon svg { width: 32px; height: 32px; color: #ffffff; }
        .content { padding: 32px 24px; }
        .greeting { font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 16px; }
        .message { color: #64748b; margin-bottom: 24px; }
        .application-number { background-color: #f1f5f9; border-radius: 12px; padding: 16px; text-align: center; margin-bottom: 24px; }
        .application-number label { display: block; font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .application-number span { font-size: 24px; font-weight: 700; color: #7c3aed; font-family: monospace; }
        .details-card { border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 24px; }
        .details-header { background-color: #f8fafc; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; }
        .details-header h3 { font-size: 14px; font-weight: 600; color: #475569; }
        .details-body { padding: 16px; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #64748b; font-size: 14px; }
        .detail-value { color: #1e293b; font-size: 14px; font-weight: 500; text-align: right; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
        .status-pending { background-color: #fef3c7; color: #d97706; }
        .status-paid { background-color: #dcfce7; color: #16a34a; }
        .travelers-list { margin-top: 8px; }
        .traveler-item { background-color: #f8fafc; border-radius: 8px; padding: 12px; margin-bottom: 8px; }
        .traveler-item:last-child { margin-bottom: 0; }
        .traveler-name { font-weight: 600; color: #1e293b; }
        .traveler-info { font-size: 13px; color: #64748b; margin-top: 4px; }
        .cta-section { text-align: center; padding: 24px 0; }
        .cta-button { display: inline-block; background: linear-gradient(135deg, #7c3aed 0%, #c026d3 100%); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 10px; font-weight: 600; font-size: 15px; }
        .next-steps { background-color: #faf5ff; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .next-steps h3 { font-size: 16px; font-weight: 600; color: #7c3aed; margin-bottom: 12px; }
        .step-item { display: flex; gap: 12px; margin-bottom: 12px; }
        .step-item:last-child { margin-bottom: 0; }
        .step-number { width: 24px; height: 24px; background-color: #7c3aed; color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; flex-shrink: 0; }
        .step-text { font-size: 14px; color: #475569; }
        .footer { background-color: #f8fafc; padding: 24px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #64748b; font-size: 13px; margin-bottom: 8px; }
        .footer a { color: #7c3aed; text-decoration: none; }
        .social-links { margin-top: 16px; }
        .social-links a { display: inline-block; margin: 0 8px; color: #94a3b8; }
        @media only screen and (max-width: 600px) {
            .wrapper { padding: 12px; }
            .content { padding: 24px 16px; }
            .detail-row { flex-direction: column; gap: 4px; }
            .detail-value { text-align: left; }
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1>Application Submitted!</h1>
                <p>Your visa application has been received successfully</p>
            </div>

            <!-- Content -->
            <div class="content">
                <p class="greeting">Hello {{ $user?->name ?? $application->contact_name ?? 'Valued Customer' }},</p>
                <p class="message">
                    Thank you for submitting your visa application. We have received your request and our team will begin processing it shortly.
                </p>

                <!-- Application Number -->
                <div class="application-number">
                    <label>Application Number</label>
                    <span>{{ $application->application_number }}</span>
                </div>

                <!-- Visa Details -->
                <div class="details-card">
                    <div class="details-header">
                        <h3>Visa Details</h3>
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
                        <div class="detail-row">
                            <span class="detail-label">Processing Type</span>
                            <span class="detail-value">{{ $application->processingTime?->name ?? 'Standard' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="details-card">
                    <div class="details-header">
                        <h3>Payment Summary</h3>
                    </div>
                    <div class="details-body">
                        <div class="detail-row">
                            <span class="detail-label">Total Amount</span>
                            <span class="detail-value" style="font-weight: 700; color: #7c3aed;">৳{{ number_format($application->total_amount, 2) }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Payment Status</span>
                            <span class="detail-value">
                                @if($application->payment_status === 'paid')
                                    <span class="status-badge status-paid">Paid</span>
                                @else
                                    <span class="status-badge status-pending">Pending</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Travelers -->
                @if($travelers && $travelers->count() > 0)
                <div class="details-card">
                    <div class="details-header">
                        <h3>Travelers ({{ $travelers->count() }})</h3>
                    </div>
                    <div class="details-body">
                        <div class="travelers-list">
                            @foreach($travelers as $traveler)
                            <div class="traveler-item">
                                <div class="traveler-name">{{ $traveler->full_name }}</div>
                                <div class="traveler-info">
                                    {{ ucfirst($traveler->traveler_type ?? 'Adult') }} • 
                                    Passport: {{ $traveler->passport_number }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Next Steps -->
                <div class="next-steps">
                    <h3>What Happens Next?</h3>
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div class="step-text">Our team will review your application within 24-48 hours.</div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div class="step-text">You may receive a call or email if additional documents are required.</div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="step-text">Once verified, your application will be submitted to the embassy.</div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">4</div>
                        <div class="step-text">You will be notified via email and SMS once your visa is ready.</div>
                    </div>
                </div>

                <!-- CTA Button -->
                @if($application->payment_status !== 'paid')
                <div class="cta-section">
                    <a href="{{ route('site.visa.payment', $application->application_number) }}" class="cta-button">
                        Complete Payment Now
                    </a>
                </div>
                @else
                <div class="cta-section">
                    <a href="{{ route('site.visa.confirmation', ['slug' => $visa->slug, 'application' => $application->application_number]) }}" class="cta-button">
                        View Application Status
                    </a>
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>If you have any questions, please contact our support team.</p>
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
