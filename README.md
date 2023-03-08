# Setup

```sh
docker compose up -d --build
docker compose exec php composer require
docker compose exec php bin/console doctrine:schema:update --force
```

App will be live at [http://localhost/api](http://localhost/api)

# Steps to reproduce bug

1. Create a new User resource:
```sh
curl --request POST \
  --url http://localhost/api/users \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{
	"username": "test",
	"password": "test"
}'
```
2. Authenticate as the new User:
```sh
curl --request POST \
  --url http://localhost/api/login \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --cookie PHPSESSID=<the-authentication-cookie> \
  --data '{
	"username": "test",
	"password": "test"
}'
```

3. Update User:
```sh
# This is successful
curl --request PUT \
  --url http://localhost/api/users/<user-id> \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --cookie PHPSESSID=<the-authentication-cookie> \
  --data '{
	"username": "123"
}'

# This returns 422 first time
curl --request PUT \
  --url http://localhost/api/users/<user-id> \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --cookie PHPSESSID=<the-authentication-cookie> \
  --data '{
	"username": "12"
}'

# But subsequent requests with previously successful bodies are rejected
curl --request PUT \
  --url http://localhost/api/users/1 \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --cookie PHPSESSID=<the-authentication-cookie> \
  --data '{
	"username": "123"
}'

# Server response
< HTTP/1.1 401 Unauthorized
< Server: nginx/1.22.0
< Content-Type: application/problem+json; charset=utf-8
< Transfer-Encoding: chunked
< Connection: keep-alive
< X-Powered-By: PHP/8.1.6
< X-Content-Type-Options: nosniff
< X-Frame-Options: deny
< Cache-Control: max-age=0, must-revalidate, private
< Date: Wed, 08 Mar 2023 21:32:55 GMT
< X-Robots-Tag: noindex
< Link: <http://localhost/api/docs.jsonld>; rel="http://www.w3.org/ns/hydra/core#apiDocumentation"
< Expires: Wed, 08 Mar 2023 21:32:55 GMT

* Replaced cookie PHPSESSID="deleted" for domain localhost, path /, expire 1

< Set-Cookie: PHPSESSID=deleted; expires=Tue, 08 Mar 2022 21:32:54 GMT; Max-Age=0; path=/; httponly; samesite=lax

