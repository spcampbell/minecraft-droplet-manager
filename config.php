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
/* Configure these variables or set the relevant
   environment variables
*/
$environmentVariableSettings = array(
  'doClientID' => 'DO_CLIENT_ID',
  'doApi' => 'DO_API'
);

// Digital Ocean v1 API client ID and API key
// Environment variable DO_CLIENT_ID
$doClientID="";

// Environment variable DO_API
$doApi="";

// Droplet details
$dropletname = "steve";
$dropletsize = "2gb";
$dropletlocation = "london";

// Port you are hosting minecraft from
$minecraftport = "25565";
/* --------------------------------------------------*/

foreach ($environmentVariableSettings as $setting => $environmentVariable) {
  if (!$$setting) {
    $$setting = getenv($environmentVariable);
  }
}
