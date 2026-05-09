import 'dotenv/config';
import fs from 'fs';

function writeJson(path: string, data: any) {
  fs.writeFileSync(path, JSON.stringify(data, null, 2) + "\n");
  console.log("WROTE", path);
}

writeJson("metadata/vusdt.json", {
  name: process.env.VUSDT_NAME,
  symbol: process.env.VUSDT_SYMBOL,
  description: process.env.VUSDT_DESCRIPTION,
  image: process.env.VUSDT_IMAGE,
  decimals: String(process.env.VUSDT_DECIMALS || "6")
});

writeJson("metadata/vshare.json", {
  name: process.env.VSHARE_NAME,
  symbol: process.env.VSHARE_SYMBOL,
  description: process.env.VSHARE_DESCRIPTION,
  image: process.env.VSHARE_IMAGE,
  decimals: String(process.env.VSHARE_DECIMALS || "9")
});
