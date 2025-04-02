`composer install`

`sail up -d`

`cp .env.example .env`

```
OPENAI_API_KEY=xxxxxxxx

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

`sail artisan migrate`

`sail artisan db:seed`

`sail artisan key:generate`

`sail down`

### 別のターミナルで以下を実行

`sail npm install`

`sail npm run dev`
