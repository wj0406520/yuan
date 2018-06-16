## 框架说明

### 想法

1.统一代码中，数据库中，数据库信息
2.统一错误信息
3.数据库读写分离（二期）
4.自动生成基础操作数据库模版，控制器
5.自动生成接口文档
6.自动同步代码和数据库信息
7.自动缓存信息
8.接入composer
9.预留各种中间层
10.更加面向对象

```
|-- app  存放一些有关项目文件
	|-- api    项目
	   |-- controls 存放控制器文件
	   |-- layout	存放框架文件
	   |-- dao      存放dao文件
	   |-- html	    存放视图文件
	   |-- views	存放表单视图
|-- bin 存放一些命令
|-- config 存放一些基础数据
|-- core  1.初始化要使用函数 2.程序入口
	|-- yuan  最核心内容
	|-- tool  相关工具类
	|-- three 第三方类
|-- public   存放index.php和js，css等外部访问的文件（项目访问目录）
	|-- data 是一个软链接，链接到 storage 目录下的上传文件目录
|-- storage 存放一些文件，日志，缓存，上传文件等
|-- vendor 存放composer内容
|-- test 存放一些测试文件
```


### html需要最少模块
- 登录页
- 列表模块
- 标签模块
- 翻页模块
- 表单模块
	- text
	- file
	- checkbox
	- radio
	- select
- 弹出框模块
- 自动验证
- 自动提交

