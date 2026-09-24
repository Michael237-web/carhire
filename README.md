# 🚗 DriveElite Car Hire

A modern car hire and booking platform with a full fleet showcase, real-time availability, and an admin management panel — built with **PHP**, **MySQL**, and **vanilla JavaScript**.

![Status](https://img.shields.io/badge/status-active-brightgreen)
![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-blue)

---
## 🖼️ Screenshots

### 🏠 Homepage
![Homepage](carhire/screenshots/home.png)

### 🚗 Fleet / Vehicle Details
![Fleet](carhire/screenshots/about.png)

### 📅 Booking
![Booking](carhire/screenshots/book.png)

### ❓ FAQ
![FAQ](carhire/screenshots/faq.png)

### 📞 Contact
![Contact](carhire/screenshots/contact.png)
---

## 📖 Table of Contents

- [About](#-about)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Screenshots](#-screenshots)
- [Project Structure](#-project-structure)
- [Local Setup](#-local-setup)
- [Configuration](#-configuration)
- [Deployment](#-deployment)
- [Security](#-security)
- [License](#-license)
- [Contact](#-contact)

---

## 📌 About

**DriveElite Car Hire** is a complete vehicle rental platform designed for both customers and fleet operators.

For **customers**, it offers a fast, mobile-friendly way to browse the fleet, check availability by date, view car details, and book a vehicle — all in a smooth single-page-app-style experience.

For **administrators**, it provides a dashboard to manage vehicles, bookings, and site-wide settings with no technical knowledge required.

The project is designed to be deployed on any shared hosting environment (tested on **AwardSpace**) as well as run locally with **XAMPP**.

---

## ✨ Features

### 🚙 Customer-Facing

- **Homepage** — Hero with search, featured vehicles, categories, testimonials
- **Fleet showcase** — Grid layout with vehicle cards (image, price/day, category, transmission, seats)
- **Advanced filters** — Brand, category, price range, seats, transmission, fuel type
- **Vehicle details** — Gallery, specs, features, availability calendar, booking form
- **Booking flow** — Pickup date, return date, pickup location, driver details
- **Confirmation page** — Booking reference + summary
- **FAQ page** — Common questions answered
- **About page** — Company story and mission
- **Contact form** — Enquiries with email notification
- **Privacy & Terms** — Legal pages
- **Custom error pages** — 400, 401, 403, 404, 500, 503
- **WhatsApp integration** — Floating button for instant enquiries
- **Clean URLs** — SPA-style routing (`/car/toyota-prado-5`, `/fleet`, `/admin/dashboard`)
- **Fully responsive** — Mobile-first design

### 🛠️ Admin Dashboard

- **Dashboard** — Overview of fleet, bookings, revenue
- **Vehicle management** — Add, edit, delete cars; upload images; set pricing
- **Booking management** — View, confirm, cancel bookings
- **Availability control** — Block dates, mark vehicles as unavailable
- **Customer enquiries** — View contact form submissions
- **Settings** — Site name, contact info, business hours

### 🔒 Security

- **PDO prepared statements** — protection against SQL injection
- **HTML escaping** on all user output — protection against XSS
- **File upload validation** — MIME type and size checks
- **`.htaccess` hardening** — blocks sensitive files, directory listing disabled
- **Security headers** — X-Frame-Options, X-Content-Type-Options, Referrer-Policy
- **Custom error pages** — no stack traces leaked to users

---

## 🧰 Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | PHP 8.x (no framework) |
| **Database** | MySQL 8.x / MariaDB |
| **Frontend** | HTML5, CSS3, vanilla JavaScript |
| **Routing** | Apache mod_rewrite (SPA-style URLs) |
| **Icons** | Custom inline SVG |
| **Email** | PHP `mail()` / SMTP |
| **Hosting** | AwardSpace (tested), any LAMP stack |

---



## 📁 Project Structure
