# HealConnect

HealConnect is a web-based platform designed for physical therapy services. It improves access to rehabilitation by enabling patients to connect directly with certified therapists in a secure, digital environment.

## Table of Contents
- [About](#about)
- [Live Demo](#live-demo)
- [Getting Started](#getting-started)
- [Credentials](#credentials)
- [User Roles](#user-roles)
- [Usage Guide](#usage-guide)
- [API Docs](#api-docs)
- [Contributing](#contributing)
- [Security](#security)
- [License](#license)
- [Contact](#contact)

---

## About
HealConnect is a web-based telehealth platform designed to improve access to physical therapy services through remote consultations, personalized rehabilitation support, and progress monitoring. The platform connects patients and licensed physical therapists in a secure and user-friendly environment, offering features such as appointment scheduling, virtual sessions, messaging, and treatment tracking. Developed to address accessibility challenges, especially for individuals with mobility limitations and underserved communities, HealConnect aims to make physical therapy more convenient, efficient, and inclusive through digital healthcare innovation.

## Live Demo
**Status:** [Not Yet Deployed]  
The application is currently in the final stages of development and will be hosted on **Hostinger**. Once deployed, the live link will be provided here.

---

## Getting Started

### Local Installation
To run this application on your local machine, ensure you have the following tools installed:
- **PHP 8.2+**
- **Composer**
- **Node.js & npm**
- **MySQL / MariaDB**

**Steps to install:**
1. **Clone the Repository**
   ```bash
   git clone https://github.com/CharlesMiranda13/Capstone-1.git
   cd Capstone/HealConnect
   ```
2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```
3. **Environment Configuration**
   ```bash
   cp .env.example .env
   # Update DB_DATABASE, DB_USERNAME, and DB_PASSWORD in your .env file
   ```
4. **Setup Application**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   npm run dev
   ```
5. **Start the Server**
   ```bash
   php artisan serve
   ```

### Cloud Configuration
- **Hosting Provider:** Hostinger
- **Status:** Deployment in progress
- **Deployment Lead:** Charles Kit Miranda

---

## Credentials
Use the following default credentials to access the administrative dashboard for testing:

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@gmail.com` | `admin1234` |

*Note: Patient and Therapist accounts can be created via the registration forms on the landing page.*

---

## User Roles
1.  **Admin:** Full system oversight, user verification, subscription management, and report generation.
2.  **Patient:** Search for therapists, book/manage appointments, view personal medical records, and message therapists.
3.  **Independent Therapist:** Manage personal practice, set availability, maintain patient EHRs, and conduct consultations.
4.  **Clinic Therapist:** Similar to independent therapists but managed under a clinic entity with employee management features.

---

## Usage Guide
- **Patients:** Navigate to the "Find a Therapist" section to book your first session. Use the "Messages" tab to communicate with your therapist.
- **Therapists:** Access the "Clients" dashboard to update EHRs, treatment plans, and progress notes. Ensure your "Availability" is set to receive bookings.
- **Admins:** Use the "Manage Users" section to verify new registrations and the "Reports" section to monitor platform activity.

---

## API Docs
HealConnect utilizes Laravel's routing system. Primary endpoints include:
- `GET /api/messages/fetch`: Retrieves conversation history for the authenticated user.
- `POST /api/messages/send`: Sends text, files, or voice messages securely.
- `POST /video/create-room`: Initializes a secure video consultation room.

---

## Contributing
Contributions are what make the open-source community such an amazing place to learn, inspire, and create.
1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## Security
HealConnect prioritizes data privacy. All User records are stored securely, and authentication is handled via Laravel's robust security features. If you discover any security vulnerabilities, please report them directly to the project lead for immediate resolution.

---

## License
Distributed under the **MIT License**.

---

## Contact
**Project Lead:** Mungcal, Lord Alfred  
**Lead Developer:** Miranda, Charles Kit  
**GitHub:** [CharlesMiranda13](https://github.com/CharlesMiranda13)