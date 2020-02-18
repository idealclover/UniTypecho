# UniTypecho - 多平台 Typecho 应用解决方案

> 🤷 上一个还不够，我要上十个（指小程序平台）

## 介绍

UniTypecho - 将 Typecho 打包为跨平台应用 Based on uni-app and WeTypecho.

由于移动互联网的普及，网站访问量下降，导致个人网站无人问津。

使用一套代码，可生成你能想到的几乎所有平台的程序，从而提高移动端访问与留存。

* 已测试：微信小程序/QQ小程序/移动 H5/Android 应用
* 暂未测试但理论可行：iOS 应用/支付宝小程序/百度小程序/头条小程序/钉钉小程序

UniTypecho 的安装非常简单，只需进行简单的插件与前端配置，三分钟就能搭建。

## 预览 Preview

![](https://github.com/idealclover/UniTypecho/raw/master/assets/pic1.png)

![](https://github.com/idealclover/UniTypecho/raw/master/assets/pic2.png)

微信小程序：暂未过审上架

QQ 小程序：暂未过审上架

Android 应用：[酷安](https://www.coolapk.com/apk/255470) 暂未上架

H5：[链接](https://h5.idealclover.cn/) （请使用手机打开）

## 特性 Features

一些亮眼的特性功能如下：

* 首页头图预览文章
* 按分类输出文章列表
* 解析 Markdown 文章
* 登录，点赞，回复
* 自定义关于页面
* 输出热门文章
* 域内链接跳转
* 转发文章

## 如何使用 How to Use

**注意：在各小程序平台上架需域名备案并开启 https**

1. 插件后台配置
	1. （star 并）下载源码，将 ```UniTypecho-Plugin``` 目录重命名为 ```UniTypecho``` 并上传到服务器 ```Typecho根目录/usr/plugins/``` 中
	2. 如想打包为小程序，在对应平台申请并认证，获取 ```APPId``` 与 ```APPSecret```
	3. 在 Typecho 后台启用 UniTypecho 插件，并进入配置界面填写小程序信息，展示信息等，其中 ```API密钥``` 为前后端通信密钥，请自己定义
	4. （可选）配置永久链接形式为 ```/archives/{cid}/``` （方便启用页内跳转）
2. 程序端配置
	1. 下载 IDE [HBuilder X](https://www.dcloud.io/hbuilderx.html) 并安装，打开 ```UniTypecho``` 文件夹中的项目
	2. 复制 ```config.js.example``` 为 ```config.js```，填写域名，密钥，主题配置信息（注：如 Typecho 未配置地址重写则需在域名后加入 /index.php）
	3. 复制 ```manifest.json.example``` 为 ```manifest.json```，配置 DCloud APPID 信息等
	4. 使用 npm/yarn，安装项目依赖
	5. 针对不同平台，下载开发者平台工具进行调试

## 遇到了问题？Get Stucked?

如果在安装使用中发现问题，你有以下的方式提出问题：

* 在 [issues](https://github.com/idealclover/Uni-WeTypecho) 中提出问题
* Telegram: [@idealclover](https://t.me/idealclover)
* 发邮件给项目维护者傻翠 [idealclover@live.com](mailto://idealclover@live.com)
* 加入 ~~暂时只有我一个人的~~ ~~粉丝群（划去）~~ UniTypecho 用户交流群 [1059333269](https://jq.qq.com/?_wv=1027&k=57glqp9)

## 开发计划 Next TODOs

- [ ] 接入广告

## 贡献项目 Contributes

**PR Wecome! 欢迎各种形式的 PR**

**一些开发中踩过的坑**

* QQ小程序：Color-UI 在 QQ 小程序编译后 CSS 无法解析图标
* APP：使用 V3 编译器 uParse 模块会报错，故使用 V2 编译器

## 开源许可 Open-source Licenses

Based on:

* [uni-app](https://github.com/dcloudio/uni-app)
* [WeTypecho](https://github.com/MingliangLu/WeTypecho)
* [Color-UI](https://github.com/weilanwl/ColorUI)
* [uParse](https://github.com/gaoyia/parse)

Under MIT LICENSE.

Long live open-source.
