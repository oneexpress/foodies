import { CompilerConfig } from '@ton/blueprint';

export const compile: CompilerConfig = {
  lang: 'func',
  targets: [
    'contracts/stdlib.fc',
    'contracts/op-codes.fc',
    'contracts/params.fc',
    'contracts/jetton-utils.fc',
    'contracts/jetton-wallet.fc'
  ]
};
