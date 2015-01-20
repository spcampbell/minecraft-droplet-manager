Minecraft Droplet Manager
-------------------------

[AngularJS](http://angularjs.org/) + PHP powered single click control of a minecraft server droplet hosted on Digital Ocean.

Thanks to [Bootstrap](http://getbootstrap.com/), it's responsive too.

Starts it up when ready to play and tears it down when done. Automatically creates a latest snapshot before destroying droplet.

Let the people play without needing your help and costing you a fortune.

### Browser Compatibility

I am not able to test this in very many browsers. I can verify it works in Safari 8, Internet Explorer 11, and Chrome 39 (desktop and mobile). If you have success or errors in other browsers, please let me know by submitting in the wiki.

### Setup

#### 1. Host this script somewhere and set up your Minecraft droplet.

You'll need a place to host the files that is not on the minecraft server you are controlling. The web host will need to have PHP available.

Before configuring this script, set up your Minecraft server at Digital Ocean. There are several options for this and Google is your friend.

My personal favorite is Mineos: [http://minecraft.codeemo.com](http://minecraft.codeemo.com). To use this, after spinning up your droplet, follow the appropriate [install steps for your distribution](http://minecraft.codeemo.com/mineoswiki/index.php?title=Main_Page).

DO NOT FORGET TO CONFIGURE YOUR FIREWALL FOR SECURITY!
- [Mineos iptables instructions here](http://minecraft.codeemo.com/mineoswiki/index.php?title=Iptables)
- [Digital Ocean article for Ubuntu firewall](https://www.digitalocean.com/community/tutorials/additional-recommended-steps-for-new-ubuntu-14-04-servers)

#### 2. Configure this script

- Set the password for this script in `index.php`

- Set the droplet details in `config.php`. Make sure and set the droplet name to match the name you give your droplet on Digital Ocean. Note that the snapshot name that is created is appended with `-snap`. Don't include `-snap` in the name you put in `config.php`.

> TIP: If you have multiple people who want to control their own droplet,
> just make a folder for each on your web host. Put a copy of the files into
> each folder and they can visit with http://yourhost.com/player1/ etc.
> Then make the `config.php` and `index.php` unique to that person/droplet.
> Note that you'll need a uniquely named droplet for each person.

#### 3. Try it out

- Got to digitalocean.com and tart your Minecraft droplet. Make sure the name you give it matches what you put in `config.php`

- Browse to `index.php` on your host and log in.

- You should see a status of "active" and the ip address information. If you do not, something is not correct.

- View your browser's javascript/error console and click the archive button. The console will show you what is going on. Note that it may take 3-5 minutes for this to complete. Be patient.

- If all goes well, the droplet will be gone and a new snapshot will be created.

- Now try to bring it back up while watching the console.

#### 4. Now go play!

You may be wondering what the "admin console" link is all about. This was specifically for mineos. You could use it to link to whatever you are using to manage the server. I may add a config to hide it in the future for those that don't need it.

### TODO

This is a work in progress. I still need to add some robust error handling. That is coming soon.

### Credit

This was inspired by the work of S-rc-C-d-

http://hi.srccd.com/post/hosting-minecraft-on-digitalocean

I recommend everyone visit his page and read over his excellent write up. He has some great scripts in there for setting up the Minecraft server yourself.

Thank you for your work and for sharing it with us!

### License

The MIT License (MIT)

Copyright (c) 2014

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
