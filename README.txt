This allow you to start and stop your Minecraft server in a web interface.
This code works in Arch linux (hasn't been tested on other distros).


To start this website you will first need to have apache and php configured on your machine and tmux installed.

unzip the downloaded files on your server directory.
put your username in the $username variable inside main.php.
In server.json configure your server paths and tmux sessions (WARRNING : tmux does not accept every char for a session name, see tmux doc for more info)

You will then need to allow your http user to launch tmux as your user without password by using this command :
"sudo EDITOR=nano visudo"
then paste on the bottom of the page
"http ALL=(USER) NOPASSWD: /usr/bin/tmux"
where USER is the name of your user.

you will then need to create a file tmux.sock on your user's main directory
