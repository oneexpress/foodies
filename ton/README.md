# ExpressVisa TON Token Workspace

Tokens:
- vUSDT-TON: settlement token representation
- vSHARE-TON: Foodies Rewards Engine participation token representation

Public rules:
- Use Foodies Rewards Engine publicly.
- Keep poShare / Proof of Share internal only.
- vShare logo: /metadata/991_vshare_logo.png
- vUSDT wallet modules are frozen; do not modify live wallet logic from this workspace.

Deploy flow:
1. Fill `.env`
2. Run `npm install`
3. Add audited jetton contracts/wrappers
4. Run build
5. Deploy vUSDT
6. Deploy vSHARE
7. Save master addresses into `/var/www/secure/.env`
