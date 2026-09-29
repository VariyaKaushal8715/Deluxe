# DELUXE BEAUTY SALON & WELLNESS PLATFORM
## ARCHITECTURE, DATABASE & 5-SESSION IMPLEMENTATION BLUEPRINT

---

### Executive Summary
This document defines the complete architectural blueprint, relational database design, business rules, UI design system, API specifications, and phased implementation roadmap for the **Deluxe Beauty Salon & Wellness Platform**.

The project is executed across **5 dedicated sessions**:
- **Session 1 (Current):** Environment Audit, Architecture, Relational Schema, Business Rules, UI System, and Roadmap.
- **Session 2:** Database Setup, Realistic Seed Data, Authentication & Roles, Public Portal & Service Catalog.
- **Session 3:** Multi-Service Booking Engine, Staff Scheduling, Double-Booking Prevention, and Admin Calendar.
- **Session 4:** Demo Payment Gateway, Billing & Invoicing, Customer Portal, Gallery (Before/After), and Verified Reviews.
- **Session 5:** Admin Analytics Dashboard, Inventory & Consumables Management, Staff & Service Admin, Final Polish & QA.

---

### 1. Technology Stack & Runtime Audit
- **Backend Language:** PHP 8.4 (CLI / Built-in Server / Apache)
- **Database:** MariaDB 10.4.32 / MySQL (InnoDB engine, UTF8MB4 charset) via PHP PDO
- **Architecture Pattern:** Modular MVC (Model-View-Controller) with Front-Controller routing and Service Layer for business logic
- **Frontend Foundation:** Bootstrap 5.3 + Custom CSS Design System (Deluxe Salon Luxury Palette)
- **Client Interactions:** Vanilla JavaScript (ES6+), Fetch API for AJAX, FullCalendar 6 for scheduler, Chart.js 4 for analytics, Flatpickr for booking dates
- **Security:** Prepared statements (zero raw SQL concatenation), CSRF protection, Bcrypt password hashing, session fixation defense, strict role authorization

---

### 2. Normalized Database Schema (DDL)

