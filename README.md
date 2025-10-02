# Bookings Manager

A simple CRUD web application built with **PHP**, **MySQL**, and **HTML/CSS**.  
This project was designed to help small travel company with simple bookings management.

## Features

- **Add, edit, and delete bookings**
- **Mark bookings as complete/incomplete**
- **Filter bookings by date** using a date picker
- **Totals panel** showing the total number of bookings and total price for the selected day
- **Post-Redirect-Get (PRG)** pattern to prevent duplicate entries when refreshing
- **Prepared statements with PDO** for secure SQL queries
- **Basic styling** with HTML and CSS for readability

## Tech Stack

- **Backend**: PHP 8.x  
- **Database**: MySQL  
- **Frontend**: HTML and custom CSS  
- **Database connection**: PDO for prepared statements

## Database Schema

```sql
CREATE TABLE tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  description VARCHAR(255) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  company VARCHAR(255) NOT NULL,
  completed BOOLEAN NOT NULL DEFAULT FALSE,
  currTime TIME NOT NULL,
  currDate DATE NOT NULL
);

CREATE TABLE currDate (
  id INT PRIMARY KEY,
  currDate DATE NOT NULL
);
