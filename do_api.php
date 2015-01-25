<?php
/* 

Minecraft Droplet Manager

http://gnonai.github.io/minecraft-droplet-manager/

Single click control of a minecraft server droplet
hosted on Digital Ocean. Starts it up when ready to play
and tears it down when done. Automatically creates a latest
snapshot before destroying droplet.

Let the kids play without needing your help and costing you a fortune.

Go here for more information: http://github.com/gnonai/minecraft-droplet-manager

Released under MIT License - 2015 - gnonai

Inspired by the work of S-rc-C-d-
http://hi.srccd.com/post/hosting-minecraft-on-digitalocean

*/

class DOAPI {
    
    var $IDandAPI;
    var $dropletname;
    var $dropletsize;
    var $dropletlocation;
    var $minecraftport;
    var $baseURL = "https://api.digitalocean.com/v1/";
    
    function __construct($doClientID,$doApi,$dropletname,$dropletsize,$dropletlocation,$minecraftport) {
        
        $this->IDandAPI = "client_id=".$doClientID."&api_key=".$doApi;
        $this->dropletname = $dropletname;
        $this->dropletsize = $dropletsize;
        $this->dropletlocation = $dropletlocation;
        $this->minecraftport = $minecraftport;        
    }
    
    function digiOceanCall($target,$ID,$action,$details) {
        /*
        possible $targets: ["droplets","images","events"]
        possible $actions (for droplets): ["","new","reboot","shutdown","power_on","snapshot","destroy"]
        $ID is droplet id, image id, or event id depending on intended target
        $details are used for droplet snapshot/restore.
            droplet snapshot: $details = snapshot_name
            droplet restore: $details = image_id
            droplet new: $details = name,size_slug,image_id,region_slug
        */
        if ($target == "droplets" and $action == "new") {
            $imageID = $this->getImageID();
            $details = "name=" . $this->dropletname . "&image_id=" . $imageID;
            $details .=  "&size_slug=" . $this->dropletsize . "&region_slug=" . $this->dropletlocation . "&";
        }
        
        if ($details != "") {
            if ($action == "snapshot") {
                $details = "name=" . $details . "&";
            } elseif ($action == "restore") {
                $details = "image_id=" . $details . "&";
            }
        }
        
        if ($ID != "")
            $ID .= "/";
        
        if ($action != "")
            $action .= "/";

            
        if ($target == "images" and $ID == "")
                $details = "filter=my_images&";
            
        //build URL
        $url = $this->baseURL . $target . "/" . $ID . $action . "?" . $details . $this->IDandAPI; 
        return file_get_contents($url);        
    }
    
    function getImageID() {
        // get image id for $this->dropletname."-snap"
        $target = "images";
        $ID = "";
        $action = "";
        $details = "";
        $json = $this->digiOceanCall($target,$ID,$action,$details);
        $imagelist = json_decode($json);
        foreach ($imagelist->images as $imagedata) {
            if ($imagedata->name == $this->dropletname."-snap") {
                return $imagedata->id;
            }
        }      
        return 0;
    }
    
    function getDropletID() {
        // get image id for $this->dropletname."-snap"
        $target = "droplets";
        $ID = "";
        $action = "";
        $details = "";
        $json = $this->digiOceanCall($target,$ID,$action,$details);
        $dropletlist = json_decode($json);
        foreach ($dropletlist->droplets as $dropletdata) {
            if ($dropletdata->name == $this->dropletname) {
                return $dropletdata->id;
            }
        }
        return 0;    
    }
    
    function getServerInfo() {
        $target = "droplets";
        $ID = "";
        $action = "";
        $details = "";  
        $json = $this->digiOceanCall($target,$ID,$action,$details);
        $dropletlist = json_decode($json);
        foreach ($dropletlist->droplets as $dropletdata) {
            if ($dropletdata->name == $this->dropletname) {
                $response = array("exists" => "true",
                                  "name" => $this->dropletname,
                                  "status" => $dropletdata->status,
                                  "ip" => $dropletdata->ip_address,
                                  "port" => $this->minecraftport);
                return json_encode($response);
            }
        }
        $response = array("exists" => "false",
                          "name" => $this->dropletname,
                          "status" => "archived",
                          "ip" => "--",
                          "port" => "--");
        return json_encode($response);
    }
    
    function createServer() {
        $target = "droplets";
        $ID = "";
        $action = "new";
        $details = "";  
        $json = $this->digiOceanCall($target,$ID,$action,$details);
        $dropletdata = json_decode($json);
        $response = array("status" => $dropletdata->status,
                          "event_id" => $dropletdata->droplet->event_id);
        return json_encode($response);
    }
    
    function powerOnServer() {
        $target = "droplets";
        $ID = $this->getDropletID();
        $action = "power_on";
        $details = "";  
        $json = $this->digiOceanCall($target,$ID,$action,$details);
        return $json;        
    }
 
    function powerOffServer() {
        $target = "droplets";
        $ID = $this->getDropletID();
        $action = "power_off";
        $details = "";  
        $json = $this->digiOceanCall($target,$ID,$action,$details);       
        return $json;        
    }
    
    function shutdownServer() {
        $target = "droplets";
        $ID = $this->getDropletID();
        $action = "shutdown";
        $details = "";  
        $json = $this->digiOceanCall($target,$ID,$action,$details);      
        return $json;         
    }

    function getEventStatus($eventid) {
        $target = "events";
        $ID = $eventid;
        $action = "";
        $details = "";  
        $json = $this->digiOceanCall($target,$ID,$action,$details);
        return $json;     
    }

    function destroyServer() {
        $target = "droplets";
        $ID = $this->getDropletID();
        $action = "destroy";
        $details = "";  
        $json = $this->digiOceanCall($target,$ID,$action,$details);
        return $json;     
    }

    function snapshotServer() {
        $target = "droplets";
        $ID = $this->getDropletID();
        $action = "snapshot";
        $details = $this->dropletname."-snap";  
        $json = $this->digiOceanCall($target,$ID,$action,$details);
        return $json;     
    }
    
    function deleteSnapshot() {
        $target = "images";
        $ID = $this->getImageID();
        $action = "destroy";
        $details = "";  
        $json = $this->digiOceanCall($target,$ID,$action,$details);
        return $json;     
    }
    
    function snapshotExists() {
        $target = "images";
        $ID = $this->getImageID();
        if ($ID) {
            $response = array("exists" => "true");
            return json_encode($response);   
        } else {
            $response = array("exists" => "false");
            return json_encode($response);               
        }
    }
}

?>