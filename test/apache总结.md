# apache总结

## dir_module和rewrite

当dir_module映射到了虚拟主机外的目录的时候，rewrite不能正常工作
>如虚拟主机目录为public/api
>dir_module目录为admin public/admin
>当访问到admin/aa的时候admin文件夹下面的.htaccess不能正常工作

## apache组件
- rewrite


## apache相关配置
```
<VirtualHost *:8080>
    DocumentRoot "/Users/wangjie/self/work/htdocs/php/yuan/public/"
    ServerName localhost
	<Location /dir/>
		# 禁止/dir/url的访问
	    Order allow,deny
	    Deny from all
	</Location>
</VirtualHost>

<Directory "/Users/wangjie/self/work/htdocs/php/yuan/public/">
    Options All
    AllowOverride All
    Order allow,deny
    Allow from all
    XSendFilePath "/Users/wangjie/self/work/htdocs/php/yuan/public/"
</Directory>

```


