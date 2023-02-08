const puppeteer = require("puppeteer");
const fs = require("fs").promises;
const _ = require("lodash");
const headless = false;

function delay(time) {
    return new Promise(function (resolve) {
        setTimeout(resolve, time);
    });
}
const clickNextPage = async (page) => {
    await page.goto("https://disneyland.disney.go.com/login");
};
// const loginToDisney = async (page) => {
//     const disneyIframe = await page.waitForSelector("#disneyid-iframe");
//     const elementHandle = await page.waitForSelector(
//         "div#disneyid-wrapper iframe"
//     );
//     const frame = await elementHandle.contentFrame();
//     const emailInp = await frame.$(
//         "#did-ui-view > div > section > section > form > section > div:nth-child(1) > div > label > span.input-wrapper > input"
//     );
//     const email = "grin.nzl@gmail.com".split("");

//     for (let i = 0; i < email.length; i++) {
//         emailInp.type(email[i]);
//         await delay(getRandomInt(300, 1000));
//     }

//     const passwordInp = await frame.$(
//         "#did-ui-view > div > section > section > form > section > div:nth-child(2) > div > label > span.input-wrapper > input"
//     );
//     const password = "Reeferm3n99".split("");
//     for (let i = 0; i < password.length; i++) {
//         passwordInp.type(password[i]);
//         await delay(getRandomInt(300, 1000));
//     }

//     await delay(getRandomInt(5000, 10000));

//     const submitBtn = await frame.$(
//         "#did-ui-view > div > section > section > form > section > div.btn-group.touch-print-btn-group-wrapper > button"
//     );
//     submitBtn.click();
//     await delay(getRandomInt(5000, 10000));
// };

const main = async () => {
    try {
        const browser = await puppeteer.launch({
            headless,
            executablePath: "/usr/bin/chromium-browser",
            args: [
                "--disable-web-security",
                "--disable-features=IsolateOrigins",
                " --disable-site-isolation-trials",
            ],
        });
        const page = await browser.newPage();
        await page.setDefaultNavigationTimeout(0);
        await clickNextPage(page);
        await delay(2000000);

        // await page.goto("https://disneyland.disney.go.com/entry-reservation");
        // await delay(getRandomInt(15000, 20000));

        // await page.evaluate(async () => {
        //     document
        //         .querySelector("body > tnp-reservations-spa")
        //         .shadowRoot.querySelector("#currentPage > tnp-hub-page")
        //         .shadowRoot.querySelector("div > div > com-button:nth-child(1)")
        //         .click();
        // });

        // await delay(getRandomInt(30000, 60000));

        // await page.evaluate(async () => {
        //     document
        //         .querySelector("body > tnp-reservations-spa")
        //         .shadowRoot.querySelector(
        //             "#currentPage > tnp-add-reservation-select-party-page"
        //         )
        //         .shadowRoot.querySelector("#partySelector")
        //         .shadowRoot.querySelector("#\\36 880041356327949287")
        //         .shadowRoot.querySelector("#checkbox")
        //         .shadowRoot.querySelector("#checkboxIcon")
        //         .click();
        // });
        // await delay(getRandomInt(30000, 60000));

        // await page.evaluate(async () => {
        //     document
        //         .querySelector("body > tnp-reservations-spa")
        //         .shadowRoot.querySelector(
        //             "#currentPage > tnp-add-reservation-select-party-page"
        //         )
        //         .shadowRoot.querySelector("#reservationPageDockBar")
        //         .shadowRoot.querySelector("#nextButton")
        //         .click();
        // });
        // await delay(getRandomInt(30000, 60000));

        // await page.evaluate(async () => {
        //     document
        //         .querySelector("body > tnp-reservations-spa")
        //         .shadowRoot.querySelector(
        //             "#currentPage > tnp-add-reservation-select-date-page"
        //         )
        //         .shadowRoot.querySelector("#ticketsReservationsCalendar")
        //         .shadowRoot.querySelector("#calendar0")
        //         .shadowRoot.querySelector("#nextArrow")
        //         .click();
        // });
        // await delay(getRandomInt(10000, 30000));

        // const oct4 = await page.evaluate(async () => {
        //     return document
        //         .querySelector("body > tnp-reservations-spa")
        //         .shadowRoot.querySelector(
        //             "#currentPage > tnp-add-reservation-select-date-page"
        //         )
        //         .shadowRoot.querySelector("#ticketsReservationsCalendar")
        //         .shadowRoot.querySelector(
        //             "#calendar0 > com-calendar-date:nth-child(4)"
        //         ).ariaLabel;
        // });

        // await page.close();
        // await browser.close();
    } catch (err) {
        console.log(err);
    }
};
main();
