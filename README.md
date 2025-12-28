# ✈️ Online Travel Agency (OTA) Platform

A full-stack **Online Travel Agency (OTA)** platform providing flight, hotel, and holiday bookings with wallet, refund, and admin management capabilities.  
Built with **Laravel, Vue.js, Inertia, Tailwind CSS, MySQL**, and designed for scalability, security, and enterprise readiness.

---

## 🚀 Features

### Customer Features

- Flight search & booking
- Hotel & holiday package booking
- Secure online payment integration
- Wallet & cashback system
- Booking cancellation & refund tracking
- User profile & booking history
- Email / SMS / push notifications

### Admin Features

- Booking management
- Refund & wallet reconciliation
- Content management (banners, offers)
- User & role management
- Reporting & analytics
- Third-party API configuration

---

## 🏗️ System Architecture

- **Frontend (Web):** Vue.js + Inertia + Tailwind CSS
- **Mobile App:** Flutter
- **Backend:** Laravel (Modular Architecture)
- **Database:** MySQL
- **Queue & Jobs:** Redis / Supervisor
- **Notifications:** Email, SMS, Push
- **Payments:** Payment Gateway APIs
- **Third-Party APIs:** Flights GDS, Hotel APIs

## 🎨 UI / UX

- Website UI inspired by https://sharetrip.net
- Wireframes & Figma specs available
- Mobile app UI designed separately

---

## 🔧 Installation

```bash
git clone https://github.com/your-org/ota-platform.git
cd ota-platform
composer install
npm install
npm run dev
php artisan key:generate
php artisan migrate --seed
php artisan serve
npm run dev
php artisan queue:work
```

---

## 🧪 Testing

```bash
php artisan test
```

---

## 🚀 Deployment

```bash
php artisan optimize
php artisan queue:work
```

## Scheduler (Cron)

```bash
* * * * * php /var/www/ota/artisan schedule:run >> /dev/null 2>&1
```

---

### 📞 Support

For technical or operational support:

- **Email:** mail4mjaman@gmail.com
- **Phone:** 01914 090747

---

## 📄 License

This project is proprietary.
Unauthorized copying, distribution, or use is strictly prohibited.

---

**Project Manager:** SA Faroque **Lead Developer:** Mehedi Jaman
