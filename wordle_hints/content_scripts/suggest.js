function setLocalStorage(key, val) {
    var obj = {};
    obj[key] = val;
    chrome.storage.local.set(obj, function() {
        console.log('Set: '+key+'='+obj[key]);
    });
}

setLocalStorage('myNytWordleState', '');

var myBoard = localStorage.getItem('nyt-wordle-state');
var myBoardP = JSON.parse(myBoard);

var boardState = myBoardP.boardState;
var evaluations = myBoardP.evaluations;

var data = new FormData();
data.append('boardState', boardState);
data.append('evaluations', evaluations);

var xhr = new XMLHttpRequest();
xhr.open('POST', 'https://wordle2.test/json/', true);
xhr.onload = function () {
    var myResponseData = JSON.parse(this.responseText);
    setLocalStorage('myNytWordleState', myResponseData.words);
    console.log(myResponseData.words);
};
xhr.send(data);
