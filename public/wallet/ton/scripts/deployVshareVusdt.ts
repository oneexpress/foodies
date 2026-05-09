import 'dotenv/config';
import process from 'process';
process.setMaxListeners(0);

import { Address, beginCell, toNano } from '@ton/core';
import { compile, NetworkProvider } from '@ton/blueprint';
import { JettonDeploy } from '../wrappers/JettonDeploy';

async function deployToken(
  provider: NetworkProvider,
  symbol: string,
  name: string,
  description: string
) {

  const admin = provider.sender().address!;
  const code = await compile('JettonMinter');

  const content = beginCell()
    .storeUint(0, 8)
    .storeStringTail(JSON.stringify({
      name,
      symbol,
      description,
      image: 'https://expressvisa.one/assets/img/vusdt-logo.png'
    }))
    .endCell();

  const walletCode = await compile('JettonWallet');

  const jetton = JettonDeploy.createFromConfig({
    admin,
    content,
    wallet_code: walletCode
  }, code);

  const opened = provider.open(jetton);

  console.log('');
  console.log('===================================');
  console.log('DEPLOYING:', symbol);
  console.log('ADDRESS:', jetton.address.toString());
  console.log('===================================');

  await opened.sendDeploy(
    provider.sender(),
    toNano('0.25')
  );

  console.log(symbol, 'DEPLOY SENT');
}

export async function run(provider: NetworkProvider) {

  await deployToken(
    provider,
    'vUSDT',
    'ExpressVisa vUSDT',
    '1:1 settlement token pegged to USDT'
  );

  await deployToken(
    provider,
    'vSHARE',
    'ExpressVisa vSHARE',
    'Foodies ecosystem reward and sharing token'
  );

  console.log('');
  console.log('ALL DEPLOYMENTS SUBMITTED');
}
