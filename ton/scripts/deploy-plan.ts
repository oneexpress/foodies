import 'dotenv/config';
import fs from 'fs';

const tokens = [
  {
    key: 'VUSDT',
    name: process.env.VUSDT_NAME,
    symbol: process.env.VUSDT_SYMBOL,
    decimals: process.env.VUSDT_DECIMALS,
    initialSupply: process.env.VUSDT_INITIAL_SUPPLY,
    mintable: process.env.VUSDT_MINTABLE === '1',
    burnable: false,
    metadata: 'https://expressvisa.one/metadata/vusdt.json'
  },
  {
    key: 'VSHARE',
    name: process.env.VSHARE_NAME,
    symbol: process.env.VSHARE_SYMBOL,
    decimals: process.env.VSHARE_DECIMALS,
    initialSupply: process.env.VSHARE_INITIAL_SUPPLY,
    maxSupply: process.env.VSHARE_MAX_SUPPLY,
    mintableAfterGenesis: false,
    burnable: process.env.VSHARE_BURNABLE === '1',
    metadata: 'https://expressvisa.one/metadata/vshare.json'
  }
];

const plan = {
  project: 'ExpressVisa 991',
  network: process.env.TON_NETWORK,
  treasury: process.env.TON_TREASURY,
  deployMode: 'Blueprint wallet flow only',
  mnemonic: 'FORBIDDEN',
  autoBroadcast: false,
  estimatedDeployTonPerToken: '0.08 - 0.15 TON',
  tokens
};

fs.writeFileSync('artifacts/deploy-plan.json', JSON.stringify(plan, null, 2) + '\n');
console.log(JSON.stringify(plan, null, 2));
