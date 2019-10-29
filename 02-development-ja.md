# はじめに
この章では、とりあえず自力で開発環境が用意できるようになります。


# 開発環境の作成
docker-composeを利用して、アプリケーションやミドルウェアを用意します。
docker-composeは Compose という単語が表しているとおり、複数のコンテナをいい感じに｢構成｣してくれます。

以下のコマンドを実行して、http://localhost:8080 を表示してみてください。wordpressの設定画面が表示されていることが確認できます。

```bash
$ cd 02-wordpress
$ docker-compose up
02-build-for-dev_wordpress_1 is up-to-date
Recreating 02-build-for-dev_db_1 ... done
Attaching to 02-build-for-dev_wordpress_1, 02-build-for-dev_db_1
db_1         | 2019-10-24 22:33:47+00:00 [Note] [Entrypoint]: Entrypoint script for MySQL Server 8.0.18-1debian9 started.
wordpress_1  | AH00558: apache2: Could not reliably determine the server's fully qualified domain name, using 172.24.0.3. Set the 'ServerName' directive globally to suppress this message
...

```


# docker-compose.ymlの解説
まず `.env` というファイルに環境変数が入っています。
これらの環境変数は各コンテナ実行の際に自動でロードされます。

先程の `docker-compose up` では以下の処理を自動で行っています。
1. docker-compose.ymlの設定を読み取る
2. wordpressコンテナとデータベース(mysql)コンテナの2コンテナを起動
3. 2コンテナが通信できるように設定
4. docker-composeを一度終了してもデータベースのデータが初期化されないように保存

以下、docker-compose.ymlで何を設定しているかを解説します。

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
      - WORDPRESS_DB_NAME=$MYSQL_DATABASE # .envに設定されている MYSQL_DATABASE の値を コンテナ内の環境変数 WORDPRESS_DB_NAME にセットします 
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
  wordpress: # 特に指定しない場合、dockerがよしなにlocalに保存してくれます
  db:
```

# 独自のコンテナを動かす

以下のように指定することで、ローカルのDockerfileをビルドして動かすこともできます。

```yaml
version: '3.1'

services:
  container1:
    build:                     # イメージのbuild指定
      context: path/to/context # Dockerのcontextを指定 (マウントしたい/COPYしたい項目があればパスを指定)
      dockerfile: path/to/Dockerfile   # Dockerfileのパス
```

再ビルドしたいときは、以下のコマンドで実行します
```bash
$ docker-compose build # イメージを再ビルド

or

$ docker-compose up --build # イメージを再ビルドして走らせる
```

# LEMP(Linux, Nginx, MySQL, and PHP)の開発環境を用意する

以下のコマンドを実行すると、http://localhost にアクセスできるようになります。
`02-lemp/php/index.php` を変更すると、変更が反映されるのがわかります。
```bash
$ cd 02-lemp
$ docker-compose up
```

# 最後に
docker-composeはdockerコマンドでできることの組み合わせです。
docker-composeで実現できる機能は、dockerコマンドを組み合わせて実現できます。
しかし、そのためには [volume](https://docs.docker.com/storage/volumes/) や、[network](https://docs.docker.com/network/) などを理解する必要があります。また、コマンドの制御が複雑になります。

そのため、開発環境ではdocker-composeを利用する利点が大きいです。
まとめると、docker-composeの利点は以下の通りです。

- 1つのファイルに複数コンテナの設定をまとめることができる
- dockerに詳しくない人でも、とりあえず動く環境を構築できる
  - 複数のコンテナを同一のネットワークに配置して相互通信ができるようにする
  - 永続化したいデータのVolumeを勝手に作成してくれる


# 課題
- http://localhost (ポート80)でwordpressにアクセスできるように変更してください
- 02-lemp を読んで、それぞれの行で何をやっているか考えてみましょう。
- 上級編) `02-lemp/docker-compose.yml` と同様のことをdockerコマンドで再現してください
  - https://hub.docker.com/_/mysql