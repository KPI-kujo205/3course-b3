const express = require('express');
const mongoose = require('mongoose');
const path = require('path');
const expressLayouts = require('express-ejs-layouts');
const postRoutes = require('./routes/post-routes');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;

// Set view engine
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));
app.set('layout', 'layout');
app.use(expressLayouts);

// Middleware
app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(express.static(path.join(__dirname, 'public')));

// MongoDB connection
const db = process.env.MONGODB_URI || 'mongodb://localhost:27017/notes-app?retryWrites=true&w=majority';
mongoose
  .connect(db, { 
    useNewUrlParser: true, 
    useUnifiedTopology: true,
    serverSelectionTimeoutMS: 5000
  })
  .then(() => {
    console.log('Connected to DB');
    // Create the database by inserting a dummy document
    const Post = require('./models/Post');
    Post.findOne().then(() => {
      console.log('Database initialized');
    }).catch(() => {
      console.log('No existing documents found');
    });
  })
  .catch((error) => {
    console.error('Database connection error:', error);
    process.exit(1);
  });

// Routes
app.use('/', postRoutes);

// 404 handler
app.use((req, res) => {
  res.status(404).render('error', { 
    title: '404 Not Found',
    error: { message: 'Page not found' }
  });
});

// Error handling middleware
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(500).render('error', { 
    title: 'Error',
    error: err 
  });
});

// Start server
app.listen(PORT, (error) => {
  error ? console.log(error) : console.log(`Listening on port ${PORT}`);
}); 