# Round-Robin Tournament Simulation

A modern web application that simulates round-robin tournament scheduling, automatically generating games and results.

## Features

- Create tournaments with 2-12 teams
- Automatic team name generation
- Round-robin tournament game scheduling based on team count
- Automatic game simulation with random basketball-like scores
- Team standings with win/loss tracking
- Real-time updates and dynamic UI

## Requirements

- PHP 8.2 or higher
- MySQL
- Composer

## Project Setup

1. Clone the repository.

2. Install dependencies:
   composer install

3. Configure your environment:
   Create new `.env` file and copy `.env.example` content
   Edit with your database credentials

4. Create the database:
   mysql -u your_user -p CREATE DATABASE tournament_db;

5. Run migrations:
   composer migrations:migrate

## Running the Application

1. Start the PHP development server (make sure port is free for use):
   php -S localhost:8000

2. Visit `http://localhost:8000` in your browser

## Summary of approach

The plan was to create a simple architecture by leveraging Doctrine ORM for basic entity management and changes, and by using an OOP approach to encapsulate each project component within its own class. Specifically, I developed three data layer entities - Tournament, Team, and Game and corresponding service layers - TournamentService, TeamService, and GameService to handle the business logic.

The project also includes a small custom router in the index.php file that serves a Vue.js based HTML front end. While the front end fairly simple, but visually appealing and logically structured, the primary focus was definitely on the back-end components.

Additionally, I implemented PHPUnit tests for each service and entity to ensure the overall stability of the application.

## Technology Stack

- PHP 8.3
- Doctrine ORM for database operations
- PHPUnit Testing framework
- FakerPHP for test data generation
- Vue.js
- Axios for API communication

## API Endpoints

- `POST /tournament/create` - Create a new tournament
- `GET /tournament/{id}` - Get tournament results

### Running Tests

composer phpunit:run

## Available Commands

- composer migrations:diff # Used to generate migration versions based of entity changes
- composer migrations:migrate # Run pending/ready migrations
- composer migrations:status # Check migration status
