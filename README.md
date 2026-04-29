# HealConnect

HealConnect is a web-based platform designed for physical therapy services. This improves access to rehabilitation by enabling patients to connect directly with certified therapists in a secure, digital environment.

## Team Members

- Espinoza, Joshua
- Gozun, Jayvee
- Miranda, Charles Kit
- Mungcal, Lord Alfred (Project Lead)

## System Access

**Live Deployment:** [Not Yet Deployed]

### Default Credentials

Use the following credentials to access the system for testing and evaluation purposes:

| Role | Username / Email | Password |
|---|---|---|
| Admin | `admin@gmail.com` | `admin1234` |


## Technical Specifications

### Development Requirements

- PHP 8.2 or higher
- Composer
- Node.js & npm
- MySQL / MariaDB (or compatible relational database)

### Technology Stack

**Frontend:**
- HTML5 & Vanilla CSS (Custom stylesheets)
- Vanilla JavaScript
- Laravel Echo & Pusher JS (WebSockets)

**Backend:**
- Laravel v12.0
- PHP 8.2+
- PayMongo (Payment gateways)
- Pusher PHP Server (Broadcasting)


## Local Installation Guide

To run this application on your local machine, follow these steps:

1. **Clone the Repository**
   ```bash
   git clone [https://github.com/CharlesMiranda13/Capstone-1.git]
   cd Capstone/HealConnect
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Configuration**
   Copy the example environment file and configure your database and API credentials.
   ```bash
   cp .env.example .env
   ```
   *Note: Ensure `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`,PayMongo, and Pusher keys are correctly set in the `.env` file.*

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations and Seeders**
   This step provisions your database with the necessary schema and default data (including the admin account).
   ```bash
   php artisan migrate --seed
   ```

6. **Compile Frontend Assets**
   ```bash
   npm run dev
   ```

7. **Start the Local Server**
   In a separate terminal session, start the Laravel development server:
   ```bash
   php artisan serve
   ```
   
The application will be available at `http://localhost:8000`.
 