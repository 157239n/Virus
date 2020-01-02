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
- Apache webserver
- Nginx webserver
- Mysql
- Docker (great source is "Docker deep dive" book)
- Docker compose

