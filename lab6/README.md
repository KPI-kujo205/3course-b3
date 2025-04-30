# Notes Application with MongoDB

A simple Node.js application that implements CRUD operations with MongoDB.

## Features

- Create, Read, Update, and Delete notes
- View all notes in a grid layout
- View individual notes
- Edit existing notes
- Delete notes
- JSON API endpoint for all notes

## Prerequisites

- Node.js (v14 or higher) and MongoDB (local or Atlas)
- OR Docker and Docker Compose

## Installation

### Option 1: Local Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   npm install
   ```
3. Create a `.env` file with your MongoDB connection string:
   ```
   PORT=3000
   MONGODB_URI=mongodb://localhost:27017/notes-app
   ```

### Option 2: Docker Installation

1. Clone the repository
2. Make sure Docker and Docker Compose are installed
3. The `.env` file is already configured for Docker

## Running the Application

### Option 1: Local Run

1. Start the application:
   ```bash
   npm start
   ```
   or for development with auto-reload:
   ```bash
   npm run dev
   ```

2. Open your browser and navigate to `http://localhost:3000`

### Option 2: Docker Run

1. Build and start the containers:
   ```bash
   docker-compose up --build
   ```
   or for development with auto-reload:
   ```bash
   docker-compose up --build
   ```

2. Open your browser and navigate to `http://localhost:3000`

To stop the containers:
```bash
docker-compose down
```

To stop and remove volumes (including database data):
```bash
docker-compose down -v
```

## API Endpoints

- `GET /` - View all notes
- `GET /posts/:id` - View a single note
- `GET /add-post` - Form to add a new note
- `POST /add-post` - Create a new note
- `GET /edit/:id` - Form to edit a note
- `POST /edit/:id` - Update a note
- `POST /delete/:id` - Delete a note
- `GET /api/posts` - Get all notes as JSON 