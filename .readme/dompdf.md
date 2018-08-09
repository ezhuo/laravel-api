

php load_font.php 'Droid' E:\myRoot\pinggu\api\doc\DroidSansFallback.ttf
php E:\myRoot\pinggu\api\vendor\dompdf\dompdf/load_font.php msyh E:/myRoot/pinggu/api/public/fonts/msyh.ttf

php   dompdf包如何将HTML页面导出中文无乱码的PDF文档
首先下载composer

           curl -sS https://getcomposer.org/installer | php

下载dompdf包

   php composer require dompdf/dompdf



下载load_font.php，此文件的功能是安装中文字体

         1.git clone  https://github.com/dompdf/utils.git

         2. 复制 load_font.php到 dompdf目录中，与lib 和 src 目录同级。



下载中文字体，推荐下载 Droid Sans Fallback 字体，也可用雅黑字体，【雅黑字体会导致导出文档过大】

                 下载链接【http://www.17ziti.com/info/71250.html】



 安装字体,将字体传到服务器目录下，运行load_font.php

       php load_font.php ‘Droid‘  /data/DroidSansFallback.ttf。

      运行后，若没报错，则在 vendor/dompdf/dompdf/lib/fonts/下生了                                    Droid.ttf，Droid.ufm    这两个文件。



在PHP代码中设置中文字体
<?php
require ‘vendor/autoload.php‘;
use Dompdf\Dompdf;
$dompdf = new Dompdf();
$html=
<<<HTML
 <html>
<head>
</head>
<body>
<!-- font-family:yahei;  china-->
<div style="font-family:Droid; color: #f00;font-size: 14px"> 中文123 </div>
</body>
</html>
HTML;
$html = iconv(‘gb2312‘,‘utf-8‘,$html);
$dompdf->loadHtml($html);
$dompdf->setPaper(‘A4‘, ‘landscape‘);
$dompdf->render();
$dompdf->stream();
注意 CSS 样式中的 font-family 设置为 之前运行load_font.php中设置的字体名。
php   dompdf包如何将HTML页面导出中文无乱码的PDF文档