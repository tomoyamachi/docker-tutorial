# はじめに
ここまで難しいことを考えず、まず手を動かすことを優先してきました。
ここからは本番環境を想定したイメージを作成していきます。
ここでまずコンテナを運用する上でベースにしたい思想を覚えてください。

# 覚えておきたい思想

## 12 Factor Apps

いい感じにアプリケーションを運用するための要素をまとめたもの。

> I. コードベース<br />
>   バージョン管理されている1つのコードベースと複数のデプロイ<br />
> II. 依存関係<br />
>   依存関係を明示的に宣言し分離する<br />
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