```sql
-- Deluxe Salon & Spa Database Schema
CREATE DATABASE IF NOT EXISTS deluxe_salon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE deluxe_salon;

-- 1. Users table (Customer, Staff, Admin)
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff', 'customer') NOT NULL DEFAULT 'customer',
    profile_image VARCHAR(255) NULL,
    gender ENUM('female', 'male', 'other') NULL,
    address TEXT NULL,
    status ENUM('active', 'inactive', 'banned') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_role (role),
    INDEX idx_user_email (email)
) ENGINE=InnoDB;

-- 2. Service Categories
CREATE TABLE IF NOT EXISTS service_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    icon VARCHAR(50) NULL,
    display_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Services
CREATE TABLE IF NOT EXISTS services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration_minutes INT UNSIGNED NOT NULL DEFAULT 30,
    gender_target ENUM('all', 'female', 'male') NOT NULL DEFAULT 'all',
    is_home_service TINYINT(1) NOT NULL DEFAULT 0,
    is_popular TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    image_url VARCHAR(255) NULL,
    treatment_details TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE CASCADE,
    INDEX idx_service_category (category_id),
    INDEX idx_service_active_popular (is_active, is_popular)
) ENGINE=InnoDB;

-- 4. Staff Profiles
CREATE TABLE IF NOT EXISTS staff (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    designation VARCHAR(100) NOT NULL,
    bio TEXT NULL,
    profile_image VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 5. Staff Specializations (Many-to-Many: Staff <-> Categories)
CREATE TABLE IF NOT EXISTS staff_specializations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    staff_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_staff_cat (staff_id, category_id),
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Staff Weekly Working Hours
CREATE TABLE IF NOT EXISTS staff_working_hours (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    staff_id INT UNSIGNED NOT NULL,
    day_of_week TINYINT UNSIGNED NOT NULL COMMENT '0=Sunday, 1=Monday, ..., 6=Saturday',
    start_time TIME NOT NULL DEFAULT '09:00:00',
    end_time TIME NOT NULL DEFAULT '19:00:00',
    is_day_off TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE KEY uq_staff_day (staff_id, day_of_week),
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. Staff Daily Breaks
CREATE TABLE IF NOT EXISTS staff_breaks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    staff_id INT UNSIGNED NOT NULL,
    day_of_week TINYINT UNSIGNED NOT NULL,
    break_name VARCHAR(50) NOT NULL DEFAULT 'Lunch Break',
    break_start TIME NOT NULL DEFAULT '13:00:00',
    break_end TIME NOT NULL DEFAULT '14:00:00',
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. Staff Time Blocks / Leaves
CREATE TABLE IF NOT EXISTS staff_time_blocks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    staff_id INT UNSIGNED NOT NULL,
    block_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    reason VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE,
    INDEX idx_staff_block_date (staff_id, block_date)
) ENGINE=InnoDB;

-- 9. Salon Holidays
CREATE TABLE IF NOT EXISTS salon_holidays (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    holiday_date DATE NOT NULL UNIQUE,
    title VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    is_closed TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 10. Appointments
CREATE TABLE IF NOT EXISTS appointments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_reference VARCHAR(30) NOT NULL UNIQUE,
    customer_id INT UNSIGNED NOT NULL,
    staff_id INT UNSIGNED NOT NULL,
    appointment_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    total_duration_minutes INT UNSIGNED NOT NULL,
    service_mode ENUM('in_parlor', 'home_service') NOT NULL DEFAULT 'in_parlor',
    home_address TEXT NULL,
    home_contact_phone VARCHAR(20) NULL,
    status ENUM('pending', 'confirmed', 'checked_in', 'in_progress', 'completed', 'cancelled', 'no_show', 'rescheduled') NOT NULL DEFAULT 'pending',
    cancellation_reason VARCHAR(255) NULL,
    cancelled_at DATETIME NULL,
    rescheduled_from_id INT UNSIGNED NULL,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    tax_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    advance_paid DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    balance_due DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    customer_notes TEXT NULL,
    admin_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE RESTRICT,
    FOREIGN KEY (rescheduled_from_id) REFERENCES appointments(id) ON DELETE SET NULL,
    INDEX idx_appt_date_staff (appointment_date, staff_id),
    INDEX idx_appt_status (status),
    INDEX idx_appt_customer (customer_id)
) ENGINE=InnoDB;

-- 11. Appointment Services (Pivot with historic pricing snapshot)
CREATE TABLE IF NOT EXISTS appointment_services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT UNSIGNED NOT NULL,
    service_id INT UNSIGNED NOT NULL,
    service_name VARCHAR(150) NOT NULL,
    duration_minutes INT UNSIGNED NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT,
    INDEX idx_appt_srv (appointment_id, service_id)
) ENGINE=InnoDB;

-- 12. Payments (Supports advance token & final settlement)
CREATE TABLE IF NOT EXISTS payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_reference VARCHAR(50) NOT NULL UNIQUE,
    appointment_id INT UNSIGNED NOT NULL,
    customer_id INT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_type ENUM('advance', 'full', 'balance') NOT NULL DEFAULT 'advance',
    payment_method ENUM('upi', 'card', 'net_banking', 'pay_at_salon') NOT NULL DEFAULT 'upi',
    payment_status ENUM('pending', 'processing', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    transaction_id VARCHAR(100) NULL,
    gateway_note VARCHAR(255) NULL,
    paid_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_pay_status (payment_status)
) ENGINE=InnoDB;

-- 13. Invoices
CREATE TABLE IF NOT EXISTS invoices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) NOT NULL UNIQUE,
    appointment_id INT UNSIGNED NOT NULL UNIQUE,
    customer_id INT UNSIGNED NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    tax_rate DECIMAL(5,2) NOT NULL DEFAULT 5.00,
    tax_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    advance_paid DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    balance_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('unpaid', 'partially_paid', 'paid', 'refunded') NOT NULL DEFAULT 'unpaid',
    issued_date DATE NOT NULL,
    pdf_path VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 14. Invoice Line Items
CREATE TABLE IF NOT EXISTS invoice_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT UNSIGNED NOT NULL,
    item_description VARCHAR(255) NOT NULL,
    item_type ENUM('service', 'product', 'fee', 'discount') NOT NULL DEFAULT 'service',
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 15. Reviews & Ratings (Strict appointment-backed verification)
CREATE TABLE IF NOT EXISTS reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT UNSIGNED NOT NULL,
    service_id INT UNSIGNED NOT NULL,
    staff_id INT UNSIGNED NOT NULL,
    customer_id INT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT NOT NULL,
    moderation_status ENUM('pending', 'published', 'hidden') NOT NULL DEFAULT 'published',
    is_verified_customer TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_appt_srv_review (appointment_id, service_id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_review_srv_status (service_id, moderation_status)
) ENGINE=InnoDB;

-- 16. Gallery / Lookbook (Supports Before/After)
CREATE TABLE IF NOT EXISTS gallery_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    category ENUM('bridal', 'hair', 'makeup', 'nails', 'facial', 'other') NOT NULL DEFAULT 'hair',
    description TEXT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_before_after TINYINT(1) NOT NULL DEFAULT 0,
    before_image_url VARCHAR(255) NULL,
    display_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 17. Inventory & Consumables
CREATE TABLE IF NOT EXISTS inventory_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(150) NOT NULL,
    sku VARCHAR(50) NOT NULL UNIQUE,
    category VARCHAR(100) NOT NULL,
    unit ENUM('ml', 'gm', 'pcs', 'bottles', 'packs', 'tubes') NOT NULL DEFAULT 'pcs',
    current_stock DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    min_stock_threshold DECIMAL(10,2) NOT NULL DEFAULT 5.00,
    cost_per_unit DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    supplier_name VARCHAR(150) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_inv_stock (current_stock, min_stock_threshold)
) ENGINE=InnoDB;

-- 18. Inventory Transactions (Stock audit trail)
CREATE TABLE IF NOT EXISTS inventory_transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_id INT UNSIGNED NOT NULL,
    transaction_type ENUM('stock_in', 'usage', 'adjustment', 'wastage') NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    balance_after DECIMAL(10,2) NOT NULL,
    notes VARCHAR(255) NULL,
    logged_by INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES inventory_items(id) ON DELETE CASCADE,
    FOREIGN KEY (logged_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 19. Global Salon Settings
CREATE TABLE IF NOT EXISTS settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    setting_group VARCHAR(50) NOT NULL DEFAULT 'general',
    description VARCHAR(255) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```
