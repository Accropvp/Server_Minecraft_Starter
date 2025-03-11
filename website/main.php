<?php
$servers =   [
    "folia_1.21.4" => "/Minecraft/server_folia_1.21.4",
    "forge_CraftForLife_1.12.2" => "/Minecraft/server_forge_1.12.2_CraftForLife"
    
];

$action = $_GET['action'] ?? null;
$serverName = $_GET['server'] ?? null;

function start_minecraft_server($servers ,$session){
    /* if the server is already running send -1
    if the server is has not launch successfully send 0 
    if the server has launch successfully send 1
    */
    if (session_check($session)){
        echo json_encode(["success" => -1]);
        return false;
    }
    if (!isset($servers[$session])) {
        echo json_encode(["error"=>"server not found"]);
        return false;
    }
    $pathToServer = $servers[$session];
    try {
        shell_exec("tmux new-session -d -s $session $pathToServer && bash start.sh");
    } catch (\Throwable $th) {
        echo json_encode(["error"=>"command failed"]);
        return false;
    }
    echo json_encode(["success"=> +session_check($session)]);
}

function session_check($session){
    // return 0 if the session does not exit, 1 if the session exist

    try {
        $sessionCheck = shell_exec("tmux has-session -t $session 2>/dev/null; echo $?");
    } catch (\Throwable $th) {
        echo json_encode(["error"=>"command failed"]);
        return;
    }
    return (trim($sessionCheck) === "0");
}

function stop_server($session){
    /*
    return -1 if the server was not running to begin with
    return 0 if the server was running and was not stopped successfully
    return 1 if the server was running and was stopped successfully
    */
    if (!session_check($session)){
        echo json_encode(["success" => -1]);
        return false;
    }
    try {
        shell_exec("tmux send-keys -t $session 'stop' C-m");
        sleep(6); // Wait for the server to shut down
        shell_exec("tmux kill-session -t $session");
        } catch (\Throwable $th) {
        echo json_encode(["error"=>"command failed"]);
        return;
    }
    echo json_encode(["message" => session_check($session)]);
}

function handle_action($servers, $action, $serverName){
    if($serverName == null){
        return;
    }
    switch ($action) {
        case null:
            break;
        case "start":
            start_minecraft_server($servers, $serverName);
            break;
    
        case "stop":
            stop_server($serverName);
            break;
    
        case "status":
            echo json_encode(["running"=> session_check($serverName)]);
            break;
        default:
            echo json_encode(["error" => "Unknown action"]);
            break;
    }
}
handle_action($servers, $action, $serverName);

?>