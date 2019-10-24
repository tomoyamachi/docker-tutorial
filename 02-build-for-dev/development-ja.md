# はじめに
この章では、とりあえず自力で開発環境が用意できるようになります。


# 開発環境の作成
docker-composeを利用して、アプリケーションやミドルウェアを用意します。

```bash
$ cd 02-build-for-dev
$ docker-compose up
02-build-for-dev_wordpress_1 is up-to-date
Recreating 02-build-for-dev_db_1 ... done
Attaching to 02-build-for-dev_wordpress_1, 02-build-for-dev_db_1
db_1         | 2019-10-24 22:33:47+00:00 [Note] [Entrypoint]: Entrypoint script for MySQL Server 8.0.18-1debian9 started.
wordpress_1  | AH00558: apache2: Could not reliably determine the server's fully qualified domain name, using 172.24.0.3. Set the 'ServerName' directive globally to suppress this message
...
```

http://localhost:8080 を表示すると、wordpressの設定画面に飛びます。

# docker-compose.ymlの解説
まず `.env` というファイルに環境変数が入っています。
これらの環境変数は各コンテナ実行の際に自動でロードされます。

```yaml
version: '3.1'
services:
  wordpress: # コンテナの識別名
    image: wordpress:5.2.4-apache # 利用するイメージ
    # build: ./path/to/dir で自作イメージを利用することもできます  
    restart: always
    ports: # {HostOSのポート}:{コンテナ内のポート} という書式で、コンテナ内のポートにホストOSからアクセスできるようになります
      - 8080:80 # HostOSで8080ポートを見に行くと、コンテナ内の80ポートにアクセスします
    environment: # 環境変数
      - WORDPRESS_DB_HOST=db
      - WORDPRESS_DB_NAME=$MYSQL_DATABASE
      - WORDPRESS_DB_USER=$MYSQL_USER
      - WORDPRESS_DB_PASSWORD=$MYSQL_PASSWORD
    volumes: # volumeで永続化したいディレクトリを指定すると、一度コンテナを停止してもデータを別領域に保存しておけます
      - wordpress:/var/www/html

  db:
    image: mysql:8.0
    restart: always
    command: '--default-authentication-plugin=mysql_native_password'
    volumes:
      - db:/var/lib/mysql

# volumeで指定したaliasの保存形式を決めます
volumes:
  wordpress:
  db:
```

# 最後に
docker-composeはdockerコマンドでできることの組み合わせです。
docker-composeで実現できる機能は、dockerコマンドを組み合わせて実現できます。

しかし、1つの設定ファイルで完結することなどを考えると、開発環境を手軽に作成する場合はdocker-composeを利用するほうが便利なことが多いです。


# 課題
- 上級編) docker-compose.ymlと同様のことをdockerコマンドで再現していきましょう
  - https://hub.docker.com/_/wordpress
  - https://hub.docker.com/_/mysql