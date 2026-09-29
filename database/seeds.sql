-- Deluxe Beauty Salon & Spa Realistic Demo Seed Data
USE deluxe_salon;

-- Clear previous data in correct foreign key order
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE reviews;
TRUNCATE TABLE invoice_items;
TRUNCATE TABLE invoices;
TRUNCATE TABLE payments;
TRUNCATE TABLE appointment_services;
TRUNCATE TABLE appointments;
TRUNCATE TABLE staff_time_blocks;
TRUNCATE TABLE staff_breaks;
TRUNCATE TABLE staff_working_hours;
TRUNCATE TABLE staff_specializations;
TRUNCATE TABLE staff;
TRUNCATE TABLE services;
TRUNCATE TABLE service_categories;
TRUNCATE TABLE gallery_items;
TRUNCATE TABLE inventory_transactions;
TRUNCATE TABLE inventory_items;
TRUNCATE TABLE settings;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Users
-- Passwords:
-- Admin: Admin@123
-- Staff / Customers: Customer@123
INSERT INTO users (id, name, email, phone, password_hash, role, profile_image, gender, address_line, area_locality, city, state, pincode, landmark, status) VALUES
(1, 'Kavita Singhania', 'admin@deluxesalon.com', '+91 98200 11223', '$2y$12$DJlgvZkWX67fH/FYKeJmq.6NivtIoK3OsTW7h2j1v1r974Mxwd9D2', 'admin', 'admin_avatar.jpg', 'female', 'Deluxe Salon HQ, 104 Elegance Blvd', 'Bandra West', 'Mumbai', 'Maharashtra', '400050', 'Near Pali Hill Water Tank', 'active'),
(2, 'Ananya Sharma', 'ananya@deluxesalon.com', '+91 98201 22334', '$2y$12$8qy.Fe0xkkuU5xmzm4TjGuDPnsjvd3yVtH2iNS2eQ7mLzzkQg4ueG', 'staff', 'ananya_portrait.jpg', 'female', 'B-302 Sea View Apts', 'Khar West', 'Mumbai', 'Maharashtra', '400052', 'Opp Khar Gymkhana', 'active'),
(3, 'Priya Patel', 'priya@deluxesalon.com', '+91 98202 33445', '$2y$12$8qy.Fe0xkkuU5xmzm4TjGuDPnsjvd3yVtH2iNS2eQ7mLzzkQg4ueG', 'staff', 'priya_portrait.jpg', 'female', '12 Sunshine Enclave', 'Santacruz West', 'Mumbai', 'Maharashtra', '400054', 'Near Podar School', 'active'),
(4, 'Rhea Mukherjee', 'rhea@deluxesalon.com', '+91 98203 44556', '$2y$12$8qy.Fe0xkkuU5xmzm4TjGuDPnsjvd3yVtH2iNS2eQ7mLzzkQg4ueG', 'staff', 'rhea_portrait.jpg', 'female', '701 Green Acres', 'Juhu', 'Mumbai', 'Maharashtra', '400049', 'Near Juhu Circle', 'active'),
(5, 'Vikram Rao', 'vikram@deluxesalon.com', '+91 98204 55667', '$2y$12$8qy.Fe0xkkuU5xmzm4TjGuDPnsjvd3yVtH2iNS2eQ7mLzzkQg4ueG', 'staff', 'vikram_portrait.jpg', 'male', '404 Ocean Heights', 'Versova', 'Mumbai', 'Maharashtra', '400061', 'Near Metro Station', 'active'),
(6, 'Sneha Kapoor', 'sneha.kapoor@gmail.com', '+91 98700 88990', '$2y$12$8qy.Fe0xkkuU5xmzm4TjGuDPnsjvd3yVtH2iNS2eQ7mLzzkQg4ueG', 'customer', 'customer_sneha.jpg', 'female', 'Flat 402, Lotus Residency', 'Carter Road, Bandra West', 'Mumbai', 'Maharashtra', '400050', 'Next to Cafe Coffee Day', 'active'),
(7, 'Rohit Mehta', 'rohit.mehta@gmail.com', '+91 98701 99001', '$2y$12$8qy.Fe0xkkuU5xmzm4TjGuDPnsjvd3yVtH2iNS2eQ7mLzzkQg4ueG', 'customer', 'customer_rohit.jpg', 'male', '15 Silver Oaks Villa', 'Juhu Tara Road', 'Mumbai', 'Maharashtra', '400049', 'Behind Marriott', 'active'),
(8, 'Aisha Khan', 'aisha.khan@gmail.com', '+91 98702 10112', '$2y$12$8qy.Fe0xkkuU5xmzm4TjGuDPnsjvd3yVtH2iNS2eQ7mLzzkQg4ueG', 'customer', 'customer_aisha.jpg', 'female', 'Tower 3, Apt 1104, Heights', 'Worli Seaface', 'Mumbai', 'Maharashtra', '400018', 'Near Nehru Planetarium', 'active');

