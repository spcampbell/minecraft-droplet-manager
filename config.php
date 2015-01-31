<?php
/* 

Minecraft Droplet Manager

http://spcampbell.github.io/minecraft-droplet-manager/

Single click control of a minecraft server droplet
hosted on Digital Ocean. Starts it up when ready to play
and tears it down when done. Automatically creates a latest
snapshot before destroying droplet.

Let the kids play without needing your help and costing you a fortune.

Released under MIT License - 2015 - spcampbell

Inspired by the work of S-rc-C-d-
http://hi.srccd.com/post/hosting-minecraft-on-digitalocean

*/

/* --------------------------------------------------*/
/* Configure these variables                         */

// Digital Ocean v1 API client ID and API key
$doClientID="abc123";
$doApi="abc123";

// Droplet details
$dropletname = "minecraft";
$dropletsize = "1gb";
$dropletlocation = "nyc3";

// Port you are hosting minecraft from
$minecraftport = "25565";

/* --------------------------------------------------*/

?>