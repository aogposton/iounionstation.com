require('chromedriver');
const axios = require('axios');
const https = require("https");

axios.defaults.timeout = 30000;
axios.defaults.httpsAgent = new https.Agent({ keepAlive: true });

const fs = require('fs');
const { parse } = require("csv-parse");
const webdriver = require('selenium-webdriver');

let num = 1;

const main = async ()=>{
    
    fs.createReadStream("./list.csv")
    .pipe(parse({ delimiter: ",", from_line: 1 }))
    .on("data", async function (row) {
        investigate("https://www.google.com/search?q="+row[0]+' -charitynavigator -guidestar causeiq')
    })
}
const investigate = async (url)=>{
        
        if(num>0&&num<2){
            if(url&&url!='http://'){
                num +=1;

                // const puppet = await puppeteer.launch();
                // const page = await puppet.newPage();
                const browser = new webdriver.Builder()
                .forBrowser('chrome')
                .build();


                // await page.goto(url);
                await browser.get(url)
                // const res = await page.content();
                
                // const containsWordpress = res.indexOf('/wp-content/')>0||res.indexOf('https://s.w.org/')>0
                // const containsGoogleAnalytics = res.indexOf('www.google-analytics.com')>0 || res.indexOf('__gtagTracker')>0
                // const containsRSSFeed = res.indexOf('rss+xml')>0
                // const containsNewRelic = res.indexOf('newrelic.com')>0
                // const PoweredBy = res.indexOf('Powered By')>0 || res.indexOf('powered by')>0 || res.indexOf('design by')>0 || res.indexOf('Design by')>0
                // const containsFeathr = res.indexOf('feathr.co')>0
                // const containsMemberClicks = res.indexOf('MemberClicks')>0
                // const containsWIX = res.indexOf('wix/htmlEmbeds')>0
                // const containsSquareSpace = res.indexOf('squarespace.co')>0;

                // const content = `<h2>url: ${url}</h2>
                // <p>${PoweredBy?'Designer known.<br />':''} ${containsSquareSpace?'Is squarespace.<br />':''}${containsWordpress?'Is WordPress.<br />':''}${containsWIX?'Is WIX.<br />':''}${containsMemberClicks?'Is MemberClicks.<br />':''}${containsGoogleAnalytics?'Is conntected to google analytics.<br />':''}${containsFeathr?'Is conntected to Feathr.<br />':''}${containsRSSFeed?'Contains RSS Feed.<br />':''}${containsNewRelic?'Contains New Relic.<br />':''}
                // </p>
                // <hr />
                // `;
                // fs.writeFile('./report.html', content, { flag: 'a+' }, err => {});


                // await puppet.close();
            }
        }
}
main();