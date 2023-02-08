const puppeteer = require("puppeteer");
const headless = true;
const fs = require("fs");
const delay = (time) => {
    return new Promise(function (resolve) {
        setTimeout(resolve, time);
    });
};

const getRandomInt = (min, max) => {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min) + min); // The maximum is exclusive and the minimum is inclusive
};

const loginToReddit = async (page) => {
    await page.goto(
        "https://www.reddit.com/login/?dest=https%3A%2F%2Fwww.reddit.com%2F"
    );

    const elementHandle = await page.waitForSelector("#loginUsername");

    await page.$eval("input[id=loginUsername]", (el) => (el.value = "agogp"));
    await page.$eval(
        "input[id=loginPassword]",
        (el) => (el.value = "finalfantasy")
    );

    const submitBtn = await page.$(".AnimatedForm__submitButton");
    await submitBtn.click();
};

const createRedditChat = async (page, to, message) => {
    await delay(1000);
    await page.goto("https://www.reddit.com/chat/channel/create", {
        waitUntil: "load",
        timeout: 30000,
    });
    await delay(3000);

    //Search user
    await page.$eval(
        "input[class=_2clRIy8wf_7H8xNNrUPaiU]",
        (el, to) => (el.value = to),
        to
    );

    await delay(500);
    await page.keyboard.press("Space");
    await delay(500);

    const inp2 = await page.$("input._2clRIy8wf_7H8xNNrUPaiU");
    inp2.type(" ");
    await delay(2000);

    //select first user in list
    await page.evaluate(() => {
        document
            .getElementsByClassName("_3GDyz0bgwoWgoxYSYSxXyA")[2]
            .children[0].children[0].click();
    });
    await delay(3000);

    //Start Chat
    await page.evaluate(() => {
        document.getElementsByClassName("_3QHhpmOrsIj9Hy8FecxWKa")[3].click();
    });

    await delay(3000);

    //fill chat box
    await page.evaluate((message) => {
        document.getElementsByClassName("_24sbNUBZcOO5r5rr66_bs4")[0].value =
            message;
    }, message);

    await delay(3000);

    //refresh page
    const inp = await page.$("textarea._24sbNUBZcOO5r5rr66_bs4");
    inp.type(" ");

    await delay(3000);

    // submit message
    await page.evaluate(() => {
        document.getElementsByClassName("_3QHhpmOrsIj9Hy8FecxWKa")[4].click();
    });

    await delay(3000);

    return await page.evaluate(() => {
        return document.getElementsByClassName(
            "_3GDyz0bgwoWgoxYSYSxXyA _3SalNr9zKm9cow28G6Et8k"
        )[0].children[
            document.getElementsByClassName(
                "_3GDyz0bgwoWgoxYSYSxXyA _3SalNr9zKm9cow28G6Et8k"
            )[0].children.length - 1
        ].children[0].textContent;
    });
};

const attemptToConnect = async () => {
    const browserWSEndpoint = await fs.readFileSync(
        "/home/vagrant/code/nodeScripts/browserWSEndpoint.txt",
        "utf8"
    );
    try {
        const browser = await puppeteer.connect({
            browserWSEndpoint,
        });
        return browser;
    } catch (e) {
        return false;
    }
};

const main = async () => {
    try {
        const to = process.argv[2];
        const message = process.argv[3];

        const browser = await puppeteer.launch({
            executablePath: "/usr/bin/chromium-browser",
            headless: true,
            args: ["--single-process", "--no-zygote", "--no-sandbox"],
        });
        const page = (await browser.pages())[0];
        await loginToReddit(page);
        page.setDefaultNavigationTimeout(100000);

        const text = await createRedditChat(page, to, message);
        console.log(text);
        await browser.close();
    } catch (err) {
        console.log(err);
    }
};
main();
