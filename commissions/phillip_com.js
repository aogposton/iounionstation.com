const cheerio = require("cheerio");
const axios = require("axios");
const https = require("https");
const puppeteer = require("puppeteer");
const headless = true;

const getDataFromUrl = async (page, url) => {
    await page.goto(url);
    await page.setDefaultNavigationTimeout(0);
    await page.waitForTimeout(2000);
    const nameElement = await page.waitForSelector(
        "#main > div >div:nth-child(3) > div > div > div > div > div > div:nth-child(3) > div > div "
    );
    const priceElement = await page.waitForSelector("._2Shl1j");

    const name = await (
        await nameElement.getProperty("textContent")
    ).jsonValue();

    let price = await (
        await priceElement.getProperty("textContent")
    ).jsonValue();
    price = price.replace("Lowest Price Guaranteed", "");
    return { price, name };
};
const main = async () => {
    const browser = await puppeteer.launch({
        headless,
        args: [
            "--disable-web-security",
            "--disable-features=IsolateOrigins",
            " --disable-site-isolation-trials",
        ],
    });
    const page = await browser.newPage();
    const url = [
        `https://shopee.ph/Apple-iPhone-13-mini-i.448087759.12020090959`,
        "https://shopee.ph/Baofeng-888S-set-of-4-5W-Two-Way-Radio-Walkie-Talkie-i.71803054.1243915661?sp_atk=b23b17ef-b5bd-463a-8de3-5e0670aa344e&xptdk=b23b17ef-b5bd-463a-8de3-5e0670aa344e",
        "https://shopee.ph/Baofeng-Walkie-talkie-signal-enhancement-antenna-BF-888s-UV-5R-i.71803054.1241542742?sp_atk=48eb1fb2-cade-4114-9e02-41df5e9de331&xptdk=48eb1fb2-cade-4114-9e02-41df5e9de331",
        "https://shopee.ph/SET-OF-2-WLN-KD-C1-UHF-Two-Way-Walkie-Talkie-Radio-5W-16-Channel-Black-i.16407019.2234627028?sp_atk=1ba231dc-3592-46f7-b313-0ff572a8729b&xptdk=1ba231dc-3592-46f7-b313-0ff572a8729b",
        "https://shopee.ph/Original-WLN-KD-C1-Pocket-Size-Portable-Mini-Walkie-Talkie-Two-Way-Radio-UHF-5W-16CHs-i.90624375.1613541423?sp_atk=c2e8df5a-4ea7-4c9f-b239-9fd2562e279e&xptdk=c2e8df5a-4ea7-4c9f-b239-9fd2562e279e",
        "https://shopee.ph/Cetaphil-Brightness-Reveal-Creamy-Cleanser-100g-Evens-Skin-Tone-Brightening-with-Niacnimade--i.159905397.6255278365?sp_atk=8b5e3395-7055-43f0-9832-0b4b4a581d6e",
    ];
    for (let i = 0; i < url.length; i++) {
        const { name, price } = await getDataFromUrl(page, url[i]);
        bot.sendMessage(5292238043, `${name} - ${price}`);
    }
    await page.close();
    await browser.close();
};

function timeout() {
    setTimeout(function () {
        main();
        timeout();
    }, 1 * 60 * 1000); //every minute
}

timeout();
