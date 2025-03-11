var startButton = document.getElementById("start_button")
var stopButton = document.getElementById("stop_button")
console.log('test');

function setupServerList(serverList){
    objServerList = JSON.parse(serverList);
    var htmlList = document.getElementById("Minecraft_Server_List");
    htmlList.innerHTML = "";
    for (let server in objServerList) {
        console.log(server);
        let readableServer = server.replace("_", " ");
        htmlList.innerHTML += `<li><a onclick="selectServer('${server}')">${readableServer}</a></li>`;
    }
}
querry("server.json", setupServerList);


function setCookie(key, value, expires=null) {
    if (expires == null) {
        document.cookie = key + "=" + value;
    }
    document.cookie = key + "=" + value + "; expires=" + expires;
}

function getCookie(key){
    var name = key + "=";
    decodedCookie = decodeURIComponent(document.cookie);
    var cookies = decodedCookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i];
        while (cookie.charAt(0) == ' ') {
            cookie = cookie.substring(1);
        }
        if (cookie.indexOf(name) == 0) {
            return cookie.substring(name.length, cookie.length);
        }
    }
    return "";
}

function scrollList() {
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById("Search_Server");
    filter = input.value.toUpperCase();
    ul = document.getElementById("Minecraft_Server_List");
    li = ul.getElementsByTagName("li");
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("a")[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}

function querry(str, func){
    var xmlhttp = new XMLHttpRequest();
    var response;
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        response = this.responseText;
        func(response);
      }
    };
    xmlhttp.open("GET", str, true);
    xmlhttp.send();
    return response;
}

function activateStartButton(){
    stopButton.className = "button";
    stopButton.disabled = true;
    startButton.disabled = false;
    startButton.className = "start_button";
}

function activateStopButton(){
    startButton.className = "button";
    startButton.disabled = true;
    stopButton.disabled = false;
    stopButton.className = "stop_button";
}

function selectServer(server) {
    setCookie("server", server);
    document.getElementById('Server_Selected').innerHTML = server;
    var func = function (response) {
        var serverData = JSON.parse(response);
        console.log(serverData);
        if (serverData["running"] == true) {
            activateStopButton();
        }
        else{
            activateStartButton();
        }
    }
    querry("main.php?action=status&server=" + server, func);
}

function testButton(){
    console.log("button activated");
}

function startServer(){
    var server = getCookie("server");
    if (!server) {
        console.error("No server selected");
        return;
    }
    var func = function (response) {
        console.log(response);
        var serverData = JSON.parse(response);
        if (serverData["error"]) {
            console.error(serverData["error"]);
            return;
        }
        switch (serverData["success"]) {
            case -1:
                console.error("server is already running");
                break;
            case 0:
                console.error("server has not started successfully");
                break;
            case 1:
                console.log("server started successfully");
                activateStopButton();
                break;
            default:
                console.error("unknown error");
                break;
        }
    }
    querry("main.php?action=start&server=" + server, func);
}

function stopServer(){
    var server = getCookie("server");
    if (!server) {
        console.error("No server selected");
        return;
    }
    var func = function (response) {
        var serverData = JSON.parse(response);
        if (serverData["error"]) {
            console.error(serverData["error"]);
            return;
        }
        switch (serverData["success"]) {
            case -1:
                console.error("server is already stopped");
                break;
            case 0:
                console.error("server has not stopped successfully");
                break;
            case 1:
                console.log("server stopped successfully");
                activateStartButton();
                break;
            default:
                console.error("unknown error");
                break;
        }
    }
    querry("main.php?action=stop&server=" + server, func);
}
document.getElementById('Server_Selected').innerHTML = getCookie("server");