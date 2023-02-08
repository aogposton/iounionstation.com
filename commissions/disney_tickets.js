const puppeteer = require("puppeteer");
const headless = false;

function delay(time) {
    return new Promise(function (resolve) {
        setTimeout(resolve, time);
    });
}
const loginToDisney = async (page) => {
    await page.goto("https://disneyland.disney.go.com/login");
    const disneyIframe = await page.waitForSelector("#disneyid-iframe");
    const elementHandle = await page.waitForSelector(
        "div#disneyid-wrapper iframe"
    );
    const frame = await elementHandle.contentFrame();
    const emailInp = await frame.$(
        "#did-ui-view > div > section > section > form > section > div:nth-child(1) > div > label > span.input-wrapper > input"
    );
    const email = "----some email".split("");

    for (let i = 0; i < email.length; i++) {
        emailInp.type(email[i]);
        await delay(getRandomInt(300, 1000));
    }

    const passwordInp = await frame.$(
        "#did-ui-view > div > section > section > form > section > div:nth-child(2) > div > label > span.input-wrapper > input"
    );
    const password = "----somepassword".split("");
    for (let i = 0; i < password.length; i++) {
        passwordInp.type(password[i]);
        await delay(getRandomInt(300, 1000));
    }

    await delay(getRandomInt(5000, 10000));

    const submitBtn = await frame.$(
        "#did-ui-view > div > section > section > form > section > div.btn-group.touch-print-btn-group-wrapper > button"
    );
    submitBtn.click();
    await delay(getRandomInt(5000, 10000));
};

const main = async () => {
    try {
        const browser = await puppeteer.launch({
            headless,
            // executablePath: "/usr/bin/chromium-browser",
            args: [
                "--disable-web-security",
                "--disable-features=IsolateOrigins",
                " --disable-site-isolation-trials",
            ],
        });
        const page = await browser.newPage();
        await page.setDefaultNavigationTimeout(0);
        await loginToDisney(page);
        await delay(getRandomInt(15000, 20000));

        await page.goto("https://disneyland.disney.go.com/entry-reservation");
        await delay(getRandomInt(15000, 20000));

        await page.evaluate(async () => {
            document
                .querySelector("body > tnp-reservations-spa")
                .shadowRoot.querySelector("#currentPage > tnp-hub-page")
                .shadowRoot.querySelector("div > div > com-button:nth-child(1)")
                .click();
        });

        await delay(getRandomInt(30000, 60000));

        await page.evaluate(async () => {
            document
                .querySelector("body > tnp-reservations-spa")
                .shadowRoot.querySelector(
                    "#currentPage > tnp-add-reservation-select-party-page"
                )
                .shadowRoot.querySelector("#partySelector")
                .shadowRoot.querySelector("#\\36 880041356327949287")
                .shadowRoot.querySelector("#checkbox")
                .shadowRoot.querySelector("#checkboxIcon")
                .click();
        });
        await delay(getRandomInt(30000, 60000));

        await page.evaluate(async () => {
            document
                .querySelector("body > tnp-reservations-spa")
                .shadowRoot.querySelector(
                    "#currentPage > tnp-add-reservation-select-party-page"
                )
                .shadowRoot.querySelector("#reservationPageDockBar")
                .shadowRoot.querySelector("#nextButton")
                .click();
        });
        await delay(getRandomInt(30000, 60000));

        await page.evaluate(async () => {
            document
                .querySelector("body > tnp-reservations-spa")
                .shadowRoot.querySelector(
                    "#currentPage > tnp-add-reservation-select-date-page"
                )
                .shadowRoot.querySelector("#ticketsReservationsCalendar")
                .shadowRoot.querySelector("#calendar0")
                .shadowRoot.querySelector("#nextArrow")
                .click();
        });
        await delay(getRandomInt(10000, 30000));

        const oct4 = await page.evaluate(async () => {
            return document
                .querySelector("body > tnp-reservations-spa")
                .shadowRoot.querySelector(
                    "#currentPage > tnp-add-reservation-select-date-page"
                )
                .shadowRoot.querySelector("#ticketsReservationsCalendar")
                .shadowRoot.querySelector(
                    "#calendar0 > com-calendar-date:nth-child(4)"
                ).ariaLabel;
        });

        const DisneylandAvailable = oct4.includes("Disneyland Park Available");
        const EitherParkAvailable = oct4.includes("Either Park Available");
        const NoneAvailable = oct4.includes("Not selectable Date");

        if (DisneylandAvailable || EitherParkAvailable || !NoneAvailable) {
            iiticket.sendMessage(
                grinbearnz,
                "TICKETS AVIALABLE!!! GO GO GO GO GO!"
            );
            iibot.sendMessage(amon, "TICKETS AVIALABLE!!! GO GO GO GO GO!");
        } else {
            iibot.sendMessage(amon, oct4);
            iibot.sendMessage(grinbearnz, oct4);
        }

        await page.close();
        await browser.close();
    } catch (err) {
        console.log(err);
    }
};
main();

//Run on random interval between an hour and ~hour and a half
function timeout() {
    setTimeout(function () {
        main();
        timeout();
    }, getRandomInt(360000, 500000));
}

function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min) + min); // The maximum is exclusive and the minimum is inclusive
}

timeout();
