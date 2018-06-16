## 数据库相关

#### 学习
[在线api](http://www.matools.com/manual/1300)
文档说明
```
	[]   可填写
	()   本身
	|	 选填
	{}   必填
	...  多个

	斜体  变量
	粗体  固定
```


1.只要新增就会占用一个id 如果回滚id自动加1，而实际却并没有这条数据，所以会产生断层现象

```
SELECT COUNT(DISTINCT name)/COUNT(name) AS selectivity from tables;
```
分离率越低，索引效率越低

force 强制命中索引
em.force index(PRI)

慢记录查询

```
show engines;
```

查看所有数据库引擎