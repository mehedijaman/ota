✈️ OTA Platform (Laravel + Vue + Inertia)
A comprehensive Online Travel Agency (OTA) solution built with the VILT Stack (Vue, Inertia, Laravel, Tailwind). This platform supports B2C (End Users) and B2B (Travel Agents) for booking flights, hotels, holiday packages, and utility services.

🚀 Features
Flight Booking Engine: Integration with GDS (Amadeus/Sabre) for real-time one-way, round-trip, and multi-city search.

Hotel & Holiday Packages: Dynamic inventory management and booking flows.

B2B Agent Portal: specialized dashboard for travel agents with wallet/deposit system and custom markup tools.

Wallet System: Internal wallet for instant payments, refunds, and cashback rewards.

Utility Services: Visa assistance, Medical appointment booking, and Bill payments.

Role-Based Access: Granular permissions for Super Admin, Support Staff, Agents, and Customers.

Omnichannel: REST API support for the companion Flutter Mobile App.

🛠️ Technology Stack
Backend: Laravel 10 (PHP 8.2+)

Frontend: Vue.js 3, Inertia.js (SSR Enabled)

Styling: Tailwind CSS

Database: MySQL / PostgreSQL

Caching: Redis (Critical for Flight Search results)

Search: Laravel Scout / Meilisearch (Optional)

Mobile App: Flutter (Dart) - [Link to Mobile Repo]

📋 Prerequisites
Ensure you have the following installed on your local machine:

PHP >= 8.2

Composer

Node.js & NPM

MySQL or MariaDB

Redis

⚡ Installation Guide
Follow these steps to set up the project locally.

1. Clone the Repository
   Bash

git clone https://github.com/your-username/ota-platform.git
cd ota-platform 2. Install Backend Dependencies
Bash

composer install 3. Install Frontend Dependencies
Bash

npm install 4. Environment Setup
Copy the example environment file and configure your database credentials.

Bash

cp .env.example .env
Open .env and set your configuration:

Ini, TOML

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ota_db
DB_USERNAME=root
DB_PASSWORD=

# Redis is required for caching flight search results

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379 5. Generate Application Key
Bash

php artisan key:generate 6. Database Setup
Run migrations and seed the database with default roles and admin user.

Bash

php artisan migrate --seed 7. Run the Application
You need to run both the Laravel server and the Vite development server.

Terminal 1 (Backend):

Bash

php artisan serve
Terminal 2 (Frontend):

Bash

npm run dev
Terminal 3 (Queue Worker): Required for sending emails and generating PDF tickets.

Bash

php artisan queue:work
Visit http://localhost:8000 to see the application.

📂 Architecture Overview
This project follows a Service-Repository Pattern to handle the complexity of OTA logic and support both Web (Inertia) and Mobile (API) from a single codebase.

app/Services: Contains business logic (e.g., FlightSearchService, BookingService). Connects to external APIs.

app/Http/Controllers/Web: Returns Inertia::render responses for the browser.

app/Http/Controllers/Api: Returns JSON responses for the Flutter App.

database/migrations: Centralized schema including the polymorphic bookings table.

Key Data Models
User (Polymorphic roles)

Booking (Master table linked to flight_bookings, hotel_bookings)

WalletTransaction (Financial ledger)

🧪 Testing
Run the PHPUnit test suite to ensure booking logic is stable.

Bash

php artisan test
📚 API Documentation
The API documentation for mobile developers is available via Postman Collection.

[Link to Postman Documentation]

Base URL: http://localhost:8000/api/v1

🤝 Contributing
Create a Feature Branch (git checkout -b feature/AmazingFeature)

Commit your changes (git commit -m 'Add some AmazingFeature')

Push to the Branch (git push origin feature/AmazingFeature)

Open a Pull Request

📄 License
Distributed under the MIT License. See LICENSE for more information.

Project Manager: Your Name Lead Developer: Your Name