-- 2. Service Categories
INSERT INTO service_categories (id, name, slug, description, icon, display_order, is_active) VALUES
(1, 'Hair Care & Styling', 'hair-care', 'Precision haircuts, organic keratin restoration, balayage, and luxurious hair spa treatments.', 'scissors', 1, 1),
(2, 'Facials & Advanced Skin', 'facials-skin', 'Clinical-grade hydra cleanups, radiance peel therapy, anti-aging micro-firming, and organic glows.', 'stars', 2, 1),
(3, 'Nails & Pedicure', 'nails-pedicure', 'Signature spa pedicures, gel extensions, Russian manicures, and bespoke metallic nail art.', 'hand-index-thumb', 3, 1),
(4, 'Bridal & Party Makeup', 'bridal-makeup', 'HD airbrush bridal makeovers, cocktail party glam, saree draping, and customized trousseau looks.', 'heart', 4, 1),
(5, 'Spa & Waxing Wellness', 'spa-wellness', 'Holistic deep tissue aromatherapy, Swedish recovery, organic Rica waxing, and rejuvenating body polishing.', 'droplet', 5, 1);

-- 3. Services
INSERT INTO services (id, category_id, name, slug, description, price, duration_minutes, gender_target, is_home_service, is_popular, is_active, image_url, treatment_details) VALUES
-- Hair Care
(1, 1, 'Precision Cut, Wash & Blowdry', 'precision-cut-wash-blowdry', 'Expert consultation, customized clarifying wash, artistic scalp massage, tailored haircut, and salon-grade bouncy blowdry.', 899.00, 45, 'all', 0, 1, 1, 'service_haircut.jpg', 'Consultation • Double Cleansing Wash • Conditioning • Precision Cut • Heat Protectant • Velvet Blowdry Finish'),
(2, 1, 'Organic Keratin Smooth Treatment', 'organic-keratin-smooth', 'Formaldehyde-free intensive botanical keratin infusion for frizz elimination, brilliant diamond shine, and silky manageable tresses lasting up to 4 months.', 3999.00, 90, 'all', 0, 1, 1, 'service_keratin.jpg', 'Deep Clarifying Detox • Keratin Micro-Infusion • Infrared Heat Activation • Precision Flat Iron Sealing • Nourishing Mask'),
(3, 1, 'Balayage & Dimensional Highlights', 'balayage-highlights', 'Hand-painted sun-kissed French balayage using Olaplex bond multiplier to protect hair health while creating seamless, luminous depth.', 4499.00, 120, 'female', 0, 0, 1, 'service_balayage.jpg', 'Color Consultation • Bond Multiplier Prep • Custom Hand-Painting • Toning Glaze • Color Lock Conditioning'),
(4, 1, 'Moroccan Argan Nourishing Hair Spa', 'moroccan-argan-hair-spa', 'Revitalizing deep moisture mask with pure cold-pressed argan oil, targeted acupressure scalp massage, and warm ozone micro-mist steam.', 1599.00, 50, 'all', 1, 1, 1, 'service_hairspa.jpg', 'Scalp Analysis • Exfoliating Scrub • Argan Mask Massage • Ozone Steamer • Cool Water Rinse & Serum Seal'),

