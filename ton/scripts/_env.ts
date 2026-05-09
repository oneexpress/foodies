import fs from 'fs';

const CANONICAL_ENV = '/var/www/secure/.env';

export function loadCanonicalEnv(): Record<string, string> {
  if (!fs.existsSync(CANONICAL_ENV)) {
    throw new Error('Missing canonical env file: /var/www/secure/.env');
  }

  const env: Record<string, string> = {};

  for (const raw of fs.readFileSync(CANONICAL_ENV, 'utf8').split(/\r?\n/)) {
    const line = raw.trim();
    if (!line || line.startsWith('#') || !line.includes('=')) continue;

    const idx = line.indexOf('=');
    const key = line.slice(0, idx).trim();
    let val = line.slice(idx + 1).trim();

    if (
      (val.startsWith('"') && val.endsWith('"')) ||
      (val.startsWith("'") && val.endsWith("'"))
    ) {
      val = val.slice(1, -1);
    }

    env[key] = val;
    process.env[key] = process.env[key] || val;
  }

  return env;
}

export function requireCanonicalEnv(key: string): string {
  const env = loadCanonicalEnv();
  const val = env[key] || process.env[key] || '';
  if (!val.trim()) throw new Error(`Missing required env key: ${key}`);
  return val.trim();
}
