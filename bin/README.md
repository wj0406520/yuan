### 命令使用说明

#### 总结

- 命令使用 **`php ****.php`**
- 每个命令会有先行条件，下面会详细介绍



#### app.php 生成项目

> 功能介绍

- 需要**`config/url.php`**的支持

- **`app`**文件夹下会生成下列文件和目录

```
	|-- controls        (控制器文件夹)
	    |-- All.php     (总的控制器)
	|-- dao 			(dao层 模型和控制器的中间层)
		|-- AllDao.php  (总的dao文件)
	|-- route 			(路由目录)
		|-- route.php   (路由文件)
```

- 如果**`is_web`**为**`1`**的情况下会

```
	|--- layout              (前端总框架)
			|--- layout.html (前端文件)
	|--- views 			     (视图目录)
			|--- Views.php   (视图文件)
```

- 还会再**`pulick`**目录下生成对应目录和**`index.php`**和**`.htaccess`**文件

#### back.php 备份数据库

> 功能介绍

- 备份当前所链接的数据库(包括结构和数据)

- 备份的文件在**`storage/back`**目录下面


#### local.php 本地同步数据库数据结构

> 功能介绍

- 需要 **`config/sql.php`** 文件的支持

- **`config/sql.php`** 可以手动编写或者**`service.php`**生成

- 把**`config/sql.php`**内容按照规则更新或新增到数据库中

#### models.php 生成模型

> 功能介绍

- 需要 **`config/sql.php`** 文件的支持

- 需要 **`config/link.php`** 文件的支持

- 会在**`service/models`**生成各个数据库对应的模型文件

- 而且会自动维护**`service/models`**目录，删除无用的，或者多余的文件

#### renew.php 恢复数据库

> 功能介绍

- 需要 **`php back.php`** 命令的支持

- 把之前备份的数据重新导入数据库中

#### route.php 自动生成项目控制器,dao[,html]

> 功能介绍

- 需要 **`config/url.php`** 的支持

- 需要 **`app//route/route.php`** 的支持

- 会把 **`route.php`** 中的配置生成对应的 控制器,dao[,html]

- 会自动新增

- 如果**`is_web`**为**`1`**则会生成对应的**`html`**目录和文件

#### service.php 数据库同步本地文件

> 功能介绍

- 需要数据库有数据结构

- 把数据库中的数据结构转化为**`config/sql.php`**文件

