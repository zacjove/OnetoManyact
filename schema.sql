CREATE DATABASE IF NOT EXISTS transport_app;
USE transport_app;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    date_of_birth DATE
);

CREATE TABLE modes (
    mode_id INT AUTO_INCREMENT PRIMARY KEY,
    mode_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    added_by INT,
    updated_by INT,
    FOREIGN KEY (added_by)
        REFERENCES users(user_id)
        ON DELETE SET NULL,
    FOREIGN KEY (updated_by)
        REFERENCES users(user_id)
        ON DELETE SET NULL
);

CREATE TABLE routes (
    route_id INT AUTO_INCREMENT PRIMARY KEY,
    route_name VARCHAR(150) NOT NULL,
    origin VARCHAR(150) NOT NULL,
    destination VARCHAR(150) NOT NULL,
    mode_id INT NOT NULL,
    added_by INT,
    updated_by INT,

    FOREIGN KEY (mode_id)
        REFERENCES modes(mode_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    FOREIGN KEY (added_by)
        REFERENCES users(user_id)
        ON DELETE SET NULL,
    FOREIGN KEY (updated_by)
        REFERENCES users(user_id)
        ON DELETE SET NULL
);
