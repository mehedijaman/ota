<?php

namespace Modules\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Page\Models\Page;

use function Laravel\Prompts\info;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        info('Creating Pages...');

        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => $this->getAboutUsContent(),
                'meta_tag_title' => 'About Us - Your Trusted Travel Partner',
                'meta_tag_description' => 'Learn about our mission, vision, and the team behind your seamless travel experiences. We are committed to making travel accessible and memorable for everyone.',
                'published_at' => now(),
                'active' => true,
            ],
            [
                'title' => 'Why Choose Us',
                'slug' => 'why-us',
                'content' => $this->getWhyUsContent(),
                'meta_tag_title' => 'Why Choose Us - Benefits of Traveling With Us',
                'meta_tag_description' => 'Discover why thousands of travelers trust us for their journeys. Best prices, 24/7 support, and a commitment to excellence.',
                'published_at' => now(),
                'active' => true,
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'content' => $this->getTermsContent(),
                'meta_tag_title' => 'Terms of Service - Usage Guidelines',
                'meta_tag_description' => 'Read our terms of service to understand your rights and responsibilities when using our platform.',
                'published_at' => now(),
                'active' => true,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => $this->getPrivacyPolicyContent(),
                'meta_tag_title' => 'Privacy Policy - How We Protect Your Data',
                'meta_tag_description' => 'Learn how we collect, use, and protect your personal information. Your privacy is our priority.',
                'published_at' => now(),
                'active' => true,
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact-us',
                'content' => $this->getContactUsContent(),
                'meta_tag_title' => 'Contact Us - Get In Touch',
                'meta_tag_description' => 'Have questions? Reach out to our team. We are here to help you with your travel needs.',
                'published_at' => now(),
                'active' => true,
            ],
            [
                'title' => 'FAQ',
                'slug' => 'faq',
                'content' => $this->getFaqContent(),
                'meta_tag_title' => 'Frequently Asked Questions',
                'meta_tag_description' => 'Find answers to common questions about bookings, payments, cancellations, and more.',
                'published_at' => now(),
                'active' => true,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }

        info('Pages created.');
    }

    protected function getAboutUsContent(): string
    {
        return <<<'HTML'
<h2>Our Story</h2>
<p>Founded with a passion for travel and a commitment to excellence, we have grown from a small travel agency into a comprehensive travel management platform trusted by thousands of travelers and partners worldwide.</p>

<h2>Our Mission</h2>
<p>To make travel accessible, seamless, and memorable for everyone. We believe that exploring the world should be an enriching experience, not a stressful one.</p>

<h2>Our Vision</h2>
<p>To become the most trusted travel partner in Bangladesh and beyond, known for our exceptional service, competitive pricing, and innovative solutions.</p>

<h2>What Sets Us Apart</h2>
<ul>
    <li><strong>Expert Team:</strong> Our travel consultants have years of experience and deep knowledge of destinations worldwide.</li>
    <li><strong>Best Prices:</strong> We negotiate directly with airlines, hotels, and tour operators to bring you the best deals.</li>
    <li><strong>24/7 Support:</strong> Travel doesn't stop, and neither do we. Our support team is available around the clock.</li>
    <li><strong>Trusted by Thousands:</strong> We've helped thousands of travelers create unforgettable memories.</li>
</ul>
HTML;
    }

    protected function getWhyUsContent(): string
    {
        return <<<'HTML'
<h2>Why Travel With Us?</h2>
<p>Choosing the right travel partner can make all the difference in your journey. Here's why travelers consistently choose us:</p>

<h3>1. Competitive Pricing</h3>
<p>We leverage our industry relationships to offer you the best possible prices on flights, hotels, tours, and visa services. No hidden fees, no surprises.</p>

<h3>2. Expert Guidance</h3>
<p>Our experienced travel consultants are here to help you plan every aspect of your trip, from destination selection to visa requirements.</p>

<h3>3. Wide Selection</h3>
<p>Access thousands of flights, hotels, and tour packages all in one place. Compare options and choose what works best for you.</p>

<h3>4. Secure Booking</h3>
<p>Your personal and payment information is protected with industry-standard encryption. Book with confidence.</p>

<h3>5. 24/7 Customer Support</h3>
<p>Whether you're booking or already on your trip, our support team is available around the clock to assist you.</p>

<h3>6. Easy Cancellation</h3>
<p>Plans change. Our flexible booking options and clear cancellation policies give you peace of mind.</p>

<h3>7. Trusted by Thousands</h3>
<p>Join the community of satisfied travelers who have made their journeys memorable with our services.</p>
HTML;
    }

    protected function getTermsContent(): string
    {
        return <<<'HTML'
<h2>Terms of Service</h2>
<p><em>Last updated: January 2026</em></p>

<h3>1. Acceptance of Terms</h3>
<p>By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement.</p>

<h3>2. Use of Services</h3>
<p>You agree to use our services only for lawful purposes and in accordance with these Terms. You agree not to use our services:</p>
<ul>
    <li>In any way that violates any applicable law or regulation</li>
    <li>To transmit any material that is defamatory, offensive, or otherwise objectionable</li>
    <li>To impersonate or attempt to impersonate the company, an employee, or another user</li>
</ul>

<h3>3. Booking and Payment</h3>
<p>All bookings are subject to availability. Prices are subject to change until payment is confirmed. Payment must be made in full at the time of booking unless otherwise specified.</p>

<h3>4. Cancellation and Refunds</h3>
<p>Cancellation policies vary by service provider. Please review the specific cancellation policy for each booking. Refunds, if applicable, will be processed according to the provider's policy.</p>

<h3>5. Liability</h3>
<p>We act as an intermediary between you and travel service providers. We are not liable for any loss, damage, or injury caused by third-party service providers.</p>

<h3>6. Changes to Terms</h3>
<p>We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting to the website.</p>

<h3>7. Contact</h3>
<p>For questions about these Terms, please contact our support team.</p>
HTML;
    }

    protected function getPrivacyPolicyContent(): string
    {
        return <<<'HTML'
<h2>Privacy Policy</h2>
<p><em>Last updated: January 2026</em></p>

<h3>1. Information We Collect</h3>
<p>We collect information you provide directly to us, including:</p>
<ul>
    <li>Name, email address, phone number</li>
    <li>Passport and travel document information</li>
    <li>Payment information</li>
    <li>Travel preferences and booking history</li>
</ul>

<h3>2. How We Use Your Information</h3>
<p>We use the information we collect to:</p>
<ul>
    <li>Process and confirm your bookings</li>
    <li>Communicate with you about your reservations</li>
    <li>Send promotional offers and updates (with your consent)</li>
    <li>Improve our services and user experience</li>
    <li>Comply with legal obligations</li>
</ul>

<h3>3. Information Sharing</h3>
<p>We share your information only with:</p>
<ul>
    <li>Travel service providers (airlines, hotels, etc.) to fulfill your bookings</li>
    <li>Payment processors to complete transactions</li>
    <li>Legal authorities when required by law</li>
</ul>

<h3>4. Data Security</h3>
<p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>

<h3>5. Your Rights</h3>
<p>You have the right to:</p>
<ul>
    <li>Access your personal data</li>
    <li>Request correction of inaccurate data</li>
    <li>Request deletion of your data</li>
    <li>Opt out of marketing communications</li>
</ul>

<h3>6. Cookies</h3>
<p>We use cookies to enhance your browsing experience. You can control cookie preferences through your browser settings.</p>

<h3>7. Contact</h3>
<p>For any privacy-related concerns, please contact our support team.</p>
HTML;
    }

    protected function getContactUsContent(): string
    {
        return <<<'HTML'
<h2>Get In Touch</h2>
<p>We'd love to hear from you! Whether you have a question about our services, need help with a booking, or just want to say hello, our team is here to help.</p>

<h3>Customer Support</h3>
<p>Our customer support team is available 24/7 to assist you with any queries or concerns.</p>

<h3>Business Inquiries</h3>
<p>For partnership opportunities, corporate travel solutions, or media inquiries, please reach out to our business development team.</p>

<h3>Feedback</h3>
<p>Your feedback helps us improve. Share your experience with us - we value every suggestion!</p>
HTML;
    }

    protected function getFaqContent(): string
    {
        return <<<'HTML'
<h2>Frequently Asked Questions</h2>

<h3>Booking & Reservations</h3>

<h4>How do I make a booking?</h4>
<p>Simply search for your desired flight, hotel, or tour, select your options, and proceed to payment. You'll receive a confirmation email once your booking is complete.</p>

<h4>Can I modify my booking?</h4>
<p>Yes, most bookings can be modified. Log in to your account or contact our support team for assistance. Please note that modification fees may apply.</p>

<h3>Payment</h3>

<h4>What payment methods do you accept?</h4>
<p>We accept major credit cards, debit cards, mobile banking (bKash, Nagad), and bank transfers.</p>

<h4>Is my payment information secure?</h4>
<p>Absolutely. We use industry-standard encryption to protect your payment information.</p>

<h3>Cancellations & Refunds</h3>

<h4>How do I cancel a booking?</h4>
<p>You can cancel through your account dashboard or by contacting our support team. Cancellation policies vary by service provider.</p>

<h4>How long does it take to receive a refund?</h4>
<p>Refunds are typically processed within 7-14 business days, depending on your payment method and the service provider's policy.</p>

<h3>Support</h3>

<h4>How can I contact customer support?</h4>
<p>You can reach us via phone, email, WhatsApp, or live chat. Our support team is available 24/7.</p>
HTML;
    }
}
