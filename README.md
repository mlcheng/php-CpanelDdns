# php-CpanelDdns

This is a small PHP library that can help you manage your own custom ddns. I used to rely on something like dyndns.org to connect back to my home computer, but that service became paid a few years ago. Not wanting to rely on another "free" service for this, I created cpanel_ddns.

PS: This requires you have cPanel :)

## Usage
Usage is quite simple, but I'm not quite sure if it'll 100% work on every cPanel (explained later). All you need to do is

```php
require("CpanelDdns.php");
```

Then, create the `CpanelDdns` object

```php
$cpanel = new CpanelDdns();
```

Then, just set the cPanel URL, username, and password. This is to login to your cPanel.

```php
$cpanel->setUrl("https://yourhost.com:2083");
$cpanel->setUser("username");
$cpanel->setPass("password123");
```

By the way, method chaining is available, so `$cpanel->setUrl(...)->setUser(...)->setPass(...)` can be done if you so desire.

Finally, just update the ddns

```php
$cpanel->updateDdns("vnc", "yourdomain.com");
```

You should specify your subdomain and the domain you want to use to connect back home (or wherever). Now you can use `vnc.yourdomain.com` to connect to your VNC server! Or whatever you want.

### Will this 100% work?
I am not sure if this will work on *all* cPanels because of one small thing. There's a parameter in the jsonapi that specifies `line=xx`. I'm not sure if this affects anything, but if this library doesn't work for you, try to change the `line` to something else. This can be found in the `$param` variable in of the `updateDdns()` function.

But I have been using this code for a long time, so it most likely should work for you too.
