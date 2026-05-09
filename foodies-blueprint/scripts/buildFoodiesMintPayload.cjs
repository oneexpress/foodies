#!/usr/bin/env node
'use strict';

const { beginCell, toNano } = require('@ton/core');

const arg = (k,d='') => {
  const p = k + '=';
  const x = process.argv.slice(2).find(v => v.startsWith(p));
  return x ? x.slice(p.length) : d;
};

const uid = (
  arg('uid','FOODIES-RWA-PENDING')
  .replace(/[^A-Z0-9_-]/g,'')
  || 'FOODIES-RWA-PENDING'
).slice(0,80);

const stars = ['1','3','5'].includes(arg('stars','1'))
  ? arg('stars','1')
  : '1';

const rawBase = (
  arg('rawBase','https://raw.githubusercontent.com/oneexpress/foodies/main/public/metadata/foodies')
).replace(/\/+$/,'');

const collection = 'UQBP2ofSKqVWs_LlSactlylcpeuuxz-sKXYyWNttLz50j6Dh';

const metadataUrl = `${rawBase}/items/${uid}.json`;

const body = beginCell()
  .storeUint(1,32)
  .storeUint(Date.now(),64)
  .storeRef(
    beginCell()
      .storeStringTail(metadataUrl)
      .endCell()
  )
  .endCell();

console.log(JSON.stringify({
  ok:true,
  uid,
  stars:Number(stars),
  collection_address:collection,
  metadata_url:metadataUrl,
  image_url:`${rawBase}/generated/${uid}-${stars}star.png`,
  verify_url:`https://expressvisa.one/foodies-nft/verify.php?uid=${encodeURIComponent(uid)}`,
  messages:[
    {
      address:collection,
      amount:toNano('0.55').toString(),
      payload:body.toBoc().toString('base64')
    }
  ]
},null,2));
