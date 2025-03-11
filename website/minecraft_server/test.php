<?php
header('Content-Type: application/json');

// Define the Minecraft servers and their respective settings
$servers = [
    "survival 1.21.4" => [
        "tumx_session" => "survival_1.21.4",
        "path" => "/Minecraft/server_folia_1.21.4"
    ],
    "creative" => [
        "tmux_session" => "minecraft_creative",
        "pth" => "/path/to/creative",
    ],
    "forge CraftForLife 1.12.2" => [
        "tumx_session" => "forge_CraftForLife_1.12.2",
        "path" => "/Minecraft/server_forge_1.12.2_CraftForLife"
    ]
];

// Get the action from the request
$action = $_GET['action'] ?? null;
$server_name = $_GET['server'] ?? null;

if (!$action || !$server_name || !isset($servers[$server_name])) {
    echo json_encode(["error" => "Invalid request"]);
    exit;
}

$tmux_session = escapeshellarg($servers[$server_name]["tmux_session"]);
$server_path = escapeshellarg($servers[$server_name]["path"]);

function start_server($session, $name){
    // Check if the session is already running
    $sessionCheck = shell_exec("tmux has-session -t $session 2>/dev/null; echo $?");
    if (trim($sessionCheck) === "0") {
        echo json_encode(["message" => "$name server is already running"]);
    } else {
        shell_exec("tmux new-session -d -s $session $name && start.sh");
        echo json_encode(["message" => "Started $name server"]);
    }
}

function stop_server($session, $name){
    shell_exec("tmux send-keys -t $session 'stop' C-m");
        sleep(6); // Wait for the server to shut down
        shell_exec("tmux kill-session -t $session");
        echo json_encode(["message" => "Stopped $name server"]);
}

function session_check($session){
    $sessionCheck = shell_exec("tmux has-session -t $session 2>/dev/null; echo $?");
        echo json_encode(["running" => trim($sessionCheck) === "0"]);
}

switch ($action) {
    case "start":
        start_server($tmux_session,$server_name);
        break;

    case "stop":
        stop_server($tmux_session, $server_name);
        break;

    case "status":
        session_check($tmux_session);
        break;

    default:
        echo json_encode(["error" => "Unknown action"]);
        break;
}
?>
