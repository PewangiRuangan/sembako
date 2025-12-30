-- Database Name: sembako_db

DROP TABLE IF EXISTS claims;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(50) UNIQUE NOT NULL, 
    password VARCHAR(255) NOT NULL,
    residence_type VARCHAR(50) NOT NULL, -- 'kos', 'kontrakan', 'asrama'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE claims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    claimed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    collected_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Dummy Data for Testing (Updated)
INSERT INTO users (full_name, phone_number, password, residence_type) VALUES 
('Ahmad Kos', '08123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'kos'), -- password: password
('Budi Asrama', '08987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'asrama'); -- password: password
