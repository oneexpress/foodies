import 'dotenv/config';
import { Address, TonClient } from '@ton/ton';

async function main() {
  const treasury = process.env.TON_TREASURY;
  if (!treasury) throw new Error("TON_TREASURY missing");
  const client = new TonClient({ endpoint: "https://toncenter.com/api/v2/jsonRPC" });
  const balance = await client.getBalance(Address.parse(treasury));
  console.log({
    wallet: treasury,
    balance_ton: Number(balance) / 1e9
  });
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
});
