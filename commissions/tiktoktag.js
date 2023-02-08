const puppeteer = require("puppeteer");
const fs = require("fs");
const _ = require("lodash");
const headless = false;

function delay(time) {
    return new Promise(function (resolve) {
        setTimeout(resolve, time);
    });
}

const gotoTikTok = async (page,link) => {
  await page.goto(link);
    // const tags = 
    const tags = await page.evaluate(() => {
    let elements = [];
    try {
      potential = document.querySelectorAll(".tiktok-5dmltr-DivContainer > .tiktok-q3q1i1-StyledCommonLink")
      if(potential.length>0){
        for(let i = 0; i<potential.length;i++){
          elements.push(potential[i].innerText)
        }
      }
    }catch(e){}
    return elements;
  });
  tags.forEach(tag=>{
    if(!tag.includes('@')){
      fs.writeFile('./tags.text', tag+'\n', { flag: 'a+' }, err => {});
    }
  })
  console.log(tags);
};

const getViewsAndVids = async (page,tag) => {
  await page.goto("https://www.tiktok.com/tag/"+tag);
    const views = await page.evaluate(() => {
    try {
      return document.querySelector(".tiktok-978mrk-H2ShareSubTitleThin").innerText;
    }catch(e){ }
    return ''
  });
      console.log('tag', tag)
      console.log('tag', views)
      const tagString = tag.replace(/(\r\n|\n|\r)/gm, "")+','+views+'\n';
      console.log(tagString)
    fs.writeFile('./tags.csv',tagString, { flag: 'a+' }, err => {});

    const vids = await page.evaluate(() => {
    try {
      let vids = [];
      let potential = document.querySelectorAll(".tiktok-yz6ijl-DivWrapper > a");
      if(potential.length>0){
        for(let i = 0; i<potential.length;i++){
          vids.push(potential[i].href)
        }
      }
      return vids;
    }catch(e){
    }
    return []
  });
    vids.forEach(vid=>{
      console.log('vid', vid)
      fs.writeFile('./vidsChecked.text', vid+'\n', { flag: 'a+' }, err => {});
  })
};

const checkForDuplicateTags = async () => {
  console.log('checkforduptag')
    const data = fs.readFileSync('./tags.text', 'utf8');
    const tags = data.split('\n');

    const data2 = fs.readFileSync('./tagsChecked.text', 'utf8');
    const tagsChecked = data2.split('\n');
    const totalTags = tagsChecked.concat(tags);
    const uniq = [...new Set(totalTags)].join('\n');
    fs.writeFile('./tagsChecked.text', uniq, function(err, data) { if (err) {/** check and handle err */} });
}

const checkForDuplicateViews = async () => {
  console.log('checkfordupviews')
    const data = fs.readFileSync('./tags.csv', 'utf8');
    const tags = data.split('\n');

    const uniq = [...new Set(tags)].join('\n');
    fs.writeFile('./tags.csv', uniq, function(err, data) { if (err) {/** check and handle err */} });
}

const main = async () => {
    const browser = await puppeteer.launch({ headless, args: [`--window-size=1920,1080`], devtools: true});
    const [page] = await browser.pages();
  while(true){
    await delay(2000);

    try {

        checkForDuplicateTags();
        await delay(2000);

        checkForDuplicateViews();
        await delay(2000);
        data = fs.readFileSync('./tagsChecked.text', 'utf8');
        let topTags = data.split('\n');

        await delay(2000);
        var linesExceptFirst = topTags.slice(1).join('\n');
        fs.writeFile('./tagsChecked.text', linesExceptFirst, function(err, data) { if (err) {/** check and handle err */} });
          
        await delay(2000);
        await getViewsAndVids(page,topTags[0].replace('#',''));

    } catch (err) {
      console.log(err);
    }
  }
};

main();
