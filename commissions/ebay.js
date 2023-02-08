// 1492036761
// 1713684223
// API Key: vaE3ZLAPDWSoD19GSo
// API Secret: wiQ52Ua0oIYDxqtzOoYqqztY9Zv2cnSEp0TK
// const { clientId, clientSecret } = require("./credentials");
const clientSecret = "PRD-7f1066913cc3-cfb6-4c4a-a106-8f7a";
const clientID = "AmonPost-iiTomato-PRD-b7f106691-e1025e00";

let eBay = require("ebay-node-api");

ebay = new eBay({
    clientID,
    clientSecret,
    body: {
        grant_type: "client_credentials",
        scope: "https://api.ebay.com/oauth/api_scope",
    },
});

const main = async () => {
    // await ebay.getAccessToken();
    ebay.findCompletedItems({
        keywords: 'Garmin nuvi 1300 Automotive GPS Receiver',
        categoryId: '156955',
        sortOrder: 'PricePlusShippingLowest', //https://developer.ebay.com/devzone/finding/callref/extra/fndcmpltditms.rqst.srtordr.html
        Condition: 3000,
        SoldItemsOnly: true,
        entriesPerPage: 2
    }).then((data) => {
        console.log(data);
    }, (error) => {
        console.log(error);
    });
};

main();
