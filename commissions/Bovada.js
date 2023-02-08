const puppeteer = require("puppeteer");
const fs = require("fs").promises;
const _ = require("lodash");
const headless = false;
const checkBovada = async (page) => {
  await page.goto("https://www.bovada.lv/sports/football");
  const newPosts = await page.evaluate(() => {
    const elements = document.getElementsByClassName("more-info");
    const results = [];
    for (let i = 7; i < elements.length; i++) {
      const timeAndDate = elements[i].children[0];
      const teamsAndBets = elements[i].children[1];
      const spreadWinTotal = elements[i].children[2];

      let title =
        elements[i].parentElement.parentElement.parentElement.parentElement
          .parentElement.children[1].children[0].textContent;

      title = title.replace("Opens in a new window or tab", "");
      title = title.replace("New Listing", "");
      const price =
        elements[i].parentElement.parentElement.parentElement.parentElement
          .parentElement.children[1].children[2].children[0].textContent;
      // const offer = innerInfoBlock.children[1].textContent;
      // const shipping = innerInfoBlock.children[2].textContent;
      // const location = innerInfoBlock.children[3].textContent;
      const link = elements[i].parentElement.parentElement.href;

      results.push({
        title,
        // price,
        link,
      });
    }
    return results;
  });
  await detectChanges("bovada", newPosts);
};

const main = async () => {
  try {
    const browser = await puppeteer.launch({ headless });
    const page = await browser.newPage();
    await checkBovada(page);
  } catch (err) {
    console.log(err);
  }
};
main();
// setInterval(main, 10000);
