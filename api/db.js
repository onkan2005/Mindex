const { Client } = require('pg');

const client = new Client({
  user: process.env.PG_USER,
  host: process.env.PG_HOST,
  database: process.env.PG_DB,
  password: process.env.PG_PASSWORD,
  port: process.env.PG_PORT,
  ssl: { rejectUnauthorized: false },  // Enable SSL if required
});

module.exports = async (req, res) => {
  try {
    await client.connect();
    const result = await client.query('SELECT COUNT(*) FROM datasets');
    res.status(200).json(result.rows);
  } catch (err) {
    res.status(500).json({ error: 'Database query failed', message: err.message });
  } finally {
    await client.end();
  }
};
