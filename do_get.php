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

session_start();

$hma = isset($_SESSION['hmauthenticated']) ? $_SESSION['hmauthenticated'] : false;
$whatlevel = isset($_SESSION['hmauthlvl']) ? $_SESSION['hmauthlvl'] : '';

if ($hma !== true || $whatlevel != "A") header( 'Location: index.php?logout=yes' );
if ($whatlevel != "A") header( 'Location: index.php?logout=yes' );

require 'do_api.php';
require 'config.php';

$todo = "";
$eventid = "";
if (isset($_GET['dothis'])) $todo = $_GET['dothis'];
if (isset($_GET['eventid'])) $eventid = $_GET['eventid'];

$doapi = new DOAPI($doClientID,$doApi,$dropletname,$dropletsize,$dropletlocation,$minecraftport);

switch($todo) {
    case "getserverinfo":    
                sendResponse($doapi->getServerInfo());
                break;
    case "createserver":
                sendResponse($doapi->createServer());
                break;
    case "poweron":
                sendResponse($doapi->powerOnServer());
                break;
    case "poweroff":
                sendResponse($doapi->powerOffServer());
                break;
    case "shutdown":
                sendResponse($doapi->shutdownServer());
                break;
    case "eventstatus":
                sendResponse($doapi->getEventStatus($eventid));
                break;
    case "destroyserver":
                sendResponse($doapi->destroyServer());
                break;
    case "snapshot":
                sendResponse($doapi->snapshotServer());
                break;
    case "deletesnapshot":
                sendResponse($doapi->deleteSnapshot());
                break;
    case "snapshotexists": 
                sendResponse($doapi->snapshotExists());
                break;
    default:
                sendResponse(json_encode('{"response":"error"}'));
}

function sendResponse($json){
    header('Content-Type: application/json');
    echo $json;
}

?>