-- Facials & Skin
(5, 2, 'Hydra Cleanse Medical Glow Facial', 'hydra-cleanse-glow-facial', 'Signature 7-step vortex vacuum technology delivering deep pore extraction, lactic exfoliation, hyaluronic hydration, and oxygen serum spray.', 1899.00, 45, 'all', 1, 1, 1, 'service_hydra.jpg', 'Vortex Deep Cleansing • Gentle Acid Peel • Painless Blackhead Extraction • Hyaluronic Peptide Infusion • Oxygen Jet Spray • Cold Hammer Seal'),
(6, 2, '24K Gold Luxury Illuminating Facial', '24k-gold-illuminating-facial', 'Opulent bridal treatment infused with colloidal gold flakes, collagen boosting peptides, and jade roller lymph drainage for an immediate red-carpet glow.', 2799.00, 60, 'female', 1, 1, 1, 'service_gold_facial.jpg', 'Double Cleansing • Enzyme Peeling • 24K Pure Gold Serum Infusion • Jade Roller Drainage • Gold Peel-Off Alginate Mask • SPF 50 Defense'),
(7, 2, 'D-Tan & Brightening Charcoal Cleanup', 'dtan-brightening-cleanup', 'Quick clarifying cleanup targeted at urban pollution, sun pigmentation, and excess sebum with activated charcoal and fruit AHA extracts.', 999.00, 35, 'all', 1, 0, 1, 'service_dtan.jpg', 'Charcoal Foam Cleanser • Steam & Blackhead Care • Botanical D-Tan Mask • Brightening Vitamin C Serum'),
(8, 2, 'Cryo-Lift Anti-Aging Collagen Therapy', 'cryo-lift-anti-aging-facial', 'Sub-zero cryo-wand tightening paired with marine collagen sheets to firm skin tone, stimulate microcirculation, and sculpt jawline contours.', 3299.00, 60, 'female', 0, 0, 1, 'service_cryo.jpg', 'Collagen Cleanse • Ultrasonic Exfoliation • Cryo-Sculpting Wand • Bio-Cellulose Lifting Mask • Peptide Eye Firming'),

-- Nails & Pedicure
(9, 3, 'Signature Rose Petal Spa Pedicure', 'signature-rose-petal-pedicure', 'Warm rose-water foot soak, dead-sea salt exfoliating scrub, callused heel softening, cuticles care, and deeply relaxing botanical massage.', 1299.00, 50, 'all', 1, 1, 1, 'service_pedicure.jpg', 'Aromatherapeutic Foot Bath • Dead Sea Salt Scrub • Callus Smoothing • Cuticle Tidy • Warm Towel Wrap • Long-wear Polish'),
(10, 3, 'Gel Extension & Chrome Nail Art', 'gel-extension-chrome-art', 'Full-set sculpting gel extensions with seamless cuticle prep, high-gloss UV cured chrome mirror finish or minimalist bespoke hand-painted nail accents.', 2199.00, 75, 'female', 0, 1, 1, 'service_nails.jpg', 'Dry Russian Manicure Prep • Gel Extension Sculpting • Nail Shaping • Chrome/Art Application • High-Gloss UV Top Coat'),
(11, 3, 'Deluxe Russian Manicure with Gel Polish', 'deluxe-russian-manicure', 'Meticulous e-file precision cuticle cleaning providing clean edges, followed by strengthening rubber base and chip-resistant gel polish lasting 3+ weeks.', 1499.00, 45, 'all', 1, 0, 1, 'service_manicure.jpg', 'E-File Cuticle Treatment • Nail Strengthening Base • Dual-layer Gel Polish • Nourishing Cuticle Oil Massage'),

-- Bridal & Party Makeup
(12, 4, 'Royal HD Airbrush Bridal Makeover', 'royal-hd-bridal-makeover', 'Comprehensive high-definition airbrush bridal makeup that lasts 18+ hours tear-proof and sweat-resistant, including 3D mink eyelashes and luxury hair styling.', 8999.00, 120, 'female', 1, 1, 1, 'service_bridal.jpg', 'Bridal Skin Prep • HD Airbrush Application • Waterproof Eye Art & 3D Lashes • Trousseau Contouring • Premium Hair Artistry • Saree Draping'),
(13, 4, 'Celebrity Evening Glam & Hair Styling', 'celebrity-evening-glam', 'Smokey or soft-glam evening party makeup with luminous strobing highlights, sculpted brows, and red-carpet Hollywood waves or trendy textured bun.', 3499.00, 60, 'female', 1, 1, 1, 'service_party_makeup.jpg', 'Pre-Makeup Hydration • Flawless Base & Concealing • Velvet Eyeshadow & Lashes • Lip Sculpting • Thermal Hair Styling'),

-- Spa & Wellness
(14, 5, 'Aromatherapy Balinese Deep Tissue Massage', 'aromatherapy-balinese-massage', 'Traditional firm pressure strokes and palm acupressure with warm therapeutic essential oils to relieve chronic muscle tension and promote holistic calm.', 2499.00, 60, 'all', 0, 1, 1, 'service_massage.jpg', 'Foot Welcome Ritual • Custom Essential Oil Selection • Full Body Acupressure & Palm Kneading • Hot Towel Cleansing • Herbal Green Tea'),
(15, 5, 'Full Body Rica White Chocolate Waxing', 'full-body-rica-waxing', 'Colophony-free Italian Rica chocolate liposoluble wax formulated for sensitive skin, dramatically minimizing discomfort and leaving silky glowing skin.', 2299.00, 60, 'female', 1, 0, 1, 'service_waxing.jpg', 'Pre-Wax Antiseptic Lotion • Gentle Rica Wax Application • Post-Wax Soothing Oil • Anti-Ingrown Hair Serum Application');

