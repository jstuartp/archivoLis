const express = require('express');
const fs = require('fs');
const path = require('path');
const mariadb = require('mariadb');
const archiver = require('archiver');
const { readLisHeader, pgaMax } = require('./lisParser');

const app = express();
app.use(express.json());

const EVENT_ROOT = path.join(__dirname, 'events');

const pool = mariadb.createPool({
  host: 'localhost',
  user: 'root',
  password: 'password',
  database: 'Informes'
});

app.get('/api/events', async (req, res) => {
  try {
    const dirents = await fs.promises.readdir(EVENT_ROOT, { withFileTypes: true });
    const folders = dirents.filter(d => d.isDirectory()).map(d => d.name);
    let rows = [];
    try {
      const conn = await pool.getConnection();
      if (folders.length) {
        const placeholders = folders.map(() => '?').join(',');
        rows = await conn.query(
          `SELECT nombre AS name, magnitud, fecha FROM archivoLis WHERE nombre IN (${placeholders})`,
          folders
        );
      }
      conn.release();
    } catch (dbErr) {
      console.error('DB error', dbErr);
    }
    const map = new Map(rows.map(r => [r.name, r]));
    const events = folders.map(name => ({
      name,
      magnitude: map.get(name)?.magnitud || null,
      date: map.get(name)?.fecha || null
    }));
    res.json(events);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

app.get('/api/events/:event', async (req, res) => {
  const eventName = req.params.event;
  const eventDir = path.join(EVENT_ROOT, eventName);
  try {
    const files = (await fs.promises.readdir(eventDir)).filter(f => f.endsWith('.Lis'));
    if (!files.length) return res.json({ files: [] });
    const headers = files.map(f => ({
      name: f,
      header: readLisHeader(path.join(eventDir, f))
    }));
    const first = headers[0].header;
    const info = {
      epicenter: first['Epicenter'] || null,
      date: first['Event date'] || null,
      magnitude: first['Event Magnitude'] || null
    };
    const list = headers.map(h => ({
      file: h.name,
      station: h.header['Station Code'] || '',
      pgaMax: pgaMax(h.header)
    }));
    res.json({ info, files: list });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

app.get('/api/events/:event/files/:file', (req, res) => {
  const filePath = path.join(EVENT_ROOT, req.params.event, req.params.file);
  res.download(filePath);
});

app.post('/api/events/:event/download', (req, res) => {
  const eventDir = path.join(EVENT_ROOT, req.params.event);
  const files = req.body.files || [];
  res.setHeader('Content-Type', 'application/zip');
  res.setHeader('Content-Disposition', 'attachment; filename="files.zip"');
  const archive = archiver('zip');
  archive.on('error', err => res.status(500).send({ error: err.message }));
  archive.pipe(res);
  for (const f of files) {
    const filePath = path.join(eventDir, f);
    archive.file(filePath, { name: f });
  }
  archive.finalize();
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log(`Server listening on ${PORT}`));
