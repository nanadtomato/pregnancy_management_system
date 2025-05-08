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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_approved TINYINT DEFAULT 0,
    full_name VARCHAR(255)
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

CREATE TABLE mother_information (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    full_name VARCHAR(100),
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
    risk_factors TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);


CREATE TABLE past_pregnancy_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    year INT,
    marriage_date DATE,
    outcome VARCHAR(100),
    delivery_type VARCHAR(100),
    place_and_attendant TEXT,
    gender VARCHAR(10),
    birth_weight DECIMAL(5,2),
    complications_mother TEXT,
    complications_child TEXT,
    breastfeeding_info TEXT,
    current_condition TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);



CREATE TABLE family_health_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    
    menstruation_days INT,
    menstruation_cycle VARCHAR(100),
    
    family_planning_practice BOOLEAN,
    family_planning_method VARCHAR(100),
    family_planning_duration VARCHAR(50),
    
    smoking_mother BOOLEAN,
    smoking_husband BOOLEAN,

    -- Mother's Medical Conditions
    condition_asthma BOOLEAN DEFAULT FALSE,
    condition_diabetes BOOLEAN DEFAULT FALSE,
    condition_thalassemia BOOLEAN DEFAULT FALSE,
    condition_thyroid BOOLEAN DEFAULT FALSE,
    condition_hypertension BOOLEAN DEFAULT FALSE,
    condition_heart_disease BOOLEAN DEFAULT FALSE,
    condition_allergy BOOLEAN DEFAULT FALSE,
    condition_tb BOOLEAN DEFAULT FALSE,
    condition_cancer BOOLEAN DEFAULT FALSE,
    condition_psychiatric BOOLEAN DEFAULT FALSE,
    condition_anemia BOOLEAN DEFAULT FALSE,
    condition_others TEXT,

    -- Tuberculosis Screening (only cough question kept)
    cough_more_than_2_weeks BOOLEAN,

    -- Family Medical History
    family_asthma BOOLEAN DEFAULT FALSE,
    family_diabetes BOOLEAN DEFAULT FALSE,
    family_anemia BOOLEAN DEFAULT FALSE,
    family_hypertension BOOLEAN DEFAULT FALSE,
    family_heart_disease BOOLEAN DEFAULT FALSE,
    family_thalassemia BOOLEAN DEFAULT FALSE,
    family_allergy BOOLEAN DEFAULT FALSE,
    family_tb BOOLEAN DEFAULT FALSE,
    family_psychiatric BOOLEAN DEFAULT FALSE,
    family_others TEXT,

    -- Immunisation Records
    immunisation_dose1_date DATE,
    immunisation_dose1_batch_no VARCHAR(50),
    immunisation_dose1_expiry DATE,

    immunisation_dose2_date DATE,
    immunisation_dose2_batch_no VARCHAR(50),
    immunisation_dose2_expiry DATE,

    immunisation_booster_date DATE,
    immunisation_booster_batch_no VARCHAR(50),
    immunisation_booster_expiry DATE,

    immunisation_other1 TEXT,
    immunisation_other2 TEXT,

    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);


CREATE TABLE blood_collection_consent (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    mother_fullname VARCHAR(255) NOT NULL,
    mother_nric VARCHAR(20) NOT NULL,
    consent_given TINYINT(1) DEFAULT 0,
    test_blood_group_rhesus TINYINT(1) DEFAULT 0,
    test_hemoglobin TINYINT(1) DEFAULT 0,
    test_diabetes_screening TINYINT(1) DEFAULT 0,
    test_syphilis TINYINT(1) DEFAULT 0,
    test_hiv TINYINT(1) DEFAULT 0,
    test_hepatitis_b TINYINT(1) DEFAULT 0,
    test_malaria TINYINT(1) DEFAULT 0,
    test_others TINYINT(1) DEFAULT 0,
    other_tests VARCHAR(255),
    consent_name VARCHAR(255),
    consent_date DATE,
    witness_name VARCHAR(255),
    witness_nric VARCHAR(20),
    tests VARCHAR(255),
    mother_signature VARCHAR(255),
    FOREIGN KEY (patient_id) REFERENCES patients(id)
);

CREATE TABLE antenatal_blood_screening_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL, -- ID of the pregnant mom (foreign key)
    condition_name VARCHAR(50) NOT NULL, -- example: HIV, Syphilis
    date_collected DATE, -- when the blood was collected
    result VARCHAR(100), -- example: Negative, O Positive
    recorded_by VARCHAR(100), -- who entered the result
    date_recorded DATE, -- when it was entered
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

CREATE TABLE consent_declaration (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    full_name VARCHAR(255),
    id_card_number VARCHAR(20),
    consent_1 ENUM('Yes', 'No'),
    reason_1 TEXT,
    consent_2 ENUM('Yes', 'No'),
    reason_2 TEXT,
    consent_3 ENUM('Yes', 'No'),
    reason_3 TEXT,
    consent_4 ENUM('Yes', 'No'),
    reason_4 TEXT,
    consent_5 ENUM('Yes', 'No'),
    reason_5 TEXT,
    mother_signature_name VARCHAR(255),
    mother_signature_ic VARCHAR(20),
    mother_signature_date DATE,
    witness_name VARCHAR(255),
    witness_ic VARCHAR(20),
    witness_position VARCHAR(100),
    witness_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

CREATE TABLE treatment_refusal_forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id)
);


