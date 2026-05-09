import 'dotenv/config';

console.log({
  workspace: 'ExpressVisa TON Token Workspace',
  network: process.env.TON_NETWORK || 'mainnet',
  vusdt: process.env.VUSDT_SYMBOL || 'vUSDT',
  vshare: process.env.VSHARE_SYMBOL || 'vSHARE',
  metadataBase: process.env.TOKEN_METADATA_BASE || 'https://expressvisa.one/metadata/tokens'
});
