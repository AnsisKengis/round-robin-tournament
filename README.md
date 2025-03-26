# Round-Robin Tournament Simulation

A PHP application that simulates round-robin tournament scheduling, generates games and results.

## Features

- Create tournaments with 2-12 teams
- Automatic team name generation
- Round-robin tournament game scheduling based on team count
- Automatic game simulation with random basketball like scores
- Team standings with win/loss tracking

## Requirements

- PHP 8.2 or higher
- MySQL
- Composer

## Project Setup

1. Clone the repository.

2. Install dependencies:
   composer install

3. Configure your environment:
   Create new .env file and copy .env.example
   Edit with your database credentials

4. Create the database:
   mysql -u your_user -p CREATE DATABASE tournament_db;

5. Run migrations:
   composer migrations:migrate

## Running the Application

1. Start the PHP development server:
   php -S localhost:8000

2. Visit `http://localhost:8000` in your browser

## Technology Stack

- PHP 8.3
- Doctrine ORM
- PHPUnit Testing framework
- FakerPHP: Generation of realistic test data
- Plain PHP Templates with HTML for front end
- CSS Basic styling

### Running Tests

composer phpunit:run

## Database Schema

The application uses three main entities:

- Tournament: Manages tournament lifecycle
- Team: Tracks team statistics and tournament participation
- Game: Records game results, rounds played and winners

## Available Commands

composer migrations:diff # Used to generate migration versions based of entity changes
composer migrations:migrate # Run pending/ready migrations
composer migrations:status # Check migration status
