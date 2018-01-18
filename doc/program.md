MySQL报错ERROR 1615 (HY000): Prepared statement needs to be re-prepared
原创 2015年01月29日 17:22:13 195201
今天公司的项目视图查询报错。找了代码和视图的原因，发现表示没有问题的，视图就出错了。报错如下：
ERROR 1615 (HY000): Prepared statement needs to be re-prepared
很多情况是mysql的变量值设置不合理引起的，调整以下值：
mysql set global table_open_cache=16384;
mysql set global table_definition_cache=16384;