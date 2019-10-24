# インストール

https://docs.docker.com/install/

利用中のOSに応じて、インストールを完了してください。

# Dockerの何が嬉しいか

## サンドボックス環境を簡単、かつ高速に作成できる


DockerはLinux Kernelの機能を用いて、プロセスを隔離した上でHostOS上で動作させることができる。
そのためオーバーヘッドが少なく、高速に動作する。
![docker-vs-vm.png](https://www.docker.com/sites/default/files/d8/2018-11/docker-containerized-and-vm-transparent-bg.png)
> https://www.docker.com/resources/what-container

## 一度つくった環境を保存/再現できる

Dockerは一度つくった環境をイメージという形式で保存できる。

# Docker ImageとDocker Container

Docker Imageは静的なもの。Docker Containerは、Docker Image を実行したプロセスのこと。

![container-image.png](https://newrelic-wpengine.netdna-ssl.com/wp-content/uploads/docker-image-vs-containers.jpg)