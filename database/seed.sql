-- Demo data for RentCMS
-- Run: mysql -u root -p rentcms_db < database/seed.sql

INSERT INTO users (username, email, password, full_name, role, status, email_verified)
VALUES
('owner_demo', 'owner@demo.local', '$2y$10$L6wCv4ZP0zxf4eNcpgxgUuMVqVwPBH5j3rOtdS1wxYjE0m7xH8c0a', 'Demo Owner', 'owner', 'active', 1),
('tenant_demo', 'tenant@demo.local', '$2y$10$L6wCv4ZP0zxf4eNcpgxgUuMVqVwPBH5j3rOtdS1wxYjE0m7xH8c0a', 'Demo Tenant', 'tenant', 'active', 1)
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO properties (owner_id, title, description, address, city, state, country, postal_code, price_per_month, bedrooms, bathrooms, square_feet, property_type, featured_image, status, verification_status)
VALUES
((SELECT id FROM users WHERE username = 'owner_demo'), 'Beautiful villa for sale in Tampa', 'Spacious villa with modern amenities.', '4935 New Providence Ave', 'Tampa', 'FL', 'USA', '33602', 1600, 5, 3, 2800, 'Villa', 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=900&q=80', 'available', 'verified'),
((SELECT id FROM users WHERE username = 'owner_demo'), 'Stylish two-level penthouse in Palm Beach', 'Luxury penthouse with ocean view.', '101 Worth Ave', 'Palm Beach', 'FL', 'USA', '33480', 2000, 2, 2, 1900, 'Penthouse', 'https://images.unsplash.com/photo-1502005097973-6a7082348e28?auto=format&fit=crop&w=900&q=80', 'available', 'verified'),
((SELECT id FROM users WHERE username = 'owner_demo'), 'Bright and Cheerful alcove studio', 'Cozy studio close to the beach.', '1451 Ocean Dr', 'Miami Beach', 'FL', 'USA', '33139', 1200, 1, 1, 650, 'Apartment', 'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?auto=format&fit=crop&w=900&q=80', 'available', 'verified')
ON DUPLICATE KEY UPDATE id=id;
