const express = require('express');
const router = express.Router();
const Post = require('../models/Post');
const path = require('path');

// Helper function to create path
const createPath = (page) => path.join(__dirname, `../views/${page}.ejs`);

// Get all posts
router.get('/', async (req, res) => {
  try {
    const posts = await Post.find().sort({ createdAt: -1 });
    res.render('posts', { posts, title: 'Posts' });
  } catch (error) {
    console.error(error);
    res.status(500).render('error', { title: 'Error', error });
  }
});

// Get single post
router.get('/posts/:id', async (req, res) => {
  try {
    const post = await Post.findById(req.params.id);
    res.render('post', { post, title: 'Post' });
  } catch (error) {
    console.error(error);
    res.status(500).render('error', { title: 'Error', error });
  }
});

// Get add post form
router.get('/add-post', (req, res) => {
  res.render('add-post', { title: 'Add Post' });
});

// Create new post
router.post('/add-post', async (req, res) => {
  try {
    const { title, author, text } = req.body;
    const post = new Post({ title, author, text });
    await post.save();
    res.redirect('/');
  } catch (error) {
    console.error(error);
    res.status(500).render('error', { title: 'Error', error });
  }
});

// Get edit post form
router.get('/edit/:id', async (req, res) => {
  try {
    const post = await Post.findById(req.params.id);
    res.render('edit-post', { post, title: 'Edit Post' });
  } catch (error) {
    console.error(error);
    res.status(500).render('error', { title: 'Error', error });
  }
});

// Update post
router.post('/edit/:id', async (req, res) => {
  try {
    const { title, author, text } = req.body;
    await Post.findByIdAndUpdate(req.params.id, { title, author, text });
    res.redirect('/');
  } catch (error) {
    console.error(error);
    res.status(500).render('error', { title: 'Error', error });
  }
});

// Delete post
router.post('/delete/:id', async (req, res) => {
  try {
    await Post.findByIdAndDelete(req.params.id);
    res.redirect('/');
  } catch (error) {
    console.error(error);
    res.status(500).render('error', { title: 'Error', error });
  }
});

// Get all posts as JSON
router.get('/api/posts', async (req, res) => {
  try {
    const posts = await Post.find().sort({ createdAt: -1 });
    res.json(posts);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Internal server error' });
  }
});

module.exports = router; 