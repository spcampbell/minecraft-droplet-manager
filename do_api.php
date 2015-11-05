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

class DOAPI {
    
    var $doAPIv2Token;
    var $dropletname;
    var $dropletsize;
    var $dropletlocation;
    var $minecraftport;
    var $baseURL = "https://api.digitalocean.com/v2/";

    function __construct($doAPIv2Token,$dropletname,$dropletsize,$dropletlocation,$minecraftport) {
        
        $this ->doAPIv2Token = $doAPIv2Token;
        $this->dropletname = $dropletname;
        $this->dropletsize = $dropletsize;
        $this->dropletlocation = $dropletlocation;
        $this->minecraftport = $minecraftport;        
    }
    
    function digiOceanCall($requesttype,$postparam,$target,$ID) {
        
        $url = $this->baseURL . $target;

        $curl = curl_init();

        if ($ID != "") {
            $url = $url . "/" . $ID;
        }
        
        if ($requesttype == "POST") {
            if ($ID){
                $url = $url . "/actions";   
            }
            
            $postData = '';
            $postData = json_encode($postparam);
                        
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        }
        
        if ($requesttype == "DELETE") {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Authorization: Bearer ' . $this->doAPIv2Token ) );
        
        $output = curl_exec($curl); 
        
        curl_close($curl);
       
        return $output;
    }
    
    function getImageID() {
        // get image id for $this->dropletname."-snap"
        $requesttype = "GET";
        $postparam = "";
        $target = "images?private=true";
        $ID = "";

        $json = $this->digiOceanCall($requesttype,$postparam,$target,$ID);
        $imagelist = json_decode($json);
        foreach ($imagelist->images as $imagedata) {
            if ($imagedata->name == $this->dropletname."-snap") {
                return $imagedata->id;
            }
        }      
        return 0;
    }
    
    function getDropletID() {
        $requesttype = "GET";
        $postparam = "";
        $target = "droplets";
        $ID = "";

        $json = $this->digiOceanCall($requesttype,$postparam,$target,$ID);
        $dropletlist = json_decode($json);
        foreach ($dropletlist->droplets as $dropletdata) {
            if ($dropletdata->name == $this->dropletname) {
                return $dropletdata->id;
            }
        }
        return 0;    
    }
    
    function getDropletDetails($size_slug) {
        $requesttype = "GET";
        $postparam = "";
        $target = "sizes";
        $ID = "";

        $json = $this->digiOceanCall($requesttype,$postparam,$target,$ID);
        $sizelist = json_decode($json);
        foreach ($sizelist->sizes as $size) {
            if ($size->slug == $size_slug) {
                return $size;
            }
        }
        return 0;    
    }           
    
    function calcUptime($created_at) {
        date_default_timezone_set('UTC');
        $offset = 0;
        $created = strtotime($created_at);
        $now = time();
        $hours = round((($now - $created)/3600) + $offset , 2);
        return $hours;    
    }  
  
    function getServerInfo() {
        $requesttype = "GET";
        $postparam = "";
        $target = "droplets";
        $ID = "";

        $json = $this->digiOceanCall($requesttype,$postparam,$target,$ID);
        $dropletlist = json_decode($json);
        foreach ($dropletlist->droplets as $dropletdata) {
            if ($dropletdata->name == $this->dropletname) {
                $alldetails = $this->getDropletDetails($dropletdata->size_slug);
                $dropletdetails = array("name" => $alldetails->slug,
                                        "cpu" => $alldetails->vcpus,
                                        "disk" => $alldetails->disk,
                                        "cost_per_hour" => $alldetails->price_hourly);
                $response = array("exists" => "true",
                                  "name" => $this->dropletname,
                                  "status" => $dropletdata->status,
                                  "ip" => $dropletdata->networks->v4[0]->ip_address,
                                  "port" => $this->minecraftport,
                                  "created_at" => $dropletdata->created_at,
                                  "uptime" => $this->calcUptime($dropletdata->created_at),
                                  "details" => $dropletdetails);
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
        $requesttype = "POST";
        
         $postparam = array(
            "name" =>  $this->dropletname,          
            "region" => $this->dropletlocation,                
            "size" => $this->dropletsize,                 
            "image" => $this->getImageID()
        );       
                
        $target = "droplets";
        $ID = "";

        $json = $this->digiOceanCall($requesttype,$postparam,$target,$ID);

        $dropletdata = json_decode($json);
        //$response = array("status" =>  $dropletdata->droplet->status,
        //                  "actions" => $dropletdata->droplet->links->actions);
        $response = array("status" =>  $dropletdata->droplet->status);
        return json_encode($response);
    }
        
    function powerOnServer() {
        $requesttype = "POST";
        $postparam = array("type" =>  "power_on" );  
        $target = "droplets";
        $ID = $this->getDropletID();
        $json = $this->digiOceanCall($requesttype,$postparam,$target,$ID);
        return $json;        
    }
 
    function powerOffServer() {
        $requesttype = "POST";
        $postparam = array("type" =>  "power_off" );  
        $target = "droplets";
        $ID = $this->getDropletID();
        $json = $this->digiOceanCall($requesttype,$postparam,$target,$ID);
        return $json;        
    }
    
    function shutdownServer() {
        $requesttype = "POST";
        $postparam = array("type" =>  "shutdown" );  
        $target = "droplets";
        $ID = $this->getDropletID();
        $json = $this->digiOceanCall($requesttype,$postparam,$target,$ID);
        return $json;          
    }
    
    function getEventStatus($eventid) {
        $requesttype = "GET";
        $postparam = "";
        $target = "actions";
        $ID = $eventid;
        $json = $this->digiOceanCall($requesttype,$postparam,$target,$ID);
        return $json;     
    }

    function destroyServer() {
        $requesttype = "DELETE";
        $postparam = "";
        $target = "droplets";
        $ID = $this->getDropletID();
        $json = $this->digiOceanCall($requesttype,$postparam,$target,$ID);
        return $json;     
    }

    function snapshotServer() {
        $requesttype = "POST";
        $postparam = array("type" =>  "snapshot", "name" => $this->dropletname."-snap" );  
        $target = "droplets";
        $ID = $this->getDropletID();
        $json = $this->digiOceanCall($requesttype,$postparam,$target,$ID);
        return $json;     
    }
    
    function deleteSnapshot() {
        $requesttype = "DELETE";
        $postparam = "";
        $target = "images";
        $ID = $this->getImageID();
        $json = $this->digiOceanCall($requesttype,$postparam,$target,$ID);
        return $json;     
    }
    
    function snapshotExists() {
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