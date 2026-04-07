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


/*
INSERT INTO users (first_name, surname, passwd, birth_year, email, tel, gender, role, description_user, verified_email, email_verified_at) 
VALUES 
-- 1. Admin, ověřený email
('Jan', 'Novák', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1985, 'admin@example.com', '+420123456789', 'male', 'admin', 'Hlavní administrátor systému.', 1, CURRENT_TIMESTAMP),

-- 2. Běžný zaměstnanec, ověřený email
('Petr', 'Svoboda', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1990, 'petr.svoboda@example.com', '+420987654321', 'male', 'user', 'Specialista marketingu.', 1, CURRENT_TIMESTAMP),

-- 3. Zaměstnankyně, ověřený email
('Jana', 'Dvořáková', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1992, 'jana.dvorakova@example.com', '+420111222333', 'female', 'user', 'HR manažerka.', 1, CURRENT_TIMESTAMP),

-- 4. Zaměstnanec, NEOVĚŘENÝ email (pro testování restrikcí)
('Tomáš', 'Krejčí', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1998, 'tomas.krejci@example.com', '+420444555666', 'male', 'user', 'Junior vývojář.', 0, NULL),

-- 5. Zaměstnanec, jiné pohlaví, ověřený email
('Alex', 'Procházka', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1995, 'alex.prochazka@example.com', '+420777888999', 'other', 'user', 'Externí konzultant.', 1, CURRENT_TIMESTAMP);


-- 1. VLOŽENÍ 5 RŮZNÝCH EVENTŮ PRO UŽIVATELE S ID 1 (Vojtech)
INSERT INTO events (event_name, description_event, location, start_at, end_at, voting_deadline, status, creator) 
VALUES 
-- Event 1: Budoucí potvrzený (Zobrazí se v Upcoming / Running)
('Jarní grilovačka', 'První letošní grilování s přáteli. Maso zajištěno, přineste si pití.', 'Park Lužánky', '2026-04-15 15:00:00', '2026-04-15 22:00:00', NULL, 'confirmed', 1),

-- Event 2: Plánovaný s anketou (Zobrazí se ve Voting)
('Teambuilding na chatě', 'Hledáme nejlepší víkend pro výjezd na chatu. Hlasujte prosím do konce měsíce!', 'Krkonoše', NULL, NULL, '2026-03-31 23:59:59', 'planning', 1),

-- Event 3: Minulý event (Zobrazí se v Past events)
('Oslava narozenin', 'Byla to pecka, díky všem co dorazili!', 'Bar v centru', '2026-02-20 19:00:00', '2026-02-21 03:00:00', NULL, 'confirmed', 1),

-- Event 4: Zrušený event
('Kino - Duna 3', 'Bohužel jsem onemocněl, musíme to zrušit a přesunout na jindy.', 'Cinema City', '2026-03-25 18:00:00', '2026-03-25 21:00:00', NULL, 'cancelled', 1),

-- Event 5: Další blížící se event
('Zápas v malém fotbale', 'Jdeme si zahrát fotbal, nezapomeňte sálovky a ručník.', 'Sportovní hala', '2026-04-05 17:00:00', '2026-04-05 19:00:00', NULL, 'confirmed', 1);

-- =========================================================================

-- 2. PŘIDÁNÍ MOŽNOSTÍ DO ANKETY PRO EVENT 2 (Teambuilding na chatě)
-- Předpokládáme, že "Teambuilding na chatě" dostal ID 2 (pokud jsi předtím neměl žádné jiné eventy).
INSERT INTO event_poll_options (event_id, start_at, end_at)
VALUES 
(2, '2026-04-17 15:00:00', '2026-04-19 12:00:00'), -- První navrhovaný víkend
(2, '2026-04-24 15:00:00', '2026-04-26 12:00:00'); -- Druhý navrhovaný víkend
*/