# Virus app

This is the server for a virus application I built. You can take this, deploy, and there will be instructions on how to install it on the target machine you want to infect. After it is infected, you can control everything through the web application.

# Setting up

The project is managed using Docker and docker-compose. Here's what you need to do:
- Pull this repo to your linux box
- Create several environment variables. The samples are in env-sample folder. You can just copy that folder into the folder env. The default "mysql" values should work fine, but you have to change the domain value inside "site" to your personal domain.
- Create docker-compose.yml file. I usually do everything with a nginx reverse proxy at the front, as a load balancer and as an SSL stripper. That sample config is in `docker-compose.proxy.sample.yml`. If you want to map ports directly to your docker host then the sample config is in `docker-compose.standalone.sample.yml`

After everything seems fine, deploy it using `docker-compose up -d` and everything will be built

Don't know what the hell am I talking about? Here's what to learn more before you can understand:
- Linux, especially Ubuntu (Debian)
- General networking, DNS
- Apache web server
- Nginx web server
- Mysql
- Docker (great source is "Docker deep dive" book)
- Docker compose

# Environment variables

There are these files containing the environment variables:
- mysql: stores mainly data used to create the database initially and to access it from an outside script
  - MYSQL_USER: username used by mysql and accessing scripts, can be anything
  - MYSQL_PASSWORD: username used by mysql and accessing scripts, can be anything
  - MYSQL_DATABASE: database name used by mysql and accessing scripts. Have to be virus_app, to be in sync with the initial mysql startup script at mysql_startup/. Change this only if you know what you are doing
  - MYSQL_ROOT_PASSWORD: root password, only used by mysql. You must have either this or MYSQL_RANDOM_ROOT_PASSWORD=yes
- site: stores mainly data used for sites, meaning PhpMyAdmin and the application
  - DOMAIN: main domain where your users will see. Can be either http or https
  - ALT_DOMAIN: main domain where your viruses will see. Has to be http. Doesn't have to be different from the domain above
  - ALT_SECURE_DOMAIN: main domain where your viruses will see. Has to be https
  - MYSQL_HOST: mysql container host name, specified in (and must be in sync with) the docker-compose.yml file
