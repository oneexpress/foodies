#!/usr/bin/env node
'use strict';
const { Address } = require('@ton/core');
const input = String(process.argv[2] || '').trim();
try {
  const a = Address.parse(input);
  console.log(a.toString({ urlSafe: true, bounceable: true, testOnly: false }));
} catch (e) {
  console.error('bad_address', input);
  process.exit(1);
}
