const fs = require('fs');

function parseLisHeader(content) {
  const lines = content.split(/\r?\n/);
  const data = {};
  for (const line of lines) {
    if (!line.trim()) break;
    const [key, ...rest] = line.split(':');
    if (key && rest.length) {
      data[key.trim()] = rest.join(':').trim();
    }
  }
  return data;
}

function readLisHeader(filePath) {
  const content = fs.readFileSync(filePath, 'utf8');
  return parseLisHeader(content);
}

function pgaMax(header) {
  const keys = ['PGA-N00E', 'PGA-UPDO', 'PGA+N90E'];
  const values = keys
    .map(k => parseFloat(header[k]))
    .filter(v => !isNaN(v));
  return values.length ? Math.max(...values) : null;
}

module.exports = { parseLisHeader, readLisHeader, pgaMax };
