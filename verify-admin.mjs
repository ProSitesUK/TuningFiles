import { chromium } from 'playwright';

const browser = await chromium.launch();
const ctx = await browser.newContext({
  viewport: { width: 1440, height: 900 },
  ignoreHTTPSErrors: true,
});
const page = await ctx.newPage();

// Login
await page.goto('https://tuningfiles.test/login');
await page.fill('input[type="email"]', 'stuart@digitaldra.co.uk');
await page.fill('input[type="password"]', 'TuningAdmin2026!');
await page.click('button[type="submit"]');
await page.waitForURL('**/admin/**', { timeout: 10000 });

// Desktop screenshots
await page.screenshot({ path: 'verify-desktop-live.png', fullPage: false });
console.log('desktop live queue captured');

await page.goto('https://tuningfiles.test/admin/overview');
await page.waitForTimeout(1500);
await page.screenshot({ path: 'verify-desktop-overview.png', fullPage: false });
console.log('desktop overview captured');

await page.goto('https://tuningfiles.test/admin/customers');
await page.waitForTimeout(1500);
await page.screenshot({ path: 'verify-desktop-customers.png', fullPage: false });
console.log('desktop customers captured');

// Mobile screenshots
await page.setViewportSize({ width: 375, height: 812 });

await page.goto('https://tuningfiles.test/admin/live');
await page.waitForTimeout(1500);
await page.screenshot({ path: 'verify-mobile-live.png', fullPage: false });
console.log('mobile live queue captured');

await page.goto('https://tuningfiles.test/admin/overview');
await page.waitForTimeout(1500);
await page.screenshot({ path: 'verify-mobile-overview.png', fullPage: false });
console.log('mobile overview captured');

await browser.close();
console.log('done');
