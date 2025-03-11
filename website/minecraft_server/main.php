<?php
$server =   [
    "survival 1.21.4" => [
        "tumx_session" => "survival_1.21.4",
        "command" => "java -Xmx 16G -Xms 2G -jar /path/to/server.jar nogui"
    ],
    "forge CraftForLife 1.12.2" => [
        "tumx_session" => "forge_CraftForLife_1.12.2",
        "command" => "java -Xmx 16G -Xms 2G -jar /path/to/server.jar nogui"
    ]
];

if (isset($_POST["launch"])) {
    $minecraftServerName = $_POST["minecraft server"];
    $minecraftServer = $server[$minecraftServerName];

    // shell_exec($minecraftServer["command"]);
}
