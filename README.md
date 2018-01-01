# oauth2
oauth2 using the league/oauth2 package.

## Generate private & public keys

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