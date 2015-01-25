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

(function() {
    
    var DomcmgrController = function ($scope,$http,doapiFactory,usSpinnerService) {
        
        $scope.output = [];
        
        function init() {
            
            doapiFactory.doGet('getserverinfo')
                .success(function(response) {
                    $scope.servername = response.name;
                    $scope.serverstatus = response.status;
                    $scope.serverip = response.ip;
                    $scope.serverport = response.port;
                    $scope.serveroutput = false;
                    if (response.exists == "true" && response.status == "active") {
                        //server is up 
                        $scope.serverup = true;
                    } else {
                        // server is down
                        $scope.serverup = false;
                        if (response.status == 'new'){
                            $scope.serveroutput = true;
                            $scope.output = "Server is starting..."
                        }
                    }      
                })
                .error( function(error) { 
                    console.log(error);
                }); 
        };                     

        $scope.startUp = function() {            
            $scope.output = [];
            $scope.serveroutput = true;
            $scope.serverstatus = "Starting...";
            usSpinnerService.spin('spinner-1');
            
            doapiFactory.doGet("getserverinfo")
                .then(function(response) {
                    if (response.data.exists == 'false') {
                        console.log("Creating server");
                        $scope.output.push("Creating server...");
                        $scope.output.push("(Please wait. This takes approx 1 minute.)");
                        return doapiFactory.doGet("createserver");
                    } else if (response.data.status == 'off') {
                        console.log("Server exists, power on");
                        $scope.output.push("Powering on server...");
                        return doapiFactory.doGet("poweron");
                    } else {
                        console.log("it's already up");
                        return doapiFactory.doCheckActive('active');
                    }
                })
                .then(function(response) {
                    return doapiFactory.doCheckActive('active');
                })
                .then(function(response) {
                    console.log("Start up is " + response.message);
                    $scope.output.push("DONE!");
                    usSpinnerService.stop('spinner-1');
                    init();
                });        
        };
        
        $scope.archive = function() {            
            $scope.output = [];
            $scope.serverup = false;
            $scope.serveroutput = true;
            $scope.serverstatus = "Archiving..."
            $scope.output.push("!DO NOT CLOSE THIS WINDOW UNTIL DONE!");
            usSpinnerService.spin('spinner-1');
            
            doapiFactory.doGet("getserverinfo")
                .then(function(response) {
                     console.log('1 - Shut down server if up');
                    if (response.data.status == 'active') {
                        $scope.output.push("Shutting down server...");
                        return doapiFactory.doGet("shutdown");
 
                    } else {
                        return doapiFactory.doCheckActive('off');       
                    } 
                })
                .then(function(response) {
                    console.log('2 - Wait for shutdown');
                    return doapiFactory.doCheckActive('off');
                })
                .then(function(response) {
                    console.log('3 - Old snapshot exist?');
                    return doapiFactory.doGet('snapshotexists');
                })
                .then(function(response) {
                    console.log('4 - Delete old snapshot if exists');
                    if (response.data.exists == 'true') {
                        $scope.output.push("Deleting old snapshot...");
                        return doapiFactory.doGet("deletesnapshot");
                    }
                }) 
                .then(function(response) {
                    console.log('5 - Wait for delete');
                    return doapiFactory.doCheckSnap('deleted');
                })
                .then(function(response) {
                    console.log('6 - Take snapshot');
                    $scope.output.push("Taking new snapshot...(approx 2-3 min)");
                    return doapiFactory.doGet("snapshot");
                })
                .then(function(response) {
                    console.log('7 - Wait for snapshot');
                    return doapiFactory.doCheckSnap('created');
                })
                .then(function(response) {
                    console.log('8 - Wait for 10 more seconds');
                    return doapiFactory.doDelay(10); 
                })
                .then(function(response) {
                    console.log('9 - Get server info/status');
                    return doapiFactory.doGet('getserverinfo');
                })
                .then(function(response) {
                    console.log('10 - Power off if running');
                    if (response.data.status == 'active') {
                        $scope.output.push("Powering off server...(approx 1-2 min)");
                        return doapiFactory.doGet("poweroff");
                    } else {
                        return doapiFactory.doCheckActive('off'); 
                    }                   
                })
                .then(function(response) {
                    console.log('11 - Wait for power off');
                    return doapiFactory.doCheckActive('off'); 
                })
                .then(function(response) {
                    console.log('12 - Wait for 10 more seconds');
                    return doapiFactory.doDelay(10); 
                })
                .then(function(response) {
                    console.log('13 - Destroy droplet');
                    $scope.output.push("Destroying server droplet...(approx 1-2 min)");
                    return doapiFactory.doGet("destroyserver");  
                })
                .then(function(response) {
                    console.log('14 - Wait for destory to complete');
                    return doapiFactory.doCheckActive('archived');
                })
                .then(function(response) {
                    console.log("Archive is " + response.message);
                    $scope.output.push("Archive is " + response.message);
                    usSpinnerService.stop('spinner-1');
                    init();
                });
        };
    
        init();     
    };
    
    DomcmgrController.$inject = ['$scope','$http','doapiFactory','usSpinnerService'];

    angular.module('domcmgrApp')
      .controller('DomcmgrController', DomcmgrController);

}());