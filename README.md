📅 Appointment Reminder System API

A RESTful API built with Laravel 10+ for managing appointment reminders. It supports user authentication via Laravel Sanctum, CRUD operations for clients and appointments, timezone-aware reminder scheduling, recurring appointments, analytics, and customizable reminder preferences.

---

🚀 Features

- Authentication with Laravel Sanctum
- Appointment Management (CRUD)
- Client Management (CRUD)
- Timezone-aware Reminder Scheduling via queues and Laravel scheduler
- Recurring Appointments with flexible recurrence rules (weekly, monthly, etc.)
- Support for Custom Reminder Offsets (e.g., 1 hour before appointment)
- User Preferences for reminder channels (email, SMS)
- Analytics to track sent, failed, and upcoming reminders
- Retry Logic for failed reminders with configurable max retries and retry delays
- Appointment status management: mark appointments and recurrence instances as completed, canceled, or missed
- JSON API responses with clean architecture using Service Classes and Form Requests

---

🛠️ Installation & Setup

1. Clone the repository

git clone https://github.com/your-username/appointment-reminder-api.git
cd appointment-reminder-api

2. Start Docker and install dependencies

./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail php artisan optimize:clear
./vendor/bin/sail php artisan migrate

3. Configure environment

- Copy .env.example to .env and update database and mail settings.
- Set REMINDER_OFFSET_MINUTES in .env to configure how long before appointments reminders are sent.
- Configure REMINDERS_MAX_RETRIES and REMINDERS_RETRY_DELAY_MINUTES for retry logic.

4. Run queue workers and scheduler

Open two terminals:

./vendor/bin/sail php artisan queue:work
./vendor/bin/sail php artisan schedule:work

---

📡 Using the API

Authentication

- Register: POST /register with name, email, password, password_confirmation
- Login: POST /login with email, password
- Use the returned access_token as Bearer token for all authenticated requests
- Logout: POST /logout

Clients

- Full CRUD on /clients
- Include prefers_sms, prefers_email, and timezone (e.g., Europe/Sofia) for each client

Appointments

- Create appointment: POST /appointments with client_id, title, description, start_time
- View upcoming appointments: GET /appointments/upcoming
- View past appointments: GET /appointments/past
- Manage recurring appointments with recurrence rules (none, weekly, monthly) and recurrence_ends_at
- Update appointment and recurrence instance statuses (completed, canceled, missed)

Reminders

- View scheduled reminders: GET /reminders/scheduled
- View sent reminders: GET /reminders/sent
- Toggle reminder channel (SMS/email): POST /reminders/{reminder}/toggle-channel
- Analytics endpoint: GET /reminders/analytics — returns counts of upcoming, sent, and failed reminders
- Retry logic automatically retries failed reminders based on configured max retries and delay

Admin Routes (requires is_admin flag)

- Toggle admin status for users: POST /users/{user}/admin-toggle
- View all reminders: POST /admin/reminders
- View appointment statistics: POST /admin/appointments/stats

---

🔧 Additional Notes

- SMS notifications are mocked and logged to Laravel logs (storage/logs/laravel.log)
- Email notifications use internal Mailpit domain (e.g., http://appointment-reminder-system.test:8025/)
- Recurring appointments are generated automatically daily at 3:00 AM via scheduled command
- Reminders are sent every minute, respecting client timezones and configured offsets
- Failed reminders are retried automatically with exponential backoff and max retry limits

---

If you want help with testing, deployment, or extending the system, feel free to ask!
