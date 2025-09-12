const path = require('path');
const { readLisHeader, pgaMax } = require('./lisParser');

const file = path.join(__dirname, 'events/sampleEvent/sample1.Lis');
const header = readLisHeader(file);
console.log('Header keys:', Object.keys(header));
console.log('PGA max:', pgaMax(header));
