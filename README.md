# OAuth2 Server
OAuth2 Server using the league/oauth2 package. This repo is just to learn the OAuth2 package with it's example files. 

**This application is not intended for production use.**

## Setup Instructions

- Copy the `.env.example` file to `.env` and update the keys you know with your values. 
Some keys like the `ENCRYPTION_KEY` will be setup by the `app:setup` command later on.

- To setup the server run:
```
php ./console.php app:setup
```

## Seeding Test Data

Seed data is available for testing by calling the following command.

```
php ./console app:seed
```

**You should ONLY call the seeders when not in production** 

## Starting the server

- To start the server on port 8080 run:
```
php ./console.php app:serve
```