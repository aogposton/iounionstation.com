const puppeteer = require("puppeteer");
const fs = require("fs").promises;
const _ = require("lodash");
const headless = true;
const TelegramBot = require("node-telegram-bot-api");
const token = "5600018620:AAHezuvb-5XnYSHI7seZ_Hw4-AmKNkvilcY";
const bot = new TelegramBot(token, { polling: true });
let last_updated = formatAMPM(new Date());
bot.onText(/\/get(.+)/, async (msg, match) => {
  const chatId = msg.chat.id;
  const resp = match[1];
  if (resp == "id") bot.sendMessage(chatId, chatId);
  if (resp == "lastupdate") bot.sendMessage(chatId, last_updated);
  if (resp == "facebook") {
    console.log("hal");
    const data = await fs.readFile(
      "facebook" + ".json",
      "utf8",
      function (err, data) {
        let msg = "";
        const parsed = JSON.parse(data);
        return parsed;
      }
    );

    bot.sendMessage(chatId, data);
  }
});

bot.on("polling_error", console.log);

//
const checkEbay = async (page) => {
  await page.goto(
    "https://www.ebay.com/sch/i.html?_from=R40&_nkw=iphone&_sop=10&_dmd=1&rt=nc"
  );
  const newPosts = await page.evaluate(() => {
    const elements = document.querySelectorAll("img.s-item__image-img");
    const results = [];
    for (let i = 1; i < elements.length; i++) {
      const infoBlock =
        elements[i].parentElement.parentElement.parentElement.parentElement
          .parentElement.children[1];

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
  await detectChanges("ebay", newPosts);
};
const detectChanges = async (table, newPosts) => {
  const data = await fs.readFile(table + ".json", "utf8");
  const prevPosts = JSON.parse(data);
  const messages = [];

  // remove link to find matches easily
  const sanitizedPosts = newPosts.map((post) => {
    delete post.link;
    return post;
  });

  if (!_.isEqual(prevPosts, sanitizedPosts)) {
    for (let i = 0; i < sanitizedPosts.length; i++) {
      let found = false;

      for (let j = 0; j < prevPosts.length; j++) {
        found = _.isEqual(sanitizedPosts[i], prevPosts[j]);
        if (found) {
          break;
        }
      }

      if (!found) {
        messages.push(newPosts[i]);
      }
    }
    messages.forEach((post) => {
      let msg = `${table}\n`;

      for (const property in post) {
        if (property == "link") {
          msg += `[Click here to view](${post[property]}\n`;
        } else {
          msg += `${property}: ${post[property]}\n`;
        }
      }

      try {
        [5734934123].forEach((chatId) => {
          bot.sendMessage(chatId, msg, {
            parse_mode: "markdown",
          });
          console.log("sending", msg);
        });
      } catch (error) {
        console.log(error);
      }
    });
    console.log(table, "changed");
  } else {
    console.log("no change");
  }
  await fs.writeFile(table + ".json", JSON.stringify(newPosts));
};
async function autoScroll(page) {
  await page.evaluate(async () => {
    await new Promise((resolve) => {
      var totalHeight = 0;
      var distance = 100;
      var timer = setInterval(() => {
        var scrollHeight = document.body.scrollHeight;
        window.scrollBy(0, distance);
        totalHeight += distance;

        if (totalHeight >= scrollHeight - window.innerHeight) {
          clearInterval(timer);
          resolve();
        }
      }, 100);
    });
  });
}
function formatAMPM(date) {
  var hours = date.getHours();
  var minutes = date.getMinutes();
  var ampm = hours >= 12 ? "pm" : "am";
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  minutes = minutes < 10 ? "0" + minutes : minutes;
  var strTime = hours + ":" + minutes + " " + ampm;
  return strTime;
}
const main = async () => {
  try {
    console.log("begin");
    const browser = await puppeteer.launch({ headless });
    const page = await browser.newPage();
    console.log("check ebay begin");
    await checkEbay(page);
    console.log("heck ebay completed");
    // await checkFacebook(page);
    // await browser.close();
    // last_updated = formatAMPM(new Date());
  } catch (err) {
    console.log(err);
    // bot.sendMessage(5734934123, err);
  }
};
main();
setInterval(main, 10000);
