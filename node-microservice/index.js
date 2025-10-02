const express = require('express');
const cors = require('cors');
const app = express();

app.use(cors());

app.get('/health', (req, res) => {
    res.json({ status: 'ok from Node' });
});

app.listen(3000, () => console.log('Node.js microservice running on port 3000'));
