<?php
$username = "accropvp";
$serverFile = file_get_contents("server.json");
$servers = json_decode($serverFile, true);

$action = $_GET['action'] ?? null;
$selectedServer = $_GET['server'] ?? null;
$server = $servers[$selectedServer] ?? null;
$sessionName = $server["tmux session"] ?? null;

function start_minecraft_server($server ,$session){
    /* if the server is already running send -1
    if the server is has not launch successfully send 0 
    if the server has launch successfully send 1
    */
    global $username;
    if (session_check($session)){
        echo json_encode(["success" => -1]);
        return false;
    }
    if (!isset($server["path"])) {
        echo json_encode(["error"=>"server not found"]);
        return false;
    }
    $pathToServer = $server["path"];
    $createProcess = true;
    $commandProcess = true;
    try {
        $createProcess = exec("sudo -u $username /usr/bin/tmux -S /home/$username/tmux.sock new-session -d -s $session");
        $commandProcess = exec("sudo -u $username /usr/bin/tmux -S /home/$username/tmux.sock send-keys -t $session 'cd /home/$username/$pathToServer && bash start.sh' C-m");
    } catch (\Throwable $th) {
        echo json_encode(["error"=>"command failed"]);
        return false;
    }
    if ($createProcess === false) {
        echo json_encode(["error"=>"Unable to create session"]);
        return false;
    }
    if ($commandProcess === false) {
        echo json_encode(["error"=>"Unable to input command in session"]);
        return false;
    }
    echo json_encode(["success"=> +session_check($session)]);
}

function session_check($session){
    // return 0 if the session does not exit, 1 if the session exist
    global $username;
    try {
        $sessionCheck = shell_exec("sudo -u $username /usr/bin/tmux -S /home/$username/tmux.sock has-session -t $session 2>/dev/null; echo $?");
    } catch (\Throwable $th) {
        echo json_encode(["error"=>"session check command failed"]);
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
    global $username;
    if (!session_check($session)){
        echo json_encode(["success" => -1]);
        return false;
    }
    $stopProcess = true;
    $killProcess = true;
    try {
        $stopProcess = exec("sudo -u $username /usr/bin/tmux -S /home/$username/tmux.sock send-keys -t $session 'stop' C-m");
        sleep(6); // Wait for the server to shut down
        $killProcess = exec("sudo -u $username /usr/bin/tmux -S /home/$username/tmux.sock kill-session -t $session");
        } catch (\Throwable $th) {
        echo json_encode(["error"=>"command failed"]);
        return;
    }
    if ($stopProcess === false) {
        echo json_encode(["error"=>"Unable to stop session"]);
        return false;
    }
    if ($killProcess === false) {
        echo json_encode(["error"=>"Unable to kill session"]);
        return false;
    }
    if (session_check($session)) {
        echo json_encode(["success" => 0]);
    }
    else{
        echo json_encode(["success" => 1]);
    }
}

function handle_action($server, $action, $sessionName){
    if ($server == null){
        echo json_encode(["error"=>"server not found"]);
    }
    if($sessionName == null){
        echo json_encode(["error" => "No server name"]);
        return;
    }
    switch ($action) {
        case null:
            break;
        case "start":
            start_minecraft_server($server, $sessionName);
            break;
    
        case "stop":
            stop_server($sessionName);
            break;
    
        case "status":
            echo json_encode(["running"=> session_check($sessionName)]);
            break;
        default:
            echo json_encode(["error" => "Unknown action"]);
            break;
    }
}

handle_action($server, $action, $sessionName);

?>