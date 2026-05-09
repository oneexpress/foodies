import { loadCanonicalEnv, requireCanonicalEnv } from './_env';

loadCanonicalEnv();

export function requireEnv(name: string): string {
  return requireCanonicalEnv(name);
}

export const tokenConfig = {
  network: process.env.TON_NETWORK || 'mainnet',
  vusdt: {
    symbol: 'vUSDT',
    name: 'ExpressVisa vUSDT',
    decimals: 6,
    metadata: 'https://expressvisa.one/metadata/tokens/vusdt.json',
    logo: 'https://expressvisa.one/metadata/991_visa_logo_only.png',
    initialSupply: 10000000,
    mintable: true,
    maxSupply: null
  },
  vshare: {
    symbol: 'vSHARE',
    name: 'Foodies vSHARE',
    decimals: 6,
    metadata: 'https://expressvisa.one/metadata/tokens/vshare.json',
    logo: 'https://expressvisa.one/metadata/991_vshare_logo.png',
    burnable: true,
    boostCoefficient: 0.003,
    maxBoost: 30,
    baseRatePer10s: 0.33
  }
};
