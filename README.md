
# redojo
**RE**mote **DO**wnload **JO**bs


## What problem does this tool solve?

I own a nice little linux-based media center (of which there exist plenty different ones), but the internet connection at my place is so slow that I can rarely stream anything. To solve this problem, I placed a simple text file containing URLs to video files I wanted to watch on my server.  
Then I wrote a shell script that would run on my media center once an hour (via cron), fetch this text file from my server and download all the files listed within to a local directory (if they don't already exist). This way, I only had to update the contents of this text file with new URLs from remote and the script would take care of the download.

This worked just fine, but obviously lacked some comfort. So I started developing this little tool that gives you a nice web frontent to add and manage download jobs and also improved the "client" script a lot.

Maybe there are people having the same problem as I did, maybe there's even a different use to this tool I didn't think of, yet. In the end it can be used for any files - the scenario isn't limited to the one explained above.

![image](https://user-images.githubusercontent.com/9215743/65347168-bd7fc200-dbde-11e9-898a-d8f0702ef92a.png)

## Requirements

- A web server that understands **PHP** (to serve the web UI)
- Anything that can run a **bash script** (that's where the downloads go!)

*That's it, really.*


## Installation and Configuration

1. Copy the contents of the `server` folder to your web server
2. Copy the contents of the `client` folder to the target system that's supposed to download the files
3. Edit the `redojo.config.template` adding the path to your target download directory and your server's URL etc. and **rename it** to `redojo.config`
4. Set up a cron job to run the `redojo.sh` script periodically (*optional - you could also run it manually, if you liked*)

**Tipp:** Look into the server script's `CSS` file! You can change some properties like the main color etc.


## Security

It may be a good idea to secure the server path exposing the web frontent (the `php` stuff) with a password. Otherwise, everyone (*who knew those files are there*) could see your planned downloads or even add new ones - of course you don't want that.  
  
You can just use basic HTTP Auth (easy to set up in both **Apache** and **Nginx**) and add your user credentials to the "client" scripts config (`redojo.config`). The script will use these credentials to fetch the list of download jobs from your server (but not for the actual downloads, of course).


## Troubleshooting

- Make sure the `php`-script on your web server has permission to write to the directory it is in!
- What else?


## Contribution

I see many aspects of this project that could be improved. Also, there are a lot of possible additional features that could be implemented. I even designed a lot of this to be easy to extend (e.g. separate files for every download job on the server instead of just one big list of `title`:`url` pairs - so these jobs could have more than those two properties!).  
If you want to add to this little project, feel free to do so (issues, forks and PRs welcome!).


## Attributions

- The web frontend uses icons from [iconmonstr.com](https://iconmonstr.com/), so thank's for that!
- The same applies to the font used: It's **ABeeZee** ([OFL](http://scripts.sil.org/OFL)) by Anja Meiners, thank you!