const cheerio = require("cheerio");
const axios = require("axios");
const https = require("https");
axios.defaults.timeout = 30000;
axios.defaults.httpsAgent = new https.Agent({ keepAlive: true });

var $ = require("jquery");

const createCsvWriter = require("csv-writer").createObjectCsvWriter;

const csvWriter = createCsvWriter({
    path: "atlantaNonProfits.csv",
    header: [
        { id: "name", title: "Name" },
        { id: "mission", title: "Mission" },
        { id: "causes", title: "Causes" },
        { id: "geographicAreasServed", title: "Area" },
        { id: "programs", title: "Programs" },
        { id: "url", title: "url" },
        { id: "streetAddress", title: "streetAddress" },
        { id: "addressLocality", title: "addressLocality" },
        { id: "addressRegion", title: "addressRegion" },
        { id: "addressCountry", title: "addressCountry" },
        { id: "postalCode", title: "postalCode" },
        { id: "taxId", title: "taxId" },
        { id: "telephone", title: "Telephone" },
    ],
});

const directory = `https://greatnonprofits.org`;

const getCheerioObject = async (link) => {
    const pageHtml = (await axios.get(link)).data;
    return cheerio.load(pageHtml);
};

const getPageCount = async () => {
    const $ = await getCheerioObject(`${directory}/city/atlanta/GA`);
    return parseInt($(".pagination li:nth-last-child(2)").text());
};

const getAllNonProfits = async () => {
    const nonprofitArry = [];
    const totalPages = await getPageCount();
    let currentPage = 1;

    for (currentPage; currentPage < totalPages; currentPage++) {
        const $ = await getCheerioObject(
            `${directory}/city/atlanta/GA/sort:last_reviewed/direction:desc/page:${currentPage}`
        );
        $(".gnp-searchResult-infoMajor h2 > a").each((index, el) => {
            const link = el.attribs.href;
            if (!nonprofitArry.includes(link)) {
                nonprofitArry.push(link);
            }
        });
    }

    return nonprofitArry;
};

const getNonProfitInfo = async (link) => {
    const $ = await getCheerioObject(link);
    const infoObj = {
        name: "",
        causes: [],
        mission: "",
        geographicAreasServed: "",
        pageViews: 0,
        programs: "",
        contact: {},
        url: [],
    };

    $(".causes > a").each((id, element) => {
        infoObj.causes.push(element.children[0].data);
    });

    infoObj.mission = $('p:contains("Mission")').text();
    infoObj.pageViews = parseInt($(".gnp-pageviews:first").text());
    infoObj.beneficiariesPerYear = $(
        'p:contains("Direct beneficiaries per year")'
    ).text();
    infoObj.geographicAreasServed = $(
        'p:contains("Geographic areas served")'
    ).text();
    infoObj.programs = $("p:contains(Programs)").text();
    infoObj.name = $('h1[itemprop="name"]').text();
    infoObj.taxId = $('span[itemprop="taxID"]').text();
    infoObj.streetAddress = $('span[itemprop="streetAddress"]').text();
    infoObj.addressLocality = $('span[itemprop="addressLocality"]').text();
    infoObj.addressRegion = $('span[itemprop="addressRegion"]')
        .text()
        .replace(/^\s+|\s+$/gm, "");
    infoObj.postalCode = $('span[itemprop="postalCode"]').text();
    infoObj.addressCountry = $('span[itemprop="addressCountry"]').text();
    $('a[itemprop="url"]').each((id, el) => {
        const link = el.attribs.href;
        infoObj.url.push(link);
    });
    infoObj.telephone = $('span[itemprop="telephone"]').text();

    return infoObj;
};

const main = async () => {
    const nonprofitArry = await getAllNonProfits();
    setTimeout(() => {
        console.log("wait a sec");
    }, 5000);
    for (let i = 0; i < nonprofitArry.length; i++) {
        setTimeout(async () => {
            const infoObj = await getNonProfitInfo(
                directory + nonprofitArry[i]
            );
            await csvWriter.writeRecords([infoObj]);
            console.log(`Got ${i}: ${infoObj.name}`);
        }, i * 100);
    }
};

main();
