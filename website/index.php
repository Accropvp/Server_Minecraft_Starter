<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>

<body>
    <h1>Accropvp's minecraft servers</h1>
    <hr>
    <input type="text" id="Search_Server" onkeyup="scrollList()" placeholder="Search for servers..">
    <ul id="Minecraft_Server_List">
    <li><a onclick="selectServer('folia_1.21.4')">folia 1.21.4</a></li>
    <li><a onclick="selectServer('forge_1.12.2_CraftForLife')">forge 1.12.2 CraftForLife</a></li>
    <li><a>Cindy</a></li>
    <li><a>Cindy</a></li>
    <li><a>Cindy</a></li>
    <li><a>Cindy</a></li>
    <li><a>Cindy</a></li>
    </ul>
    <a id="Server_Selected"></a><br>
    <button id="start_button" onclick="startServer()" class="start_button">start</button>
    <button id="stop_button" onclick="stopServer()" class="stop_button">stop</button>
    
</body>
</html>