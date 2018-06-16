## shell相关

### 设置上传文件夹超链接

设置上传文件超链接到`public`文件夹下面是为了让上传文件也能够通过目录访问

```
ln -s /www/storage/data
```


### 查看安装的服务


#### 查看某某服务的状态
```
systemctl status mysql.service
```


#### 查看某某服务是否可用，如果不可用就会报错
```
systemctl enable mysql.service
```


这个错误
```
perl: warning: Falling back to the standard locale ("C").
```

可以用这个解决
```
export LC_ALL=C
```