-- 4. Staff Profiles
INSERT INTO staff (id, user_id, full_name, email, phone, designation, bio, profile_image, is_active) VALUES
(1, 2, 'Ananya Sharma', 'ananya@deluxesalon.com', '+91 98201 22334', 'Senior Creative Hair Stylist & Colorist', 'With 8+ years of experience trained at Vidal Sassoon London, Ananya specializes in dimensional balayage, corrective hair coloring, and precision geometric cuts.', 'staff_ananya.jpg', 1),
(2, 3, 'Priya Patel', 'priya@deluxesalon.com', '+91 98202 33445', 'Master Clinical Esthetician', 'Certified in advanced dermal treatments and HydraFacial technology, Priya has transformed hundreds of client complexions using tailored peptide regimens and cryo-therapy.', 'staff_priya.jpg', 1),
(3, 4, 'Rhea Mukherjee', 'rhea@deluxesalon.com', '+91 98203 44556', 'Celebrity Bridal & Glamour Artist', 'Renowned for luminous, glass-skin bridal looks and bespoke saree draping. Featured in leading lifestyle magazines with over 300 successful bridal makeovers.', 'staff_rhea.jpg', 1),
(4, 5, 'Vikram Rao', 'vikram@deluxesalon.com', '+91 98204 55667', 'Nail Artist & Holistic Wellness Therapist', 'Master of Russian e-file manicures and Balinese deep-tissue massage techniques. Known for his meticulous attention to detail and calming therapeutic presence.', 'staff_vikram.jpg', 1);

-- 5. Staff Specializations (Categories)
INSERT INTO staff_specializations (staff_id, category_id) VALUES
(1, 1), -- Ananya: Hair Care
(1, 5), -- Ananya: Spa & Waxing
(2, 2), -- Priya: Facials & Skin
(2, 5), -- Priya: Spa & Waxing
(3, 4), -- Rhea: Bridal & Makeup
(3, 2), -- Rhea: Facials & Skin
(4, 3), -- Vikram: Nails & Pedicure
(4, 5); -- Vikram: Spa & Wellness

-- 6. Staff Weekly Working Hours (0=Sun, 1=Mon, ..., 6=Sat)
-- Mon-Sat: 09:00 to 19:00, Sun off for Ananya & Vikram; Sun 10:00-18:00, Mon off for Priya & Rhea
INSERT INTO staff_working_hours (staff_id, day_of_week, start_time, end_time, is_day_off) VALUES
-- Ananya (Tue-Sun active, Mon off)
(1, 0, '10:00:00', '18:00:00', 0),
(1, 1, '09:00:00', '19:00:00', 1), -- Monday OFF
(1, 2, '09:00:00', '19:00:00', 0),
(1, 3, '09:00:00', '19:00:00', 0),
(1, 4, '09:00:00', '19:00:00', 0),
(1, 5, '09:00:00', '19:00:00', 0),
(1, 6, '09:00:00', '19:00:00', 0),
-- Priya (Mon-Sat active, Sun off)
(2, 0, '09:00:00', '19:00:00', 1), -- Sunday OFF
(2, 1, '09:00:00', '19:00:00', 0),
(2, 2, '09:00:00', '19:00:00', 0),
(2, 3, '09:00:00', '19:00:00', 0),
(2, 4, '09:00:00', '19:00:00', 0),
(2, 5, '09:00:00', '19:00:00', 0),
(2, 6, '09:00:00', '19:00:00', 0),
-- Rhea (Tue-Sun active, Mon off)
(3, 0, '10:00:00', '18:00:00', 0),
(3, 1, '09:00:00', '19:00:00', 1), -- Monday OFF
(3, 2, '09:00:00', '19:00:00', 0),
(3, 3, '09:00:00', '19:00:00', 0),
(3, 4, '09:00:00', '19:00:00', 0),
(3, 5, '09:00:00', '19:00:00', 0),
(3, 6, '09:00:00', '19:00:00', 0),
-- Vikram (Mon-Sat active, Sun off)
(4, 0, '09:00:00', '19:00:00', 1), -- Sunday OFF
(4, 1, '09:00:00', '19:00:00', 0),
(4, 2, '09:00:00', '19:00:00', 0),
(4, 3, '09:00:00', '19:00:00', 0),
(4, 4, '09:00:00', '19:00:00', 0),
(4, 5, '09:00:00', '19:00:00', 0),
(4, 6, '09:00:00', '19:00:00', 0);

