# tinyblog

Myself blog based BlogMi, a tiny php blog, which is rather not need DataBase, than based files. [MiniCMS](https://github.com/bg5sbk/MiniCMS)

Main Link Category
- ?post/${id}
- ?archive/
- ?${page_path}/  page
- ?date/2019-12/
- ?tag/${tag}/

# Deploy

```
git clone https://github.com/lyj289/docker-compose-php-nginx-mysql
```
It will be prepare the deploy env, includes nginx, php7, to help you run the blog more faster.

## Update docker-compose volumes
```
# nginx
volumes:
   - "/Users/liyujian/www/blog:/var/www/html/blog"
# php
volumes:
   - "/Users/liyujian/www/blog:/var/www/html/blog"
```
## Run
```
docker-compose up -d
```

# Fixed
- Add Markdown Support 191218

