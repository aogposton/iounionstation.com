const puppeteer = require("puppeteer");
const fs = require("fs");
const _ = require("lodash");
const headless = true;

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
    return 
    }catch(e){
    }
    return ''
  });
      console.log('tag', tag)

    fs.writeFile('./tags.csv', tag+','+views+'\n', { flag: 'a+' }, err => {});

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
    const data = fs.readFileSync('./tags.text', 'utf8');
    const tags = data.split('\n');
    const uniq = [...new Set(tags)].join('\n');
    fs.writeFile('./tagsChecked.text', uniq, function(err, data) { if (err) {/** check and handle err */} });
}
const checkForDuplicateVids = async () => {
    const data = fs.readFileSync('./vidsChecked.text', 'utf8');
    const tags = data.split('\n');
    const uniq = [...new Set(tags)].join('\n');
    fs.writeFile('./vidsChecked.text', uniq, function(err, data) { if (err) {/** check and handle err */} });
}


const main = async () => {
  try {
    const browser = await puppeteer.launch({ headless, args: [`--window-size=1920,1080`], devtools: true});
    await delay(2000);
    const [page] = await browser.pages();
    while(true){
      checkForDuplicateVids();
      await delay(2000);

      let data = fs.readFileSync('./vidsChecked.text', 'utf8');
      let topVideos = data.split('\n');

      while(topVideos.length>10){
        data = fs.readFileSync('./vidsChecked.text', 'utf8');
        topVideos = data.split('\n');

        var linesExceptFirst = topVideos.slice(1).join('\n');
        fs.writeFile('./vidsChecked.text', linesExceptFirst, function(err, data) { if (err) {/** check and handle err */} });

        await gotoTikTok(page,topVideos[0]);
        await delay(2000);
      }

      checkForDuplicateTags();
      await delay(2000);
      data = fs.readFileSync('./tagsChecked.text', 'utf8');
      let topTags = data.split('\n');

      while(topTags.length>10){
        data = fs.readFileSync('./tagsChecked.text', 'utf8');
        topTags = data.split('\n');

        var linesExceptFirst = topTags.slice(1).join('\n');
        fs.writeFile('./tagsChecked.text', linesExceptFirst, function(err, data) { if (err) {/** check and handle err */} });
        
        await delay(2000);
        await getViewsAndVids(page,topTags[0].replace('#',''));
      }
    }

  } catch (err) {
    console.log(err);
  }
};

main();
