require("chromedriver");
const webdriver = require("selenium-webdriver");
const By = require("selenium-webdriver").By;

let num = 1;

const main = async () => {
  openSite("http://www.korean-books.com.kp/en/search/?page=all");
};
const openSite = async (url) => {
  // const puppet = await puppeteer.launch();
  // const page = await puppet.newPage();
  const browser = new webdriver.Builder().forBrowser("chrome").build();
  browser.get(url);
  // wait for...
  // xGalleryFrame

  element = browser
    .findElement
    // By.xpath('//*[@id="HHLText"]')
    // By.xpath("/html/body/div[4]/div[2]/table/tbody/tr[1]/td[1]/div/p[2]/img")
    ();
  await element.click();
  // const res = await page.content();
};
main();
