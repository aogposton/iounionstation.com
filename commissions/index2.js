const cheerio = require("cheerio");
const axios = require("axios");
const https = require("https");

axios.defaults.timeout = 30000;
axios.defaults.httpsAgent = new https.Agent({ keepAlive: true });

var $ = require("jquery");

const url = `https://www.facebook.com/marketplace/london/search?deliveryMethod=local_pick_up&query=Brompton`;

const getCheerioObject = async (link) => {
  const pageHtml = (await axios.get(link)).data;
  return cheerio.load(pageHtml);
};

const checkFacebook = async (page) => {
//   await page.goto(
//     "https://www.facebook.com/marketplace/london/search/?daysSinceListed=1&deliveryMethod=local_pick_up&sortBy=creation_time_descend&query=Brompton&exact=false"
//   );
//   await delay(5000);

//   const query = "img";
//   const elements = await page.$(query);

//   await page.setViewport({
//     width: 1200,
//     height: 800,
//   });

//   // await autoScroll(page);

//   const newPosts = await page.evaluate(() => {
//     const elements = document.querySelectorAll("img");

//     const results = [];
//     for (let i = 0; i < elements.length; i++) {
//       const postElement =
//         elements[i].parentNode.parentElement.parentElement.parentElement
//           .parentElement.parentElement.parentElement.children[1];
//       const link =
//         elements[i].parentElement.parentElement.parentElement.parentElement
//           .parentElement.parentElement.parentElement.parentElement.href;
//       const price = postElement.children[0].textContent;
//       const title = postElement.children[1].textContent;
//       const location = postElement.children[2].textContent;

//       results.push({ price, title, location, link });
//     }
//     return results;
//   });

//   await detectChanges("facebook", newPosts);
// };

// const getAllNonProfits = async () => {
//   const nonprofitArry = [];
//   const totalPages = await getPageCount();
//   let currentPage = 1;

//   for (currentPage; currentPage < totalPages; currentPage++) {
//
//     $(".gnp-searchResult-infoMajor h2 > a").each((index, el) => {
//       const link = el.attribs.href;
//       if (!nonprofitArry.includes(link)) {
//         nonprofitArry.push(link);
//       }
//     });
//   }

//   return nonprofitArry;
// };

// const getNonProfitInfo = async (link) => {
//   const $ = await getCheerioObject(link);
//   const infoObj = {
//     name: "",
//     causes: [],
//     mission: "",
//     geographicAreasServed: "",
//     pageViews: 0,
//     programs: "",
//     contact: {},
//     url: [],
//   };

//   $(".causes > a").each((id, element) => {
//     infoObj.causes.push(element.children[0].data);
//   });

//   infoObj.mission = $('p:contains("Mission")').text();
//   infoObj.pageViews = parseInt($(".gnp-pageviews:first").text());
//   infoObj.beneficiariesPerYear = $(
//     'p:contains("Direct beneficiaries per year")'
//   ).text();
//   infoObj.geographicAreasServed = $(
//     'p:contains("Geographic areas served")'
//   ).text();
//   infoObj.programs = $("p:contains(Programs)").text();
//   infoObj.name = $('h1[itemprop="name"]').text();
//   infoObj.taxId = $('span[itemprop="taxID"]').text();
//   infoObj.streetAddress = $('span[itemprop="streetAddress"]').text();
//   infoObj.addressLocality = $('span[itemprop="addressLocality"]').text();
//   infoObj.addressRegion = $('span[itemprop="addressRegion"]')
//     .text()
//     .replace(/^\s+|\s+$/gm, "");
//   infoObj.postalCode = $('span[itemprop="postalCode"]').text();
//   infoObj.addressCountry = $('span[itemprop="addressCountry"]').text();
//   $('a[itemprop="url"]').each((id, el) => {
//     const link = el.attribs.href;
//     infoObj.url.push(link);
//   });
//   infoObj.telephone = $('span[itemprop="telephone"]').text();

//   return infoObj;
// };

const main = async () => {
  // const nonprofitArry = await getAllNonProfits();
  // const $ = await getCheerioObject(url);
  console.log((await axios.get(url)).data);
  // console.log($("img"));
  // setTimeout(() => {
  //   console.log("wait a sec");
  // }, 5000);
  // for (let i = 0; i < nonprofitArry.length; i++) {
  //   setTimeout(async () => {
  //     const infoObj = await getNonProfitInfo(url + nonprofitArry[i]);
  //     await csvWriter.writeRecords([infoObj]);
  //     console.log(`Got ${i}: ${infoObj.name}`);
  //   }, i * 100);
  // }
};

main();
