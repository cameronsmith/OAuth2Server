# oauth2
OAuth2 Server using the league/oauth2 package. This repo is just to learn the OAuth2 package with it's example files. It
is not intended for production use. 

## Generate private + public keys

The oauth2 package requires a public and private key. You must run the following commands to 
generate those keys:

**Private Key**
```
openssl genrsa -out ./storage/private.key 2048
```

**Public Key**
```
openssl rsa -in ./storage/private.key -pubout -out ./storage/public.key
```

## Starting the server

For this simple example we'll use PHP's built in server:

```
php -S localhost:8080 ./public/index.php
```