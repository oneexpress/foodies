import 'dotenv/config';

if (process.env.TON_NETWORK !== 'mainnet') throw new Error('TON_NETWORK must be mainnet');
if (process.env.VUSDT_DECIMALS !== '6') throw new Error('VUSDT_DECIMALS must be 6');

console.log({
  action: 'DRY_RUN_ONLY',
  token: 'vUSDT',
  name: process.env.VUSDT_NAME,
  symbol: process.env.VUSDT_SYMBOL,
  decimals: process.env.VUSDT_DECIMALS,
  initialSupply: process.env.VUSDT_INITIAL_SUPPLY,
  mintable: process.env.VUSDT_MINTABLE,
  treasury: process.env.TON_TREASURY,
  metadata: 'https://expressvisa.one/metadata/vusdt.json',
  deployMethod: 'Blueprint wallet flow only',
  broadcast: false
});
