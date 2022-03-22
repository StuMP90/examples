function setLocalStorage(key, val) {
    var obj = {};
    obj[key] = val;
    chrome.storage.local.set(obj, function() {
        console.log('Set: '+key+'='+obj[key]);
    });
}

function getLocalStorage(key, callback) {
    chrome.storage.local.get(key, function(items) {
        callback(key, items[key]);
    });
}

function NytCallback(key, val) {
    document.querySelector("#wordlist").innerHTML="<p>Key: " + key + "<br />Value: " + val + "</p>";
}
setLocalStorage('myNytWordleState', '');

document.addEventListener('DOMContentLoaded', function() {
    var checkPageButton = document.getElementById('checkPage');
    checkPageButton.addEventListener('click', function() {

        chrome.tabs.query({active: true, currentWindow: true}, function(tabs) {
            var currTab = tabs[0];
            var id = currTab.id;
            if (currTab.url == "https://www.nytimes.com/games/wordle/index.html") {
                if (id) {
                    setLocalStorage('myNytWordleState', '');
                    chrome.scripting.executeScript({
                        target: {tabId: id, allFrames: true},
                        files: ['content_scripts/suggest.js'],
                    });
                }
            }
        });
        
    }, false);
    var cheatButton = document.getElementById('cheat');
    cheatButton.addEventListener('click', function() {

        chrome.tabs.query({active: true, currentWindow: true}, function(tabs) {
            var currTab = tabs[0];
            var id = currTab.id;
            if (currTab.url == "https://www.nytimes.com/games/wordle/index.html") {
                if (id) {
                    setLocalStorage('myNytWordleState', '');
                    chrome.scripting.executeScript({
                        target: {tabId: id, allFrames: true},
                        files: ['content_scripts/cheat.js'],
                    });
                }
            }
        });
        
    }, false);
    chrome.tabs.query({active: true, currentWindow: true}, function(tabs) {
        var currTab = tabs[0];
        var id = currTab.id;
        if (currTab.url == "https://www.nytimes.com/games/wordle/index.html") {
            document.querySelector("#checkPage").style.display="block";
            document.querySelector("#wrongPage").style.display="none";
        } else {
            document.querySelector("#checkPage").style.display="none";
            document.querySelector("#wrongPage").style.display="block";
        }
    });
    chrome.storage.onChanged.addListener(function (changes, namespace) {
      for (let [key, { oldValue, newValue }] of Object.entries(changes)) {
            if (key == "myNytWordleState") {
                document.querySelector("#wordlist").innerHTML="<p>" + newValue + "</p>";
            }
        }
    });
}, false);
