# World-Class Car Showroom System

## Premium Production Edition

### Setup Guide

1.  **Database Configuration**:
    *   Open phpMyAdmin (`http://localhost/phpmyadmin`).
    *   Create a new database named `showroom_db`.
    *   Import the `database.sql` file located in the root directory.

2.  **Admin Account Setup**:
    *   Open your browser and navigate to `http://localhost/showroom/setup_admin.php`.
    *   This will create the default admin account:
        *   **Email**: `admin@showroom.com`
        *   **Password**: `Admin@123`
    *   **IMPORTANT**: Delete `setup_admin.php` after use for security.

3.  **Folder Structure**:
    *   Ensure the `showroom` folder is in your `htdocs` directory (e.g., `C:\xampp\htdocs\showroom`).
    *   Create an `uploads` folder if it doesn't exist (the system should have created it, but verify).

4.  **Running the Project**:
    *   **Customer Portal**: `http://localhost/showroom/index.php`
    *   **Admin Panel**: `http://localhost/showroom/admin/login.php`

### Features Implemented

*   **Authentication**: Secure Login/Register with password hashing.
*   **Roles**: Customer and Admin specific dashboards.
*   **Premium UI**: Glassmorphism design, Dark Mode toggle (localStorage), GSAP animations.
*   **Cars**: Listing with filters, Details with 360 viewer (placeholder logic), EMI Calculator.
*   **Showrooms**: Map integration.
*   **Admin**: Dashboard with Chart.js analytics.

### Libraries Used (CDN)
*   GSAP (Animations)
*   AOS (Scroll Reveal)
*   Swiper.js (Sliders)
*   Chart.js (Charts)
*   Font Awesome (Icons)
*   Google Fonts (Inter)

### Notes
*   This system uses PDO for database interactions.
*   360 Viewer expects images in `car_360_frames` table.
*   Dark mode preference is saved in localStorage.
