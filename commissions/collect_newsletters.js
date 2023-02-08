const httpScraper = require("./http-scraper");
const fs = require("fs");

const main = async () => {
    let page;
    let newsletterCount;
    let pageCount;
    let name;
    let link;
    let description;
    let newsletters = [];
    let info = {};
    let toInvestigate;

    const categories = [""];
    for (let i = 0; i < categories.length; i++) {
        page = await httpScraper.getCheerioObject(
            `https://www.datanewsletters.com/recherche?data_mainbundle_search%5Blibelle%5D=&data_mainbundle_search%5B_token%5D=4zSvO4vgM943kJUhsiklCF3PMZEvVg6eChPUU64tbX0&categorieform=${categories[i]}`
        );

        newsletterCount = await page(
            "body > div.kw-page-content > div > div > div > main > header > div.kw-md-table-row.kw-xs-small-offset > div.col-md-4"
        )
            .text()
            .match(/\d+/)[0];

        pageCount = Math.ceil(newsletterCount / 10);
        console.log("total Pages", pageCount);
        for (let j = 1; j < pageCount; j++) {
            console.log("current Pages", j);

            page = await httpScraper.getCheerioObject(
                `https://www.datanewsletters.com/recherche?data_mainbundle_search%5Blibelle%5D=&data_mainbundle_search%5B_token%5D=4zSvO4vgM943kJUhsiklCF3PMZEvVg6eChPUU64tbX0&categorieform=${categories[i]}&page=${j}`
            );
            let toInvestigate = [];

            for (let k = 1; k < 11; k++) {
                name = await page(
                    `#kw-listings-container > div:nth-child(${k}) > article > div.kw-listing-item-info > header > h3 > a`
                ).text();

                link = await page(
                    `#kw-listings-container > div:nth-child(${k}) > article > div.kw-listing-item-info > header > h3 > a`
                ).attr("href");

                description1 = await page(
                    `#kw-listings-container > div:nth-child(${k}) > article > div.kw-listing-item-info > div.kw-listing-item-description > p`
                ).text();
                toInvestigate.push({ link, description1, name });
            }
            for (let p = 0; p < toInvestigate.length; p++) {
                page = await httpScraper.getCheerioObject(
                    `https://www.datanewsletters.com${toInvestigate[p].link}`
                );

                info = {};

                keywords = [];
                category = [];
                socials = [];

                website = await page("#receive-newsletter").attr("href");
                motscles = await page(
                    page(".kw-entry-tags .tagcloud")[0]
                ).children();

                categorie = await page(
                    page(".kw-entry-tags .tagcloud")[1]
                ).children();
                for (let r = 0; r < motscles.length; r++) {
                    keywords.push(page(motscles[r].children[0]).text());
                }

                for (let s = 0; s < categorie.length; s++) {
                    category.push(page(categorie[s].children[0]).text());
                }
                description = await page(".kw-listing-item-description").text();

                for (
                    let m = 1;
                    m < (await page("#details > dl > dt").length) * 2 + 1;
                    m += 2
                ) {
                    info[
                        await page(`#details > dl > dt:nth-child(${m})`).text()
                    ] = await page(`#details > dl > dd:nth-child(${m + 1})`)
                        .text()
                        .trim();
                    if (info["Evaluation"]) delete info["Evaluation"];
                    if (info["Réseaux sociaux"]) {
                        socials = await getSocials(page);
                    }
                }

                console.log(`Pushing ${p}:`, toInvestigate[p].name);

                fs.appendFile(
                    "newsletter.text",
                    JSON.stringify({
                        link: `www.datanewsletters.com${toInvestigate[p].link}`,
                        name: toInvestigate[p].name,
                        description,
                        description2: toInvestigate[p].description1,
                        website,
                        info,
                        keywords: keywords.toString(),
                        category: category.toString(),
                        socials: socials.toString(),
                    }) + ",",
                    function (err) {
                        if (err) throw err;
                        // console.log("Saved!");
                    }
                );
            }
        }
    }
};
main();

const getSocials = async (page) => {
    let socials = [];

    const item = page(".social-service");
    for (let i = 0; i < item.length; i++) {
        socials.push(item[i].attribs.href);
    }
    console.log(socials);
    return socials;
};

