function setLocalStorage(key, val) {
    var obj = {};
    obj[key] = val;
    chrome.storage.local.set(obj, function() {
        console.log('Set: '+key+'='+obj[key]);
    });
}

setLocalStorage('myNytWordleState', '');

var xhr = new XMLHttpRequest();
xhr.open('POST', 'https://wordle2.test/json/?mode=cheat', true);
xhr.onload = function () {
    var myResponseData = JSON.parse(this.responseText);
    setLocalStorage('myNytWordleState', myResponseData.words);
    console.log(myResponseData.words);
};
xhr.send(data);
