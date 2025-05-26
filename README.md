# Scoring Application (LAMP Stack with Docker)

This project is a simple web application designed to manage and display scores in a contest or event. It's built using a classic LAMP (Linux, Apache, MySQL, PHP) stack, fully containerized with Docker for easy setup, deployment, and development.

---

## Table of Contents

1.  [Project Overview](#project-overview)
2.  [Features](#features)
3.  [Technical Stack](#technical-stack)
4.  [Setup & Installation](#setup--installation)
    * [Prerequisites](#prerequisites)
    * [Steps to Run](#steps-to-run)
5.  [Project Structure](#project-structure)
6.  [Database Schema](#database-schema)
7.  [Key Design Choices & Security Considerations](#key-design-choices--security-considerations)
8.  [Deployment Considerations](#deployment-considerations)
9.  [Features to Add if More Time](#features-to-add-if-more-time)

---

## 1. Project Overview

This application provides three main interfaces:

* **Public Scoreboard:** Displays real-time scores for all participants.
* **Judge Portal:** Allows authorized judges to submit scores for participants.
* **Admin Panel:** Enables administrators to manage judges.

The entire application is orchestrated using Docker Compose, ensuring a consistent development and deployment environment across different machines.

---

## 2. Features

* **Dockerized Environment:** All services (Apache, PHP, MySQL, phpMyAdmin) run in isolated Docker containers.
* **Automated Database Setup:** The MySQL database is automatically initialized with necessary tables (`users`, `judges`, `scores`) and sample data upon first container startup via `init.sql`.
* **Admin Panel:**
    * Add new judges (username and display name).
    * View a list of all existing judges.
* **Judge Portal:**
    * Select a judge and a participant from dropdowns.
    * Submit scores (between 1 and 100 points) for participants.
    * Includes **client-side validation** for point input using the Constraint Validation API.
    * Displays a list of all participating users.
* **Public Scoreboard:**
    * Displays participants and their total scores, sorted in descending order.
    * Highlights the top participant.
    * **Auto-refreshes** every 5 seconds using AJAX (Fetch API) to display real-time updates without page reloads.
* **User Feedback:** Dynamic and dismissible **toast messages** for success, error, and validation feedback.
* **Environment Configuration:** Database connection details are managed securely via a `.env` file.

---

## 3. Technical Stack

* **Web Server:** Apache HTTP Server
* **Server-side Scripting:** PHP 8.2
* **Database:** MySQL 8.0
* **Database Management:** phpMyAdmin
* **Containerization:** Docker & Docker Compose
* **Frontend:** HTML5, CSS3, JavaScript (Vanilla JS, Fetch API)

---

## 4. Setup & Installation

Follow these steps to get the application running on your local machine.

### Prerequisites

* **Docker Desktop:** Make sure Docker Desktop (which includes Docker Engine and Docker Compose) is installed and running on your system.
    * [Download Docker Desktop](https://www.docker.com/products/docker-desktop/)

### Steps to Run

1.  **Clone the Repository:**
    ```bash
    git clone [https://github.com/your-username/scoring-app.git](https://github.com/your-username/scoring-app.git) # Replace with your repo URL
    cd scoring-app
    ```

2.  **Build and Start Docker Containers:**
    From the project root directory, run:
    ```bash
    docker-compose up --build -d
    ```
    * `--build`: Forces Docker to rebuild images (useful if you make changes to `Dockerfile`).
    * `-d`: Runs the containers in detached mode (in the background).

    This command will:
    * Build the `web` service image (installing Apache, PHP, and extensions).
    * Pull the `mysql:8.0` and `phpmyadmin` images.
    * Create and start the `scoring-app-db`, `scoring-app-web`, and `scoring-app-phpmyadmin` containers.
    * Execute `init.sql` inside the `db` container on its first run to set up the database.

3.  **Access the Application:**
    * **Main Application:** Open your web browser and navigate to: `http://localhost/`
    * **Judge Portal:** `http://localhost/judge.php`
    * **Admin Panel:** `http://localhost/admin.php`
    * **Public Scoreboard:** `http://localhost/scoreboard.php`
    * **phpMyAdmin:** `http://localhost:8080/` (Login with `user` and the `MYSQL_PASSWORD` you set in `.env`)

4.  **Stop the Application:**
    When you're done, stop the containers and remove their resources:
    ```bash
    docker-compose down
    ```
    * `docker-compose down -v`: Use `-v` if you want to remove the named volume (`db_data`) as well, which will delete all your database data.

---

## 5. Project Structure

The project follows a clean and logical directory structure:

scoring-app/
├── public/                 # Web-accessible files (Document Root)
│   ├── css/
│   │   ├── global.css      # General application styles
│   │   └── style.css       # Main stylesheet for overall layout and components
│   ├── js/
│   │   ├── admin.js        # JavaScript for admin panel (future use/enhancements)
│   │   ├── judge.js        # JavaScript for judge portal (client-side validation)
│   │   ├── scoreboard.js   # JavaScript for real-time scoreboard updates
│   │   └── ui_utils.js     # Shared UI utilities (e.g., toast messages)
│   ├── admin.php           # Admin panel for judge management
│   ├── index.php           # Landing page / Placeholder
│   ├── judge.php           # Judge portal for score submission
│   └── scoreboard.php      # Public-facing scoreboard
├── src/                    # Backend PHP logic, not directly web-accessible
│   ├── db_connect.php      # Handles PDO database connection
│   └── get_scores.php      # API endpoint to fetch scoreboard data (JSON)
├── apache_config/
│   └── 000-default.conf    # Custom Apache virtual host configuration
├── docker-compose.yml      # Defines Docker services (web, db, phpmyadmin)
├── Dockerfile              # Instructions for building the web service image
├── init.sql                # SQL script for database initialization
└── README.md               # Project documentation

---

## 6. Database Schema

The application uses a simple MySQL schema with three tables:

* **`users`**: Represents participants in the contest.
    * `id` (INT, PK, AUTO_INCREMENT)
    * `username` (VARCHAR, UNIQUE)
    * `display_name` (VARCHAR)
    * `created_at` (TIMESTAMP)

* **`judges`**: Represents individuals who submit scores.
    * `id` (INT, PK, AUTO_INCREMENT)
    * `username` (VARCHAR, UNIQUE)
    * `display_name` (VARCHAR)
    * `created_at` (TIMESTAMP)

* **`scores`**: Stores individual score submissions.
    * `id` (INT, PK, AUTO_INCREMENT)
    * `user_id` (INT, FK to `users.id`)
    * `judge_id` (INT, FK to `judges.id`)
    * `points` (INT)
    * `submitted_at` (TIMESTAMP)

The `init.sql` script creates these tables and populates `users` with some sample data.

---

## 7. Key Design Choices & Security Considerations

* **Containerization (Docker):**
    * **Isolation:** Each service runs in its own isolated environment, preventing conflicts and simplifying dependency management.
    * **Portability:** The entire environment can be set up identically on any machine with Docker, eliminating "it works on my machine" issues.
    * **Reproducibility:** Ensures consistent behavior from development to production.
* **`src/` directory for PHP logic:**
    * Files like `db_connect.php` and `get_scores.php` are placed outside the web server's public document root (`public/`). This is a **security best practice** as it prevents direct browser access to sensitive logic or configuration files. They are included into `public/` PHP files using `require_once '../src/...'`.
* **`depends_on` with `service_healthy`:**
    * In `docker-compose.yml`, the `web` service `depends_on` the `db` service with `condition: service_healthy`. This is crucial to ensure the web server container doesn't try to connect to the database *before* the database service is fully up and ready to accept connections, preventing "Connection refused" errors during startup.
* **Prepared Statements (PDO):**
    * All database interactions involving user input (e.g., adding judges, submitting scores) utilize PDO prepared statements with bound parameters (`:parameter`). This is the primary defense against **SQL Injection attacks**.
* **Environment Variables (`.env`):**
    * Sensitive database credentials are not hardcoded in the PHP files but are loaded from the `.env` file. This keeps sensitive information out of the codebase and Git repository, and allows for easy configuration changes across different environments (development, staging, production).
* **Input Validation:**
    * **Server-side:** All form submissions are validated on the server (e.g., points range, non-empty fields) to prevent invalid data from entering the database and to guard against malicious input.
    * **Client-side (Constraint Validation API):** Provides immediate feedback to the user, enhancing UX and reducing unnecessary server requests. This is a progressive enhancement.
* **Error Logging:**
    * Database errors are caught and logged to the Apache error log (`docker-compose logs web`), rather than being displayed directly to the user. This prevents sensitive error details from being exposed to the public. A generic user-friendly error message is displayed instead.
* **HTTPS (Conceptual for Production):**
    * While not implemented in this development environment (due to complexity for a basic project), for a production deployment, **HTTPS is absolutely critical**. This encrypts communication between the user's browser and the server, protecting sensitive data (like potential login credentials if authentication were implemented). This would typically involve a reverse proxy (e.g., Nginx) and SSL certificates (e.g., Let's Encrypt).

---

## 8. Deployment Considerations

Deploying this Dockerized application involves setting it up on a server that supports Docker.

* **Not suitable for Static Hosting (e.g., GitHub Pages):** This application cannot be hosted on platforms like GitHub Pages because it requires a dynamic backend (PHP interpreter) and a database (MySQL), which static hosting services do not provide.
* **Recommended Deployment Environment:** A Virtual Private Server (VPS) from a cloud provider (e.g., DigitalOcean, AWS EC2, Google Cloud, Oracle Cloud) where you can install Docker and Docker Compose.
* **Environment Variables for Production:** On deployment, you would manually create the `.env` file on the server with production-specific, strong credentials that are not committed to Git.
* **HTTPS:** For any public-facing application, implementing HTTPS is crucial. This typically involves configuring a reverse proxy (like Nginx or Apache) in front of your Dockerized web service and obtaining an SSL certificate.

---

## 9. Features to Add if More Time

Here are some potential enhancements to further develop the application:

* **User Authentication System:** Implement proper login for judges and administrators using sessions/tokens and secure password hashing. This is the most critical next step for a production-ready application.
* **Improved Judge & User Management:**
    * Allow editing/deleting existing judges and users from the admin panel.
    * Implement user/judge search and pagination for larger lists.
* **Score Editing/Deletion:** Provide functionality for judges to edit or revoke previously submitted scores.
* **Advanced Scoreboard Features:**
    * Filter scoreboard by judge or time period.
    * Display individual scores from different judges for a participant.
    * Visualizations (charts/graphs) for score trends.
* **Enhanced UI/UX:**
    * **More Advanced CSS:** Implement a more polished and modern design using a CSS framework (e.g., Tailwind CSS, Bootstrap) or a custom design system for better visual appeal and consistency.
    * **Full Responsiveness:** Ensure the entire application layout (forms, tables, navigation) adapts perfectly to various screen sizes (mobile, tablet, desktop).
    * **Loading States:** Add visual feedback (spinners, "loading..." messages) for AJAX requests, especially on the scoreboard, to improve user experience.
* **API Development:** Create a more formal RESTful API for score submission and retrieval, separating the backend from the frontend more cleanly.
* **Error Pages:** Implement custom 404 (Not Found) and 500 (Server Error) pages for a more professional user experience.
* **Automated Testing:** Implement unit tests for PHP functions and integration tests for key application flows.





