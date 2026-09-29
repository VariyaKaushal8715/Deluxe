<?php
declare(strict_types=1);

// Application Configuration
return [
    'name' => 'Your Salon',
    'tagline' => 'Luxury Beauty & Holistic Wellness',
    'url' => getenv('APP_URL') ?: 'http://localhost:8000',
    'timezone' => 'Asia/Kolkata',
    'currency' => '₹',
    'currency_code' => 'INR',
    'phone' => '+91 98765 43210',
    'email' => 'contact@yoursalon.com',
    'address' => '104 Elegance Boulevard, Bandra West, Mumbai, MH 400050',
    'opening_hours' => [
        'weekday' => '09:00 AM - 08:00 PM',
        'weekend' => '09:00 AM - 09:00 PM'
    ],
    'cancellation_cutoff_hours' => 4,
    'advance_deposit_percent' => 20, // 20% or ₹200 minimum
    'advance_deposit_min' => 200,
];
