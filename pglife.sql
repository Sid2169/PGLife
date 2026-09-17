-- =====================================================================
-- PG Life - Database Schema & Seed Data
-- Deliverable: complete .sql file for the PG Life application.
--
-- Import:
--   mysql -u root -p < pglife.sql
-- (or via phpMyAdmin > Import)
--
-- Application DB credentials come from includes/config.php, which reads the
-- environment variables DB_HOST, DB_USER, DB_PASS and DB_NAME. If they are
-- not set, the local XAMPP defaults are used:
--   host 127.0.0.1, user root, password password, database pglife
--
-- Demo user created below:
--   email    demo@pglife.in
--   password password
-- =====================================================================

CREATE DATABASE IF NOT EXISTS pglife CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pglife;

-- ---------------------------------------------------------------------
-- Table: cities
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS cities;
CREATE TABLE cities (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ---------------------------------------------------------------------
-- Table: properties
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS properties;
CREATE TABLE properties (
    id INT(11) NOT NULL AUTO_INCREMENT,
    city_id INT(11) NOT NULL,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(1000) NOT NULL,
    description VARCHAR(2000) NOT NULL,
    gender ENUM('male', 'female', 'unisex') NOT NULL,
    rent INT(11) NOT NULL,
    rating_clean FLOAT NOT NULL,
    rating_food FLOAT NOT NULL,
    rating_safety FLOAT NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_Properties_City FOREIGN KEY (city_id) REFERENCES cities(id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ---------------------------------------------------------------------
-- Table: amenities
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS amenities;
CREATE TABLE amenities (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(100) DEFAULT NULL,
    icon VARCHAR(200) DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ---------------------------------------------------------------------
-- Table: properties_amenities  (junction)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS properties_amenities;
CREATE TABLE properties_amenities (
    property_id INT(11) NOT NULL,
    amenity_id INT(11) NOT NULL,
    PRIMARY KEY (property_id, amenity_id),
    CONSTRAINT fk_PropAmenity_Property FOREIGN KEY (property_id) REFERENCES properties(id),
    CONSTRAINT fk_PropAmenity_Amenity FOREIGN KEY (amenity_id) REFERENCES amenities(id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ---------------------------------------------------------------------
-- Table: testimonials
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS testimonials;
CREATE TABLE testimonials (
    id INT(11) NOT NULL AUTO_INCREMENT,
    property_id INT(11) NOT NULL,
    user_name VARCHAR(100) NOT NULL,
    content VARCHAR(1000) NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_Testimonials_Property FOREIGN KEY (property_id) REFERENCES properties(id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ---------------------------------------------------------------------
-- Table: users
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    college_name VARCHAR(100) NOT NULL,
    PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ---------------------------------------------------------------------
-- Table: interested_users_properties  (junction)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS interested_users_properties;
CREATE TABLE interested_users_properties (
    user_id INT(11) NOT NULL,
    property_id INT(11) NOT NULL,
    PRIMARY KEY (user_id, property_id),
    CONSTRAINT fk_Interested_User FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_Interested_Property FOREIGN KEY (property_id) REFERENCES properties(id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- =====================================================================
-- SEED DATA
-- =====================================================================

INSERT INTO cities (id, name) VALUES
    (1, 'Delhi'),
    (2, 'Mumbai'),
    (3, 'Bengaluru'),
    (4, 'Hyderabad');

-- Property ids 1..10 map to image folders img/properties/1..10/
INSERT INTO properties (id, city_id, name, address, description, gender, rent, rating_clean, rating_food, rating_safety) VALUES
    (1, 1, 'Sagar PG', 'Block B, Hauz Khas Enclave, New Delhi', 'A comfortable PG in the heart of South Delhi. Walking distance to IIT Delhi and metro station.\n\nBASICS\n1. Rent is inclusive of food\n2. 4 people sharing a room\n3. Available for 6+ months...', 'male', 7000, 3.8, 4.1, 4.5),
    (2, 1, 'Pooja PG', 'Uttam Nagar East, New Delhi', 'A no-fuss PG near Uttam Nagar metro station. Ideal for students and working women.\n\nBASICS\n1. Rent is inclusive of food\n2. 3 people sharing a room\n3. Available for 6+ months...', 'female', 6000, 4.0, 3.9, 4.6),
    (3, 1, 'Anjali PG', 'Dwarka Sector 12, New Delhi', 'Spacious rooms with good ventilation. Close to Dwarka Sector 12 metro station.\n\nBASICS\n1. Rent is exclusive of food\n2. 2 people sharing a room\n3. Available for 3+ months...', 'unisex', 5500, 4.2, 3.6, 4.4),
    (4, 1, 'Ashoka PG', 'Laxmi Nagar, New Delhi', 'A lively PG near Laxmi Nagar metro station, surrounded by food joints and markets.\n\nBASICS\n1. Rent is inclusive of food\n2. 5 people sharing a room\n3. Available for 1+ months...', 'male', 6500, 3.5, 4.2, 4.1),
    (5, 1, 'New City PG', 'Karol Bagh, New Delhi', 'Modern furnishings and friendly warden. 2 minutes from Karol Bagh metro station.\n\nBASICS\n1. Rent is inclusive of food\n2. 3 people sharing a room\n3. Available for 6+ months...', 'unisex', 7000, 4.1, 4.0, 4.5),
    (6, 1, 'Dream Home PG', 'Rohini Sector 3, New Delhi', 'A homely stay for working women with a caring staff and homely food.\n\nBASICS\n1. Rent is exclusive of food\n2. 2 people sharing a room\n3. Available for 3+ months...', 'female', 5000, 3.9, 4.3, 4.8),
    (7, 1, 'Sharma PG', 'Paschim Vihar, New Delhi', 'Quiet neighbourhood with easy metro access. Ideal for students preparing for exams.\n\nBASICS\n1. Rent is inclusive of food\n2. 4 people sharing a room\n3. Available for 6+ months...', 'male', 6000, 3.7, 3.8, 4.2),
    (8, 1, 'Shree Balaji PG', 'Pitampura, New Delhi', 'Premium PG with power backup and housekeeping included. Near Netaji Subhash Place metro.\n\nBASICS\n1. Rent is inclusive of food\n2. 2 people sharing a room\n3. Available for 6+ months...', 'male', 8500, 4.4, 4.1, 4.6),
    (9, 1, 'Green Leaf PG', 'Vasant Kunj, New Delhi', 'A green, peaceful PG surrounded by parks. Close to Vasant Kunj mall and metro.\n\nBASICS\n1. Rent is exclusive of food\n2. 1 person per room\n3. Available for 3+ months...', 'unisex', 12000, 4.6, 4.3, 4.9),
    (10, 1, 'Sunrise PG', 'Mahipalpur, New Delhi', 'Budget friendly PG near Indira Gandhi International Airport. Great for flight staff.\n\nBASICS\n1. Rent is inclusive of food\n2. 4 people sharing a room\n3. Available for 1+ months...', 'male', 5500, 3.6, 3.9, 4.0);

INSERT INTO amenities (id, name, type, icon) VALUES
    (1, 'Air Conditioner', 'Room', 'ac.svg'),
    (2, 'Bed', 'Room', 'bed.svg'),
    (3, 'CCTV', 'Safety', 'cctv.svg'),
    (4, 'Mess / Dining', 'Food', 'dining.svg'),
    (5, 'Fire Extinguisher', 'Safety', 'fireext.svg'),
    (6, 'Geyser', 'Room', 'geyser.svg'),
    (7, 'Lift', 'Building', 'lift.svg'),
    (8, 'Parking', 'Building', 'parking.svg'),
    (9, 'Power Backup', 'Room', 'powerbackup.svg'),
    (10, 'RO Water Purifier', 'Food', 'rowater.svg'),
    (11, 'TV', 'Room', 'tv.svg'),
    (12, 'Washing Machine', 'Room', 'washingmachine.svg'),
    (13, 'WiFi', 'General', 'wifi.svg');

INSERT INTO properties_amenities (property_id, amenity_id) VALUES
    (1, 2), (1, 4), (1, 5), (1, 6), (1, 9), (1, 13),
    (2, 2), (2, 4), (2, 6), (2, 9), (2, 12), (2, 13),
    (3, 2), (3, 6), (3, 7), (3, 9), (3, 13),
    (4, 2), (4, 4), (4, 6), (4, 9), (4, 10), (4, 13),
    (5, 1), (5, 2), (5, 3), (5, 4), (5, 9), (5, 13),
    (6, 2), (6, 4), (6, 9), (6, 12), (6, 13),
    (7, 2), (7, 4), (7, 6), (7, 9), (7, 13),
    (8, 1), (8, 2), (8, 3), (8, 4), (8, 6), (8, 7), (8, 9), (8, 13),
    (9, 1), (9, 2), (9, 3), (9, 4), (9, 5), (9, 6), (9, 7), (9, 8), (9, 9), (9, 10), (9, 11), (9, 12), (9, 13),
    (10, 2), (10, 4), (10, 6), (10, 9), (10, 13);

INSERT INTO testimonials (id, property_id, user_name, content) VALUES
    (1, 1, 'Amit Sharma', 'Very good PG. Food is excellent and the warden is very helpful.'),
    (2, 1, 'Rahul Verma', 'Nice place to stay for working professionals. Metro is nearby.'),
    (3, 2, 'Priya Singh', 'Safe and clean PG for girls. Highly recommended!'),
    (4, 3, 'Ankit Jain', 'Great value for money and cooperative staff.'),
    (5, 4, 'Vikas Yadav', 'Good location. Lots of food options nearby.'),
    (6, 5, 'Neha Gupta', 'Modern PG with all basic amenities. Loved my stay.'),
    (7, 8, 'Karan Malhotra', 'Premium rooms and the best wifi speed I have seen in a PG.'),
    (8, 9, 'Sneha Reddy', 'Silent surroundings, perfect for study.'),
    (9, 10, 'Mohit Das', 'Budget friendly and quite close to the airport.');

-- Demo user: email demo@pglife.in / password: password
-- (password is stored as a bcrypt hash to mirror password_hash() in api/signup_submit.php)
INSERT INTO users (id, email, password, full_name, phone, gender, college_name) VALUES
    (1, 'demo@pglife.in', '$2y$10$cdRoiwOcSeRF2qi8PRYUDOcsrUdJRXeWPCYwobQYsxKW7b0VLRms2', 'Demo User', '9876543210', 'male', 'Sample College');

INSERT INTO interested_users_properties (user_id, property_id) VALUES
    (1, 1),
    (1, 3),
    (1, 5);