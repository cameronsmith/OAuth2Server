# OAuth2 Server
OAuth2 Server using the league/oauth2 package. This repo is just to learn the OAuth2 package with it's example files. 

**This application is not intended for production use.**

## Setup Instructions

- Copy the `.env.example` file to `.env` and update the keys you know with your values. 
Some keys like the `ENCRYPTION_KEY` will be setup by the `app:setup` command later on.

- Run `php ./console.php app:setup` to setup the server.

## Starting the server

- Run `php ./console.php app:serve` to start the server on port 8080.