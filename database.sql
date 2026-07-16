CREATE DATABASE IF NOT EXISTS petapp_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE petapp_db;

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    profile_picture VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS pets (
    pet_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    pet_name VARCHAR(50) NOT NULL,
    breed VARCHAR(50),
    age INT,
    weight DECIMAL(5,2),
    pet_photo VARCHAR(255),
    pet_creat_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pets_user FOREIGN KEY (user_id)
        REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL UNIQUE,
    category_icon VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS health_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT NOT NULL,
    category_id INT NOT NULL,
    log_title VARCHAR(100) NOT NULL,
    description TEXT,
    status ENUM('Pending', 'Completed', 'Administered') DEFAULT 'Pending',
    log_date DATE NOT NULL,
    log_create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_health_logs_pet FOREIGN KEY (pet_id)
        REFERENCES pets(pet_id) ON DELETE CASCADE,
    CONSTRAINT fk_health_logs_category FOREIGN KEY (category_id)
        REFERENCES categories(category_id)
);

CREATE TABLE IF NOT EXISTS appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    appointment_date DATETIME NOT NULL,
    location VARCHAR(150),
    notes TEXT,
    status ENUM('Upcoming', 'Completed', 'Cancelled') DEFAULT 'Upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_appointments_pet FOREIGN KEY (pet_id)
        REFERENCES pets(pet_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS weight_logs (
    weight_id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT NOT NULL,
    weight DECIMAL(5,2) NOT NULL,
    log_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_weight_logs_pet FOREIGN KEY (pet_id)
        REFERENCES pets(pet_id) ON DELETE CASCADE
);

INSERT IGNORE INTO categories (category_id, category_name, category_icon) VALUES
    (1, 'Vaccination', 'vaccine_icon.png'),
    (2, 'Medication', 'medication_icon.png'),
    (3, 'Vet Visit', 'vet_icon.png'),
    (4, 'Grooming', 'grooming_icon.png');
