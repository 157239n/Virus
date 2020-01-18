# Virus app

This is the server for a virus application I built. You can take this, deploy, and there will be instructions on how to install it on the target machine you want to infect. After it is infected, you can control everything through the web application.

# Setting up

The project is managed using Docker and docker-compose. Here's what you need to do:
- Pull this repo to your linux box
- Create several environment variables. The samples are in env-sample folder. You can just copy that folder into the folder env. The default "mysql" values should work fine, but you have to change the domain value inside "site" to your personal domain.
- Create docker-compose.yml file
  - Option 1: Put an SSL stripping reverse proxy container exposed to the internet, then redirect it to the main application container. Sample config: `docker-compose.sample.yml`
  - Option 2: If you don't know what the hell am I talking about, then copy `docker-compose.sample.standalone.yml` to `docker-compose.yml`
- Run the application
  - run `docker-compose build`
  - run `docker-compose up -d`

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
  - MYSQL\_USER: username used by mysql and accessing scripts, can be anything
  - MYSQL\_PASSWORD: username used by mysql and accessing scripts, can be anything. Please do not have really, really special characters. Quotes (', " and `) are not okay, (@, %, _, ...) should be okay but I have not tested this
  - MYSQL\_DATABASE: database name used by mysql and accessing scripts. Have to be virus\_app, to be in sync with the initial mysql startup script at mysql\_startup/
  - MYSQL\_ROOT\_PASSWORD: root password, only used by mysql. You must have either this or MYSQL\_RANDOM\_ROOT\_PASSWORD=yes
- site: stores mainly data used for sites, meaning PhpMyAdmin and the application. The domains don't have to be different from each other. They can all either be http:// or https://, but they must include that bit in the URL. In fact, they can all be the same, and the application will still work. But if you value security and other things, then you can customize.
  - DOMAIN: main domain where your users will see
  - ALT\_DOMAIN: main domain where your viruses will see
  - ALT\_SECURE\_DOMAIN: main domain where your viruses will see, used in places where there is sensitive data involved
  - MYSQL\_HOST: mysql container host name, specified in (and must be in sync with) the docker-compose.yml file
