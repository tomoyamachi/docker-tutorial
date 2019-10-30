# インストール

https://docs.docker.com/install/

利用中のOSに応じて、インストールを完了してください。

# Dockerの何が嬉しいか

## サンドボックス環境を簡単、かつ高速に作成し、破棄できる

DockerはLinux Kernelの機能を用いて、プロセスを隔離した上でHostOS上で動作させることができる。
そのためオーバーヘッドが少なく、高速に動作する。
![docker-vs-vm.png](https://www.docker.com/sites/default/files/d8/2018-11/docker-containerized-and-vm-transparent-bg.png)
> https://www.docker.com/resources/what-container

### ためしてみよう

00-php/main.php をPHP5.6環境と、PHP7.3環境でそれぞれ動かしたいとします。

従来のアプローチだと、PHPをそれぞれインストールして、バージョン管理をする、というアプローチでした。
しかしDockerを利用すると、以下のようにすることで両方確認することができます。


```.env
# ver5.6環境で実行
$ docker run -it -v $(pwd)/00-php-sample:/app \
    --rm php:5.6-cli-alpine \
    php /app/main.php
NULL

# ver7.3環境で実行
$ docker run -it -v $(pwd)/00-php-sample:/app \
    --rm php:7.3-cli-alpine \
    php /app/main.php

Fatal error: Redefinition of parameter $void in /app/main.php on line 3
```


## 一度つくった環境を保存/再現できる

Dockerは一度つくった環境をイメージという形式で保存できる。

### Docker ImageとDocker Container

Docker Imageは静的なもの。Docker Containerは、Docker Image を実行したプロセスのこと。

![container-image.png](https://newrelic-wpengine.netdna-ssl.com/wp-content/uploads/docker-image-vs-containers.jpg)