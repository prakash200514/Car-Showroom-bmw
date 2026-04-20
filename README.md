
🏎️ World-Class BMW Car Showroom System
### Premium Production Edition | Intelligent Automotive Management

[![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![JS](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![GSAP](https://img.shields.io/badge/Animations-GSAP-88CE02?style=for-the-badge&logo=greensock&logoColor=white)](https://greensock.com/)

A sophisticated, high-performance web application designed for premium automobile retail. Inspired by the sleek aesthetics of BMW, this system provides a seamless bridge between luxury car browsing and administrative fleet management.

 ✨ Key Features

💎 Customer Experience
- **Premium UI/UX**: Glassmorphic design language with smooth GSAP and AOS scroll-reveal animations.
- **Dynamic Banners**: Integrated CMS-driven banner system supporting high-definition 4K images and muted auto-play videos (YouTube/MP4).
- **Virtual Showroom**: Interactive car listings with advanced filtering and 360° visual logic.
- **Service & Experience**: Integrated modules for booking service appointments, requesting test drives, and showroom visits.
- **BMW Spares Shop**: Fully functional e-commerce module for authentic BMW parts and accessories with cart and secure checkout.

🛠️ Administrative Control
- **Advanced Dashboard**: Real-time analytics powered by Chart.js, tracking bookings, revenue, and user engagement.
- **Fleet Management**: Comprehensive CRUD system for managing vehicle specifications, multi-image galleries, and pricing.
- **Banner CMS**: Easily update the homepage hero sliders and promotional banners through the admin panel.
- **Order & Service Tracking**: Centralized system to manage part orders, test drive requests, and service appointments.
- **User Management**: Unified interface to manage customer accounts and roles.

🚀 Quick Setup Guide

> [!IMPORTANT]
> This project is optimized for an **XAMPP** environment (Windows) or a standard LAMP stack.

### 1. Database Configuration
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Create a new database named `showroom_db`.
3. Import the `database.sql` file provided in the root directory.

### 2. Admin Account Initialization
1. Navigate to your project directory (e.g., `C:\xampp\htdocs\showroom`).
2. Run the initialization script by visiting: `http://localhost/showroom/setup_admin.php`.
3. Default Credentials:
   - **Email**: `admin@showroom.com`
   - **Password**: `Admin@123`
   
> [!WARNING]
> For security, immediately delete `setup_admin.php` and `reset_admin.php` once the admin account is created.

### 3. Application Access
- **Customer Portal**: `http://localhost/showroom/index.php`
- **Admin Control Panel**: `http://localhost/showroom/admin/login.php`

