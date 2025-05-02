CREATE DATABASE IF NOT EXISTS pregnancy_management_system;

USE pregnancy_management_system;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address VARCHAR(255),
    date_of_birth DATE,
    identification_number VARCHAR(50),
    role_id INT NOT NULL, -- 1: Patient, 2: Doctor, 3: Nurse, 4: Admin
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Patient-Specific Data
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    last_menstrual_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Doctor-Specific Data
CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    license_number VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Nurse-Specific Data
CREATE TABLE nurses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nurse_license_number VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE kick_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    log_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    kick_count INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id)
);

CREATE TABLE mother_information (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    registration_number VARCHAR(100),
    id_card_number VARCHAR(100),
    date_of_birth DATE,
    age INT,
    clinic_phone_number VARCHAR(20),
    jkn_serial_number VARCHAR(100),
    antenatal_color_code VARCHAR(50),
    ethnic_group VARCHAR(50),
    nationality VARCHAR(50),
    education_level VARCHAR(50),
    occupation VARCHAR(100),
    home_address_1 TEXT,
    home_address_2 TEXT,
    phone_residential VARCHAR(20),
    phone_mobile VARCHAR(20),
    phone_office VARCHAR(20),
    nurse_ym VARCHAR(100),
    workplace_address TEXT,
    estimated_due_date DATE,
    revised_due_date DATE,
    gravida INT,
    para INT,
    husband_name VARCHAR(100),
    husband_id_card_number VARCHAR(100),
    husband_occupation VARCHAR(100),
    husband_workplace_address TEXT,
    husband_phone_residential VARCHAR(20),
    husband_phone_mobile VARCHAR(20),
    postnatal_address_1 TEXT,
    postnatal_address_2 TEXT,
    postnatal_address_3 TEXT,
    risk_factors TEXT
);

CREATE TABLE past_pregnancy_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    year INT,
    outcome VARCHAR(100),
    delivery_type VARCHAR(100),
    place_and_attendant TEXT,
    gender VARCHAR(10),
    birth_weight DECIMAL(5,2),
    breastfeeding_info TEXT,
    current_condition TEXT,
    marriage_date DATE
);

CREATE TABLE family_health_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    menstruation_days INT,
    menstruation_cycle VARCHAR(100),
    family_planning_method VARCHAR(100),
    family_planning_duration VARCHAR(50),
    smoking_mother BOOLEAN,
    smoking_husband BOOLEAN,
    conditions TEXT,
    tb_screening BOOLEAN,
    cough_more_than_2_weeks BOOLEAN,
    family_conditions TEXT,
    immunisation_status TEXT
);
