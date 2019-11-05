# はじめに
ここまで難しいことを考えず、まず手を動かすことを優先してきました。

ここからは本番環境を想定したイメージを作成していきます。
ここでまずコンテナを運用する上でベースにしたい思想を覚えてください。

# 覚えておきたい思想

## 12 Factor Apps

いい感じにアプリケーションを運用するための要素をまとめたもの。

> I. コードベース<br />
>  バージョン管理されている1つのコードベースと複数のデプロイ<br />
> II. 依存関係<br />
>  依存関係を明示的に宣言し分離する<br />
> III. 設定<br />
>   設定を環境変数に格納する<br />
> IV. バックエンドサービス<br />
>   バックエンドサービスをアタッチされたリソースとして扱う<br />
> V. ビルド、リリース、実行<br />
>   ビルド、リリース、実行の3つのステージを厳密に分離する<br />
> VI. プロセス<br />
>   アプリケーションを1つもしくは複数のステートレスなプロセスとして実行する<br />
> VII. ポートバインディング<br />
>   ポートバインディングを通してサービスを公開する<br />
> VIII. 並行性<br />
>   プロセスモデルによってスケールアウトする<br />
> IX. 廃棄容易性<br />
>   高速な起動とグレースフルシャットダウンで堅牢性を最大化する<br />
> X. 開発/本番一致<br />
>   開発、ステージング、本番環境をできるだけ一致させた状態を保つ<br />
> XI. ログ<br />
>   ログをイベントストリームとして扱う<br />
> XII. 管理プロセス<br />
>   管理タスクを1回限りのプロセスとして実行する<br />

https://12factor.net/ja/

## それらを踏まえた Docker Way
- 1コンテナに1アプリケーション
- イメージはステージングと本番環境で同一のものを利用する (可能であれば開発環境も)
- コンテナ内にデータを溜め込まない - VOLUMEを利用する
- ログは標準出力に流すだけにする
- 設定をコンテナ内に持たず、環境変数で管理する
- SSH接続しない (docker exec か docker attach でttyに入る)
- プロセスをbackgroundで起動しない(systemdなど禁止)

## Dockerfile Best Practice
> Incremental build time<br/>
> Tip #1: Order matters for caching<br/>
> Tip #2: More specific COPY to limit cache busts<br/>
> Tip #3: Identify cacheable units such as apt-get update & install<br/><br/>
> Reduce Image size<br/>
> Tip #4: Remove unnecessary dependencies<br/>
> Tip #5: Remove package manager cache<br/><br/>
> Maintainability<br/>
> Tip #6: Use official images when possible<br/>
> Tip #7: Use more specific tags<br/>
> Tip #8: Look for minimal flavors<br/><br/>
> Reproducibility<br/>
> Tip #9: Build from source in a consistent environment<br/>
> Tip #10: Fetch dependencies in a separate step<br/>
> Tip #11: Use multi-stage builds to remove build dependencies (recommended Dockerfile)

https://www.docker.com/blog/intro-guide-to-dockerfile-best-practices/

# 本番環境用のイメージを作成する際の仕様

- ビルド時間の最短化
- イメージの軽量化
- セキュリティリスクの最小化

これらをどのように行っていくかを書いていきます。

## ビルド時間の最短化
ビルド時間を最短にすることで、本番デプロイまでの時間を最小にすることができます。

Multi-stage buildを利用すると、依存性がないステージのビルドを並列で実行することができる。
また `RUN --mount=type=cache` を用いると、ビルドデータをキャッシュできるので、CIでのビルドが有利になる。
どちらもBuildKitの機能なので `export DOCKER_BUILDKIT=1` をONにして実行するとよいです。

## イメージの軽量化

イメージを軽量化することで、イメージのPush/Pullの時間が短縮されます。
またイメージが軽量=余計な情報が入っていないので、基本的にはセキュリティにも有利になります。

まずイメージを軽量にするためには、不要なファイルを追加しないことが第一です。
依存関係のあるパッケージは最低限にして、開発環境でのみ利用するファイルなどはイメージに含めないようにしましょう。

またコンパイル可能な言語であれば、Multi-stage buildを利用して、ビルド後の成果物だけをイメージに残します。

その他で言うと、イメージの軽量化のためには、イメージに含まれるレイヤのサイズを意識します。

たとえば以下のようなDockerfileを書いた場合、`docker run`した場合には気づけませんが、イメージのレイヤーデータには、`test.txt`を追加したレイヤと`test.txt`を削除したレイヤが残ります。

```
RUN echo "test" > test.txt 
RUN rm test.txt
```

以下のようにすると、1つのレイヤー内で完結しているため、イメージサイズは増えません。
```.env
RUN echo "test" > test.txt && rm test.txt
```

## セキュリティリスクの最小化

コンテナのセキュリティに関しては、以下の2つの資料がよくまとまっています。

- [Application Container Security Guide - NIST](https://www.nist.gov/publications/application-container-security-guide)
- [CIS Docker Benchmark - CIS](https://www.cisecurity.org/benchmark/docker/)

実際に運用する際には、以下の観点でセキュリティチェックを実施しましょう。
- Image Risks
- Registry Risks
- Orchestration Risks
- Container Risks
- Host OS Risks