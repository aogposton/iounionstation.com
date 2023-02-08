const axios = require("axios");
const https = require("https");
const cheerio = require("cheerio");
const fs = require("fs");
const { parse } = require("csv-parse");
const $ = require("jquery");
const createCsvWriter = require("csv-writer").createObjectCsvWriter;
axios.defaults.timeout = 360000;
axios.defaults.httpsAgent = new https.Agent({ keepAlive: true });

module.exports.hello = function () {
    return Date();
};

module.exports.getCheerioObject = async (link) => {
    const pageHtml = (await axios.get(link)).data;
    return cheerio.load(pageHtml);
};

module.exports.writeToCsv = async (name, objectArray) => {
    const csvWriter = createCsvWriter({
        path: `${name}.csv`,
        // header: [
        //     //     { id: "name", title: "Name" },
        //     //     { id: "mission", title: "Mission" },
        //     //     { id: "causes", title: "Causes" },
        //     //     { id: "geographicAreasServed", title: "Area" },
        //     //     { id: "programs", title: "Programs" },
        //     //     { id: "url", title: "url" },
        //     //     { id: "streetAddress", title: "streetAddress" },
        //     //     { id: "addressLocality", title: "addressLocality" },
        //     //     { id: "addressRegion", title: "addressRegion" },
        //     //     { id: "addressCountry", title: "addressCountry" },
        //     //     { id: "postalCode", title: "postalCode" },
        //     //     { id: "taxId", title: "taxId" },
        //     //     { id: "telephone", title: "Telephone" },
        // ],
    });
    for (let i = 0; i < objectArray.length; i++) {
        await csvWriter.writeRecords([objectArray[i]]);
    }
};
