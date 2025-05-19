# 📅 Appointment Reminder System API

A RESTful API built with **Laravel 10+** for managing appointment reminders. It supports **user authentication via Laravel Sanctum**, CRUD operations for **clients** and **appointments**, **timezone-aware reminder scheduling**, and optionally recurring appointments with customizable reminder preferences.

---

## 🚀 Features

- 🔐 **Authentication** with Laravel Sanctum
- 📆 **Appointment Management** (CRUD)
- 👤 **Client Management** (CRUD)
- ⏰ **Reminder Scheduling** via queues and Laravel scheduler
- 🌐 **Timezone Support** for accurate notifications
- 🔁 Optional **Recurring Appointments**
- 📧 Support for **Custom Reminder Offsets** (e.g., 1 hour before)
- 🎛️ **User Preferences** for reminder channels (email, SMS, etc.)
- 📄 JSON API responses
- ✅ Clean architecture using **Service Classes**, **Form Requests**

---

## 🛠️ Installation

1. **Clone the repository**

```bash
git clone https://github.com/your-username/appointment-reminder-api.git
cd appointment-reminder-api

./vendor/bin/sail up - to run docker
composer install
./vendor/bin/sail php artisan optimize:clear
./vendor/bin/sail php artisan migrate
create .env file and copy .env.example
create connection with your SQL GUI with the db parameters from .env.example
open terminal and type - ./vendor/bin/sail php artisan queue:work to run the queue workers
open second terminal and type - ./vendor/bin/sail php artisan shedule:work to run the deamon from running every job per minite

Using the restful api app 
   1. First post request /register using /register with name, email and password and password confirmation. 
     - Then get the access_token and use it as Bearer Token for every request.
   2. Post request to /login use email and password and get access_token and use it for every request.
   3. Make client with name, email,phone,prefers_sms/prefers_email, timezone example (Europe/Sofia)
   4. Adjust REMINDER_OFFSET_MINUTES in .env for every future appointment reminder time before sending automatic scheduling.
   5. Register an appointment for the client with request body - client_id,title,description,start_time(date)
   6. If client was created with 
      - prefers_email (true) will then receive an email at internal mailpit domain (http://appointment-reminder-system.test:8025/)
      - prefers_sms will have SMS Mock Log in laravel.log file in /storage
   7. Client full restful crud 
   8. Appointment 
        - get /appointments/upcoming - view upcoming appointments
        - get /appointments/past - view past appointments
        - post /appointments - create appointment
   9. Reminders 
      - get /reminders/scheduled - view scheduled reminders
      - get /reminders/sent - view sent reminders
      - post /reminders/{reminder}/toggle-channel - toggle channle with request body (channel - sms, email ) 
      
   10. Admin API routes 
    If the auth user is_admin. If not post request /users/{user}/admin-toggle to toggle users is_admin boolean flag.
    Otherwhise won't be able to perform the admin actions protected by Gate in AuthServiceProvider
      - post /admin/reminders - see all reminders
      - post /admin/appointments/stats - see all appointment stats
      
     
   11. Post request to /logout to logout
      
      
   
    
    