-- 7. Staff Daily Breaks (Lunch 13:00 - 14:00)
INSERT INTO staff_breaks (staff_id, day_of_week, break_name, break_start, break_end) VALUES
(1, 0, 'Lunch Break', '13:00:00', '14:00:00'),
(1, 2, 'Lunch Break', '13:00:00', '14:00:00'),
(1, 3, 'Lunch Break', '13:00:00', '14:00:00'),
(1, 4, 'Lunch Break', '13:00:00', '14:00:00'),
(1, 5, 'Lunch Break', '13:00:00', '14:00:00'),
(1, 6, 'Lunch Break', '13:00:00', '14:00:00'),
(2, 1, 'Lunch Break', '13:00:00', '14:00:00'),
(2, 2, 'Lunch Break', '13:00:00', '14:00:00'),
(2, 3, 'Lunch Break', '13:00:00', '14:00:00'),
(2, 4, 'Lunch Break', '13:00:00', '14:00:00'),
(2, 5, 'Lunch Break', '13:00:00', '14:00:00'),
(2, 6, 'Lunch Break', '13:00:00', '14:00:00'),
(3, 2, 'Lunch Break', '13:30:00', '14:30:00'),
(3, 3, 'Lunch Break', '13:30:00', '14:30:00'),
(3, 4, 'Lunch Break', '13:30:00', '14:30:00'),
(3, 5, 'Lunch Break', '13:30:00', '14:30:00'),
(3, 6, 'Lunch Break', '13:30:00', '14:30:00'),
(4, 1, 'Lunch Break', '14:00:00', '15:00:00'),
(4, 2, 'Lunch Break', '14:00:00', '15:00:00'),
(4, 3, 'Lunch Break', '14:00:00', '15:00:00'),
(4, 4, 'Lunch Break', '14:00:00', '15:00:00'),
(4, 5, 'Lunch Break', '14:00:00', '15:00:00'),
(4, 6, 'Lunch Break', '14:00:00', '15:00:00');

-- 8. Realistic Settings
INSERT INTO settings (setting_key, setting_value, setting_group, description) VALUES
('salon_name', 'Deluxe Salon & Spa', 'general', 'Public commercial salon brand name'),
('salon_email', 'contact@deluxesalon.com', 'general', 'Official customer support email'),
('salon_phone', '+91 98765 43210', 'general', 'Front-desk WhatsApp and appointment hotline'),
('salon_address', '104 Elegance Boulevard, Bandra West, Mumbai, MH 400050', 'general', 'Physical parlor address'),
('cancellation_cutoff_hours', '4', 'booking', 'Minimum advance notice required to self-cancel or reschedule online'),
('advance_deposit_percentage', '20', 'payment', 'Mandatory advance token percentage for online reservations'),
('advance_deposit_minimum', '200', 'payment', 'Minimum rupee advance deposit threshold'),
('tax_rate_percentage', '5.00', 'billing', 'Applicable GST percentage for beauty parlor services');

-- 9. Gallery Items
INSERT INTO gallery_items (id, title, category, description, image_url, is_before_after, before_image_url, display_order, is_active) VALUES
(1, 'Warm Caramel Balayage Transformation', 'hair', 'Subtle dimensional color blending with face-framing babylights and gloss glaze.', 'gallery_hair_1.jpg', 1, 'gallery_hair_before_1.jpg', 1, 1),
(2, 'Classic Royal Indian Bridal Look', 'bridal', 'Matte velvet base, royal gold eye shimmer, traditional kohl eyes, and intricate maang tikka setting.', 'gallery_bridal_1.jpg', 0, NULL, 2, 1),
(3, 'Hydra Detox Skin Renewal', 'facial', 'Significant reduction in congested pores and renewed luminous moisture balance.', 'gallery_skin_1.jpg', 1, 'gallery_skin_before_1.jpg', 3, 1),
(4, 'Ombre French Chrome Nails', 'nails', 'Almond sculpted extensions with iridescent pearl chrome dust finish.', 'gallery_nails_1.jpg', 0, NULL, 4, 1),
(5, 'Contemporary Reception Sangeet Glam', 'makeup', 'Soft sculpted contour with glossy nude lips and effortless Hollywood glamour waves.', 'gallery_makeup_1.jpg', 0, NULL, 5, 1);

