# Laravel Roblox Clone Setup
Let's get started! First make sure that you have the following software installed on your server:
- **Apache/Nginx Webserver**
- **PHP 7.3+**
- **MySQL 5.7+**
- **Blender 2.79** (Renderer is stuck to this version because I couldn't be bothered enough to make it work with 2.8+)

Alongside this you will also need Google Recaptcha V2 API keys in production. Go [here](https://www.google.com/recaptcha/about/) and click on "Admin Console" to create them. And a PayPal account if you would like to sell currency and membership to your users. You can create a PayPal account [here](https://www.paypal.com).

To get a jumpstart with the webserver/PHP/MySQL part, I recommend installing the LAMP/LEMP stack.

## 1. Pulling the Repository
Create a new folder where you want the project files to be stored on your server, and then run this command inside of it:
```
git clone https://github.com/FoxxoSnoot/laravel-roblox-clone.git
```

## 2. Setting up the Site
First make sure that you install [Composer](https://getcomposer.org/download/) inside of the project folder, after you did that run these commands:
```
composer install
cp .env.example .env (copy .env.example .env for Windows)
php artisan key:generate
```

Then make sure to enter your project name, provide your Recaptcha keys, email information, and database information in the `.env` file, after this be sure to make virtual hosts for your domains. You will need to them for the following services:
- **Main Website (eg: www.robloxclone.com)
- **Storage for thumbnails, uploads, etc (eg: cdn.robloxclone.com)
- **Administration Panel (eg: ap.robloxclone.com)
- **Renderer (eg: renderer.robloxclone.com)

Also be sure to specify these domains in the `config/site.php` file as well or your site won't work!

After you set up the database run the following command. This will insert all the necessary SQL tables in your database:
```
php artisan migrate
```

If you setup everything correctly your site should now be working. If you sign up, the first user will immediately gain admin panel permissions and the panel password for it will be `password`, so make sure to change this or else anybody can mess with your site if they know the admin panel url!

(Optional step) Run the following command to run the application on the PHP development server:

```
php artisan serve
```

## 3. Setting up background workers for email and automatic rerendering
Run the following command:
```
sudo apt-get install supervisor
```

After you installed supervisor, go to the `/etc/supervisor/conf.d` directory and create a configuration file with the following template:
```
[program:jobs-worker]
process_name=%(program_name)s_%(process_num)02d
command=php (PROJECT_DIRECTORY)/artisan queue:work --tries=3
autostart=true
autorestart=true
user=root
numprocs=4
redirect_stderr=true
stdout_logfile=(PROJECT_DIRECTORY)/worker.log
```

And then run the following commands:
```
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```
