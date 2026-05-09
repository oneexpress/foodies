import 'dotenv/config';

if (process.env.TON_NETWORK !== 'mainnet') throw new Error('TON_NETWORK must be mainnet');
if (process.env.VSHARE_DECIMALS !== '9') throw new Error('VSHARE_DECIMALS must be 9');

console.log({
  action: 'DRY_RUN_ONLY',
  token: 'vSHARE',
  name: process.env.VSHARE_NAME,
  symbol: process.env.VSHARE_SYMBOL,
  decimals: process.env.VSHARE_DECIMALS,
  initialSupply: process.env.VSHARE_INITIAL_SUPPLY,
  maxSupply: process.env.VSHARE_MAX_SUPPLY,
  mintableAfterGenesis: false,
  burnable: process.env.VSHARE_BURNABLE,
  treasury: process.env.TON_TREASURY,
  metadata: 'https://expressvisa.one/metadata/vshare.json',
  deployMethod: 'Blueprint wallet flow only',
  broadcast: false
});
