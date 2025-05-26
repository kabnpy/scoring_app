-- init.sql
--
-- this file contains the sql commands to create the database schema
-- and populate initial data for the scoring application.
-- this script is automatically executed by the mysql docker container
-- when it starts for the first time, thanks to the docker-entrypoint-initdb.d/ directory.

-- create table for judges
CREATE TABLE IF NOT EXISTS judges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL
);

-- create table for users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL
);

-- create table for scores
CREATE TABLE IF NOT EXISTS scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judge_id INT NOT NULL,
    user_id INT NOT NULL,
    points INT NOT NULL CHECK (points >= 1 AND points <= 100),
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (judge_id) REFERENCES judges(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- initial data for users (participants)
-- use INSERT IGNORE to prevent errors if these users already exist (e.g., on container restart without volume removal)
INSERT IGNORE INTO users (username, display_name) VALUES
('alice_p', 'Alice Participant'),
('bob_p', 'Bob Participant'),
('charlie_p', 'Charlie Participant'),
('diana_p', 'Diana Participant'),
('eve_p', 'Eve Participant');
