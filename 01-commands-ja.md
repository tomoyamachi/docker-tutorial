# はじめに
この章では基本となるdocker CLIのコマンドを学びます。

# 基本となるコマンド

## docker run : イメージを元にコンテナプロセスを実行する

```bash
$ docker run alpine whoami 
root
# alpineというOSで、whoamiを実行した結果

$ docker run alpine /bin/sh
# インタラクティブな動作に対応してないので、何もおこらず終了 

$ docker run -it alpine /bin/sh
/ #
/ # exit
# インタラクティブモード (-i) で実行すると、コマンドをインタラクティブに実行できる

$ docker container ls -a // docker ps -a でも同様
CONTAINER ID        IMAGE                              COMMAND                   CREATED              STATUS                         PORTS                                            NAMES
18752853ea11        alpine                             "/bin/sh"                 About a minute ago   Exited (130) 24 seconds ago                                                     gifted_satoshi
# コンテナのプロセス自体がexitになってもコンテナは残る

$ docker rm gifted_satoshi // コンテナ名を指定して削除

$ docker container ls -a // docker ps -a でも同様
CONTAINER ID        IMAGE                              COMMAND                   CREATED              STATUS                         PORTS                                            NAMES
# コンテナが削除されたことを確認

$ docker run --rm -it alpine /bin/sh
/ #
/ # exit
# --rm を指定して実行すると、実行後もコンテナが残らない

$ docker container ls -a // docker ps -a でも同様
CONTAINER ID        IMAGE                              COMMAND                   CREATED              STATUS                         PORTS                                            NAMES
# コンテナが残ってないことを確認
```

## docker build -t <imageName> <context> : Dockerfileなどを元にイメージを作成

```bash
$ cd 01-commands
$ cat Dockerfile
FROM busybox
CMD ["echo", "hello world"]
$ docker build -t local-hello-world:v1 .
Sending build context to Docker daemon  5.632kB
Step 1/2 : FROM busybox
 ---> 19485c79a9bb
Step 2/2 : CMD ["echo", "hello world"]
 ---> Running in 42594be1d165
Removing intermediate container 42594be1d165
 ---> 197fa904249e
Successfully built 197fa904249e
Successfully tagged local-hello-world:v1
$ docker images
REPOSITORY                                                              TAG                 IMAGE ID            CREATED             SIZE
local-hello-world                                                              v1                  197fa904249e        6 minutes ago       1.22MB
$ docker run --rm local-hello-world:v1
hello world
```

Dockerfileなしでstdinでもビルドはできる。

```bash
$ docker build -<<EOF
FROM busybox
CMD ["echo", "hello, world"]
EOF
```
## docker pull : リモートにあるイメージを持ってくる
`docker run`で、ローカルにないイメージ名を指定すると勝手に持ってきてくれるので、わざわざ利用することはないかも。

## docker push : リモートにイメージを送る
まだ覚えなくていい。

# Dockerfileの書き方

細かいベストプラクティスは04章で見ていく。ここでは最低限のルールだけ覚える。

```Dockerfile
# FROM はベースとなるイメージを指定する
FROM ubuntu:19.10

# RUN はイメージビルド時に内部で実行するコード 
RUN apt-get install -y --no-install-recommends curl

# COPY はイメージにホストOSのファイルを追加できる
COPY ./sample.txt /app/sample.txt

# コンテナ内の環境変数を指定する
ENV PATH="${PATH}:/app/bin"

# ARGは docker build --build-arg ... という形式で引数を渡すことができる
ARG sample="default message"
# RUN echo $hoge => build-arg sample=helloworld でビルドした場合、 "helloworld"が出力

# コンテナ内のpwdを設定する
WORKDIR /root

# ENTRYPOINT はdocker run時にデフォルトで実行するコマンドを記載します
# CMD はdocker run時のパラメタで上書きできます。
ENTRYPOINT ["echo", "hello, world"]
CMD ["default message"]
```

# 課題

- このディレクトリのDockerfileを利用してイメージを作成してください
- 作成したイメージを実行してください
- Dockerfileを編集して、以下の挙動を持つイメージをつくってください
  - `docker run <image>` で `hello, world`と出力。
  - `docker run <image> hoge` で `hello, hoge` と出力。
- 上記のコンテナの/bin/shにインタラクティブモードでログインしてください。
  - ENTRYPOINTの上書きが必要になると思います