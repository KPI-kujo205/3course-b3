const express = require('express');
const hbs = require('hbs');
const path = require('path');
const axios = require('axios');
const morgan = require('morgan');
const fs = require('fs');
require('dotenv').config();

// Logger setup
const logDirectory = path.join(__dirname, 'logs');

// Create logs directory if it doesn't exist
if (!fs.existsSync(logDirectory)) {
  fs.mkdirSync(logDirectory);
}

// Create a write stream for access logs
const accessLogStream = fs.createWriteStream(
  path.join(logDirectory, 'access.log'),
  { flags: 'a' }
);

const apiKey = process.env.OPENWEATHER_API_KEY;
const app = express();
const port = process.env.PORT || 3333;

// Set up handlebars
app.set('view engine', 'hbs');
app.set('views', path.join(__dirname, 'views'));
hbs.registerPartials(path.join(__dirname, 'views/partials'));

// Register handlebars helpers
hbs.registerHelper('eq', function (a, b) {
  return a === b;
});

// New helper for contains check
hbs.registerHelper('contains', function (str, substr) {
  if (typeof str !== 'string' || typeof substr !== 'string') return false;
  return str.includes(substr);
});

// Middleware
app.use(express.static(path.join(__dirname, 'public')));
// HTTP request logger middleware
app.use(morgan('combined', { stream: accessLogStream }));
app.use(morgan('dev')); // Console logging in development format

// Log all requests
app.use((req, res, next) => {
  console.log(`[${new Date().toISOString()}] ${req.method} ${req.url}`);
  next();
});

// Routes
app.get('/', (req, res) => {
  console.log('Rendering index page');
  res.render('index', {
    title: 'Погода',
    cities: ['Київ', 'Львів', 'Харків', 'Одеса', 'Дніпро', 'Моє місцезнаходження']
  });
});

// Helper function to get coordinates from city name
async function getCoordinates(city) {
  try {
    console.log(`Getting coordinates for city: ${city}`);
    const geocodingUrl = `http://api.openweathermap.org/geo/1.0/direct?q=${city}&limit=1&appid=${apiKey}`;
    console.log(`Geocoding API URL: ${geocodingUrl.replace(apiKey, 'API_KEY_HIDDEN')}`);

    const response = await axios.get(geocodingUrl);

    if (response.data && response.data.length > 0) {
      const { lat, lon } = response.data[0];
      console.log(`Coordinates found: lat ${lat}, lon ${lon}`);
      return { lat, lon };
    } else {
      console.log(`No coordinates found for city: ${city}`);
      throw new Error('City not found');
    }
  } catch (error) {
    console.error(`Error getting coordinates for ${city}:`, error.message);
    throw error;
  }
}


app.get('/weather/:city', async (req, res) => {
  try {
    const city = req.params.city;
    console.log(`Fetching weather data for city: ${city}`);

    // First get coordinates
    const { lat, lon } = await getCoordinates(city);

    // Then get weather data using coordinates
    const weatherData = await getWeatherFromCoordinates(lat, lon);

    console.log(`Weather data received for ${city}:`, JSON.stringify(weatherData, null, 2));

    res.render('weather', {
      title: `Погода у ${city}`,
      city: weatherData.name,
      temperature: weatherData.main.temp,
      description: weatherData.weather[0].description,
      humidity: weatherData.main.humidity,
      windSpeed: weatherData.wind.speed
    });
    console.log(`Successfully rendered weather page for ${city}`);
  } catch (error) {
    console.error(`Error processing weather for ${req.params.city}:`, error.message);
    res.status(404).render('error', {
      title: 'Помилка',
      message: 'Місто не знайдено або дані про погоду недоступні'
    });
  }
});

app.get('/weather', async (req, res) => {
  try {
    console.log('Fetching user location weather data');
    // Get user's location using IP geolocation
    const ipResponse = await axios.get('https://ipapi.co/json/');
    const { latitude, longitude, city } = ipResponse.data;

    console.log(`User location detected: ${city} [${latitude}, ${longitude}]`);

    // Get weather data using coordinates
    const weatherData = await getWeatherFromCoordinates(latitude, longitude);

    console.log(`Weather data received for location:`, JSON.stringify(weatherData, null, 2));

    res.render('weather', {
      title: 'Погода у вашому місцезнаходженні',
      city: weatherData.name,
      temperature: weatherData.main.temp,
      description: weatherData.weather[0].description,
      humidity: weatherData.main.humidity,
      windSpeed: weatherData.wind.speed
    });
    console.log(`Successfully rendered weather page for user location: ${weatherData.name}`);
  } catch (error) {
    console.error('Error fetching location weather:', error.message);
    res.status(404).render('error', {
      title: 'Помилка',
      message: 'Неможливо отримати дані про погоду для вашого місцезнаходження'
    });
  }
});

// Error handling middleware
app.use((req, res, next) => {
  console.log(`404 Not Found: ${req.method} ${req.url}`);
  res.status(404).render('error', {
    title: '404 Сторінку не знайдено',
    message: 'Сторінку, яку ви запитали, не знайдено'
  });
});

app.use((err, req, res, next) => {
  console.error(`Error: ${err.message}`);
  res.status(500).render('error', {
    title: 'Помилка сервера',
    message: 'Щось пішло не так на сервері'
  });
});

app.listen(port, () => {
  console.log(`[${new Date().toISOString()}] Server is running on port ${port}`);
  console.log(`[${new Date().toISOString()}] Environment: ${process.env.NODE_ENV || 'development'}`);
  console.log(`[${new Date().toISOString()}] API Key configured: ${apiKey ? 'Yes' : 'No'}`);
});


// Helper function to get weather data from coordinates
async function getWeatherFromCoordinates(lat, lon) {
  try {
    console.log(`Getting weather for coordinates: lat ${lat}, lon ${lon}`);
    const weatherUrl = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${apiKey}&units=metric`;
    console.log(`Weather API URL: ${weatherUrl.replace(apiKey, 'API_KEY_HIDDEN')}`);
    
    const response = await axios.get(weatherUrl);
    return response.data;
  } catch (error) {
    console.error(`Error getting weather for coordinates lat ${lat}, lon ${lon}:`, error.message);
    throw error;
  }
}

