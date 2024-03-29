
# redojo
**re**mote **do**wnload **jo**bs

## What it does
1) Add (or remove) download jobs to the server via the simple web interface
2) Let the client run periodically to retrieve download jobs and execute them locally, effectively "syncing" the local download directory with the remote download jobs list

```
    +------+                         +--------------+
    |      |                         ||            ||
    +------+  Add download jobs      ||            ||
    |      |  using the web-UI (php) ||Any computer||
    |      | <---------------------+ ||            ||
    |      |                         ||            ||
    |redojo|                         +---+------+---+
    |server|                             |      |
    |      |                         +---+      +---+
    +------+                         +--------------+

    +    ^
    |    |  Request download jobs   +--------------+
    |    +------------------------+ ||            ||
    |                               ||   running  ||
    |                               ||   redojo   ||
    | Retrieve download jobs        ||   client   ||
    | and run them locally          ||            ||
    +-----------------------------> +---+------+---+
                                        |      |
                                    +---+      +---+
                                    +--------------+

```
> Thank you, asciiflow.com


## The web interface
![image](https://user-images.githubusercontent.com/9215743/65347168-bd7fc200-dbde-11e9-898a-d8f0702ef92a.png)


## What problem does it solve?
I own a nice little linux-based media center (of which there exist plenty different ones), but the internet connection at my place is so slow that I can rarely stream anything. To solve this problem, I placed a simple text file containing URLs to video files I wanted to watch on my server.  
Then I wrote a shell script that ran on my media center once an hour (via cron), fetched this text file from my server and downloaded all the files listed within to a local directory (if they didn't already exist). This way, I only had to update the contents of this text file with new URLs from remote and the script would take care of the download *on my media center*.

This worked just fine, but obviously lacked some comfort. So I decided to develop this little tool that gives you a nice web frontent to manage download jobs and also improved the "client" script a lot. It now also deletes old downloaded files that don't have related download job on the server anymore.

Maybe there are people having a similar problem to solve, maybe there's even a different use to this tool I didn't think of, yet. In the end it can be used for any files - the scenario isn't limited to the one explained above.

## Requirements
- A web server that understands **PHP** (to serve the web UI)
- Anything that can run a **bash script** (that's where the downloads go!)  
*That's it, really.*


## Installation and Configuration
1. Copy the contents of the `server` folder to your web server
2. Copy the contents of the `client` folder to the target system that's supposed to download the files
3. Edit the `redojo.config.template` by adding the path to your target download directory and your server's URL etc. and **rename it** to `redojo.config`
4. Set up a cron job to run the `redojo.sh` script periodically (*optional, but recommended - you could also run it manually, but why should you do that?*)

**Tipp:** Look into the server script's `CSS` file! You can change some properties like the main color etc.


## Security
It may be a good idea to secure the server path exposing the web frontent (the `php` stuff) with a password. Otherwise, everyone (*who knows those files are there*) could see your planned downloads or even add new ones - of course you don't want that.  
  
You can just use basic HTTP Auth (easy to set up in both **Apache** and **Nginx**) and add the credentials to the "client" scripts config (`redojo.config`). The script will use them to fetch the list of download jobs from your server (but not for the actual downloads, of course).


## Troubleshooting
- Make sure the `php`-script on your web server has permission to write to the directory it is in (the user running you web server should be the owner of the directory and everything within it)!
- Also, on the client machine, the script must be able to write to the target downloads directory, of course
- tbc


## Contribution
I see many aspects of this project that could be improved. Also, there are a lot of possible additional features that could be implemented. I designed a lot of this to be easy to extend (e.g. separate files for every download job on the server instead of just one big list of `title`:`url` pairs - so these jobs could have more than those two properties!).  
If you want to add to this little project, feel free to do so (issues, forks and PRs welcome!).


## Attributions
- The web frontend uses icons from [iconmonstr.com](https://iconmonstr.com/), so thank's for that!
- The same applies to the font used: It's **ABeeZee** ([OFL](http://scripts.sil.org/OFL)) by Anja Meiners, thank you!