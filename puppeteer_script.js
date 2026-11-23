const puppeteer = require('puppeteer');

(async () => {
    const browser = await puppeteer.launch({headless: true});
    const page = await browser.newPage();

    await page.goto('https://www.betano.de/api/search/?term=HB%20Torshavn%20-%20Eb/Streymur');

    const content = await page.content();
    console.log(content);

    await browser.close();
})();
