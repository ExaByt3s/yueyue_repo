#POCO UI

POCO

---

##安装说明

1. 必须安装nodejs
2. 主目录下运行命令，安装任务组件
   npm install grunt-contrib-compass --save-dev
   npm install grunt-contrib-watch --save-dev
   npm install grunt-contrib-copy --save-dev
   npm install grunt-contrib-clean --save-dev



##目录结构

```html
sass/
  |-- dist      存放 seajs、jquery 等文件，这也是模块的部署目录
  |-- src           存放各个项目的 js、css 文件
  |     |-- _common.scss
  |     |-- include
  |     |     |-- _base.scss             
  |     |     |-- _reset.scss
  |     |     |-- _iconfont.scss
  |     |     |-- _ui-button.scss
  |     |     |-- _ui-pagination.scss
  |     |-- helps   工具和帮助
  |     |-- module
  |     |-- channel
  |     |-- pages
  |     |     |-- pages
  |     |-- world
  |-- Gruntfile.js
  |-- package.json
  |-- config.rb
  |-- README.md
```

##启动监听

`grunt --w`

##打包

`grunt build --target=module`

`module`