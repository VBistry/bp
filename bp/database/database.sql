DROP TABLE IF EXISTS event_poll_votes;
DROP TABLE IF EXISTS event_poll_options;
DROP TABLE IF EXISTS event_comments;
DROP TABLE IF EXISTS event_registrations;
DROP TABLE IF EXISTS friendships;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS users;

CREATE DATABASE IF NOT EXISTS planner CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE planner;

--- USERS ---
CREATE TABLE users (
    id INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    surname VARCHAR(100) NOT NULL,
    passwd VARCHAR(255) NOT NULL,
    birth_year INTEGER NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    tel VARCHAR(20) NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    description_user VARCHAR(1000) DEFAULT NULL,
    avatar_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    verified_email BOOLEAN DEFAULT FALSE,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    password_reset_token VARCHAR(255) DEFAULT NULL,
    password_reset_expires_at TIMESTAMP NULL DEFAULT NULL
);

--- EVENTS ---
CREATE TABLE events (
    id INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    event_name VARCHAR(150) NOT NULL,
    description_event VARCHAR(1000) DEFAULT NULL,
    location VARCHAR(255) DEFAULT NULL,
    start_at DATETIME DEFAULT NULL,
    end_at DATETIME DEFAULT NULL,
    voting_deadline DATETIME DEFAULT NULL,
    status ENUM('planning', 'confirmed', 'cancelled') DEFAULT 'confirmed',
    creator INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (creator) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
);

--- POLL OPTIONS ---
CREATE TABLE event_poll_options (
    id INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    event_id INTEGER NOT NULL,
    start_at DATETIME NOT NULL,
    end_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- POLL VOTES ---
CREATE TABLE event_poll_votes (
    id INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    event_id INTEGER NOT NULL,
    option_id INTEGER NOT NULL,  -- Which option the user voted for
    user_id INTEGER NOT NULL,
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (option_id) REFERENCES event_poll_options(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE(user_id, event_id)
);

--- INVITAIONS/REGISTRATIONS ---
CREATE TABLE event_registrations (
    id INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id INTEGER NOT NULL,
    event_id INTEGER NOT NULL,
    status ENUM('pending', 'attending', 'declined') DEFAULT 'pending',
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE(user_id, event_id)
);

--- FRIENDSHIPS ---
CREATE TABLE friendships(
    id INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id_sender INTEGER NOT NULL,
    user_id_receiver INTEGER NOT NULL,
    FOREIGN KEY (user_id_sender) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (user_id_receiver) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    friends_since TIMESTAMP NULL,
    status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
    UNIQUE(user_id_sender,  user_id_receiver)
);
