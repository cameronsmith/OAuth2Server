[![Build Status](https://travis-ci.org/uk-casmith/OAuth2Server.svg?branch=add-travis-config)](https://travis-ci.org/uk-casmith/OAuth2Server)

# OAuth2 Server

A simple OAuth2 Server built without a framework using the league/oauth2 package. This repo is just to learn how to 
implement the OAuth2 package. 

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

## Which grant type should I use?

You can test out the oauth2 server using the postman collection, and choose what grant type 
you want to use:

### Password Grant

The password grant is to be used on a first party client. That is an application that is trusted enough to handle the 
end user's username and password. For example Microsoft's Xbox app is owned and developed by Microsoft so it's a first 
party app.

```
{
	"grant_type": "password",
	"client_id": 1,
	"client_secret": "secret1!",
	"username": "user1",
	"password": "Password1!"
}
```

### Refresh Grant

When a refresh token is available instead of authenticating again you can just use your refresh token and your client 
credentials to reauthorize.

**Note:** Not all grants provide refresh tokens for security reasons.

```
{
	"grant_type": "refresh_token",
	"client_id": 1,
	"client_secret": "secret1!",
	"refresh_token": "def50200af79de469430525a5e7690...."
}
```

### Client Credentials Grant

The client credentials grant is mostly used in M2M (machine to machine) communication. Meaning that there is no user 
accounts to authenticate against just the client.

```
{
    "grant_type": "client_credentials",
    "client_id": 1,
    "client_secret": "secret1!",
    "scope": "email"
}
```

### Implicit Grant

The implicit grant is where the client is a user-agent that runs a scripting language like javascript and the backend 
server does not participate in the interaction.

The user logs into the provider and is presented with the consent dialog. Once confirmed the user is redirected with the 
access token.

**Note:** The consent dialog is not provided within this package. You'll have to create one and protect the implicit 
grant route to only authenticated users.

### Auth Code Grant

#### Step 1.
The client will redirect the user to the authorization server with a get request

```
localhost:8080/auth-code?response_type=code&client_id=1&redirect_uri=/&scope=email
```

The user will then be asked to login to the authorization server and approve the client.

**Note:** The authentication dialog is not provided within this package. You'll have to create one and protect the 
redirect route to only authenticated users.

#### Step 2.
If approved the user will be redirected from the authorization server to the client with the `code`.
 
The backend server will then send a POST request with the code and the client credentials to request the token.

```
{
	"grant_type": "authorization_code",
	"client_id": "1",
	"client_secret": "secret1!",
	"redirect_uri": "/",
	"code": "def50200cc980a420d9e66059fec37"
}
```