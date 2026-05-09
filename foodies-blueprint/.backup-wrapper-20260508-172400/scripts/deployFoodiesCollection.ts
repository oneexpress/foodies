import 'dotenv/config';

console.log('===== @foodies DEPLOY CONFIG =====');
console.log('Name:', process.env.FOODIES_COLLECTION_NAME);
console.log('Metadata:', process.env.FOODIES_COLLECTION_URL);
console.log('Owner:', process.env.FOODIES_OWNER);
console.log('Treasury:', process.env.FOODIES_TREASURY);
console.log('Royalty BP:', process.env.FOODIES_ROYALTY_BP);
console.log('Public Mint:', process.env.FOODIES_PUBLIC_MINT);
console.log('Treasury Contribution TON:', process.env.FOODIES_TREASURY_CONTRIBUTION_TON);

console.log('');
console.log('STOP: deploy wrapper/contract must be inserted before broadcasting.');
console.log('This preflight confirms metadata + workspace only.');
