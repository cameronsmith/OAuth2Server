[![Build Status](https://travis-ci.org/uk-casmith/OAuth2Server.svg?branch=add-travis-config)](https://travis-ci.org/uk-casmith/OAuth2Server)

# OAuth2 Server

A simple OAuth2 Server built without a framework using the league/oauth2 package. This repo is just to learn how to 
implement the OAuth2 package with it's example files. 

## Setup Instructions

- Composer install

- Copy the `.env.example` file to `.env` and update the keys you know with your values. 

- [Setup the server](#setup-the-server)

- [Seed test data](#seeding-test-data)

## Setup the Server

You can setup the server by running this command:

```
php ./console app:setup
```

If you already have an existing installation you can overwrite it by passing the `reinstall` argument.

## Seeding Test Data

Seed data is available for testing by calling the following command.

```
php ./console app:seed
```

**Note: You should ONLY call the seeders when not in production** 

## Starting the server

To start the server on port 8080 run:
```
php ./console app:serve
```

## Testing

For testing and/or a full working example of how everything works you can perform:

```
./vendor/bin/phpunit
```

You can also import the postman collection within the `docs` folder to find real examples.