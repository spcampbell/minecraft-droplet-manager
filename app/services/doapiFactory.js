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

(function() {
    var doapiFactory = function($http,$timeout,$q) {
    
        var factory = {};
        
        factory.doGet = function(dothis) {
            return $http.get('do_get.php?dothis=' + dothis);
        };
       
        factory.doCheckActive = function(status) {
            
            // TODO
            // ----
            // Need to set up a count so that recursion doesn't go on forever
            // if problem occurs. Would throw error after 2 minutes or something...
            
            var deferred = $q.defer();
            
            console.log("--- Checking for status: " + status);
            
            function recurse(status){
                $http.get('do_get.php?dothis=getserverinfo')
                    .then(function(response) {
                        if(response.data.status != status) {
                            console.log("------ Status is:", response.data.status);
                            $timeout(function(){
                                recurse(status);
                            },5000);
                        } else {
                            console.log("------ Status is:", response.data.status);
                            deferred.resolve({ message: "Done" });
                        }
                    });
            };
            
            recurse(status);
            
            return deferred.promise;
        };

        factory.doCheckSnap = function(goal) {
            
            // TODO
            // ----
            // Need to set up a count so that recursion doesn't go on forever
            // if problem occurs. Would throw error after 2 minutes or something...
            
            var deferred = $q.defer();
            
            console.log("--- Checking snapshot " + goal);
            
            function recurse(goal){
                if (goal == 'deleted') snap_goal = 'false';
                if (goal == 'created') snap_goal = 'true';
                
                $http.get('do_get.php?dothis=snapshotexists')
                    .then(function(response) {
                        if(response.data.exists != snap_goal) {
                            console.log("------ Still not " + goal);
                            $timeout(function(){
                                recurse(goal);
                            },5000);
                        } else {
                            console.log("------ Snapshot " + goal);
                            deferred.resolve({ message: "Done" });
                        }
                    });
            };
            
            recurse(goal);
            
            return deferred.promise;
        };

        factory.doDelay = function(seconds) {
            var deferred = $q.defer();
            console.log("--- Pausing for " + seconds + " seconds");
            $timeout(function(){
                deferred.resolve();
            },seconds * 1000);            
            return deferred.promise;
        };
        
        return factory;
    };
    
    doapiFactory.$inject = ['$http','$timeout','$q'];
        
    angular.module('domcmgrApp').factory('doapiFactory', doapiFactory);
                                           
}());