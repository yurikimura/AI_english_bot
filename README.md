# 英語面接用アプリ
英語圏の面接官に扮したチャットボットが模擬面接を実施し、ユーザーの応答内容を評価します。

ユーザーはマイクを使用して音声で回答し、面接官とのインタラクティブな会話形式で練習を行えます。

面接官の発言は、ボタン操作で音声再生や日本語訳の表示が可能です。

また、練習した日は可視化され、GitHubの草のようにカレンダー上に記録されるため、学習の継続状況をひと目で把握できます。 

# 初期構築

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

seedが成功していれば、test@example.com パスワード：password123 でログインできる。

### 別のターミナルで以下を実行

`sail npm install`

`sail npm run dev`

### PHP側を修了するとき

`sail down`
