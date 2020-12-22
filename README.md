## Initial steps

1. Run `docker-compose up`
    - run `docker-compose exec php composer i`
    - you can connect to db by credentials in .env file (outside container: 127.0.0.1:8001)
2. Run migrations `docker exec $(docker ps -q -f name=myapp-php) php artisan migrate`
3. Run command for Creating a personal access client `docker exec $(docker ps -q -f name=myapp-php) php artisan passport:install`
4. Run command `docker exec $(docker ps -q -f name=myapp-php) php artisan command:sample-data` to fill DB by test data.
    (In console terminal you will see needed credentials: created users and wallets)
5. Create POST Requests to http://127.0.0.1:8012/api/v1/auth with `email = user1@app.app, password = 123456` for get access_token
6. Requests must be sent with `OAuth 2.0` type authorization and token must be `Bearer <your_access_token>`.
7. There are unit tests. Execute by: `docker exec $(docker ps -q -f name=myapp-php) ./vendor/bin/phpunit tests/Unit/`
8. There are tests for end-points. Execute by: `docker exec $(docker ps -q -f name=myapp-php) ./vendor/bin/phpunit tests/Controller/Api/V1/`
9. There is a Swagger info: `http://127.0.0.1:8012/docs/api-docs.json`

## Curl requests
##### POST /api/v1/auth
`curl --location --request POST 'http://127.0.0.1:8012/api/v1/auth' \
--form 'email="user1@app.app"' \
--form 'password="123456"'`

##### POST /api/v1/transactions
`curl --location --request POST 'http://127.0.0.1:8012/api/v1/transactions' \
--header 'Authorization: Bearer <your_access_token>' \
--form 'from_wallet_id="<from_wallet_id>"' \
--form 'to_wallet_id="<to_wallet_id>"' \
--form 'amount="0.1"'`