-- 10. Sample Completed Appointments (Foundation for verified reviews & history)
INSERT INTO appointments (id, booking_reference, customer_id, staff_id, appointment_date, start_time, end_time, total_duration_minutes, service_mode, status, subtotal, tax_amount, total_amount, advance_paid, balance_due) VALUES
(1, 'DLX-2026-0901', 6, 2, '2026-09-15', '14:30:00', '15:15:00', 45, 'in_parlor', 'completed', 1899.00, 94.95, 1993.95, 200.00, 0.00),
(2, 'DLX-2026-0902', 7, 1, '2026-09-18', '17:00:00', '18:30:00', 90, 'in_parlor', 'completed', 3999.00, 199.95, 4198.95, 500.00, 0.00),
(3, 'DLX-2026-0903', 8, 3, '2026-09-22', '17:00:00', '19:00:00', 120, 'in_parlor', 'completed', 8999.00, 449.95, 9448.95, 2000.00, 0.00),
(4, 'DLX-2026-0904', 6, 4, '2026-09-25', '16:00:00', '16:50:00', 50, 'in_parlor', 'completed', 1299.00, 64.95, 1363.95, 200.00, 0.00);

INSERT INTO appointment_services (appointment_id, service_id, service_name, duration_minutes, price) VALUES
(1, 5, 'Hydra Cleanse Medical Glow Facial', 45, 1899.00),
(2, 2, 'Organic Keratin Smooth Treatment', 90, 3999.00),
(3, 12, 'Royal HD Airbrush Bridal Makeover', 120, 8999.00),
(4, 9, 'Signature Rose Petal Spa Pedicure', 50, 1299.00);

-- 11. Reviews (Pre-populated authentic testimonials for homepage and service details)
INSERT INTO reviews (id, appointment_id, service_id, staff_id, customer_id, rating, review_text, moderation_status, is_verified_customer, created_at) VALUES
(1, 1, 5, 2, 6, 5, 'The Hydra Cleanse Facial by Priya was truly transformative! My skin has never felt this clean, plump and radiant. The Bandra studio ambiance is exceptionally luxurious.', 'published', 1, '2026-09-15 14:30:00'),
(2, 2, 2, 1, 7, 5, 'Got the Keratin treatment done with Ananya. She explained every step patiently and the results are unbelievable. Zero frizz even in Mumbai humidity. Worth every rupee.', 'published', 1, '2026-09-18 17:00:00'),
(3, 3, 12, 3, 8, 5, 'Rhea is an absolute magician with bridal makeovers! She gave me the natural glow look I always envisioned for my engagement. The makeup did not budge all evening.', 'published', 1, '2026-09-22 19:15:00'),
(4, 4, 9, 4, 6, 5, 'Vikram gave the most relaxing pedicure I have ever had. The warm rose petal soak and reflexology massage made all my week stress disappear.', 'published', 1, '2026-09-25 16:45:00');

-- 11. Initial Consumables Inventory
INSERT INTO inventory_items (id, item_name, sku, category, unit, current_stock, min_stock_threshold, cost_per_unit, supplier_name, is_active) VALUES
(1, 'L\'Oreal Professionnel Inoa Ammonia-Free Color 60g', 'LRL-INOA-001', 'Hair Color', 'tubes', 18.00, 6.00, 480.00, 'L\'Oreal India Salon Direct', 1),
(2, 'HydraFacial Vortex Hyaluronic Peptide Serum 50ml', 'HYD-SRM-50', 'Skin Treatment', 'bottles', 3.00, 5.00, 1850.00, 'DermaQuip Aesthetics', 1),
(3, 'OPI ProSpa Protective Hand Serum & Cuticle Oil', 'OPI-OIL-15', 'Nail Care', 'bottles', 7.00, 3.00, 620.00, 'OPI Beauty Distribution', 1),
(4, 'Rica Brazilian Avocado Wax 800ml', 'RICA-WAX-800', 'Waxing Consumable', 'packs', 9.00, 4.00, 1150.00, 'Rica Professional Cosmetics', 1),
(5, 'Disposable Biodegradable Facial Bed Sheets (Pack of 50)', 'DSP-SHT-50', 'Salon Hygiene', 'packs', 28.00, 10.00, 350.00, 'EcoClean Healthcare', 1);
