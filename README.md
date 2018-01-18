# API

## 安装
* composer install  #因为系统没有vendor目录 ，请使用以下命令安装依赖
* composer update  #更新当前依赖包

## 发布
* php artisan config:cache
* php artisan route:cache
* php artisan o --force
* composer dump-autoload --optimize

## plugins
* composer require maatwebsite/excel
* composer require illuminate/redis
* composer require predis/predis

## composer list
