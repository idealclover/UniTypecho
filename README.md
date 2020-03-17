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

微信小程序 & QQ 小程序：

![](https://github.com/idealclover/UniTypecho/raw/master/assets/qrcode.png)

字节跳动小程序：正在审核中

Android 应用：[蓝奏云](https://www.lanzous.com/i9k2usj)

H5 页面：[链接](https://h5.idealclover.cn/) （请使用手机打开）

![](https://github.com/idealclover/UniTypecho/raw/master/assets/qrcode2.png)

截图：

![](https://github.com/idealclover/UniTypecho/raw/master/assets/pic1.png)

## 特性 Features

### 微信订阅消息：

当发送评论时，微信会询问是否发送订阅消息。若用户选择接收，则当其评论有新回复时会收到微信订阅消息通知，并可进入小程序查看。

![](https://github.com/idealclover/UniTypecho/raw/master/assets/pic2.png)

### 内链打开：

前提：开启伪静态并规定链接形式为 ```域名/archives/:cid```

众所周知，小程序禁止外链链接。而在 UniTypecho 中。将会根据 URL 进行自动识别，并进行小程序内跳转。这样当自己的一篇文章引用另一篇文章时，便可让读者很方便地查看。

而对于外链链接，UniTypecho 则会将链接地址复制到剪贴板。

### 海报分享：

在微信小程序中提供了海报分享方式，可以生成海报并保存，便于分享。

![](https://github.com/idealclover/UniTypecho/raw/master/assets/pic3.png)

而 QQ 小程序由于自带可以分享到 QQ 空间，微信好友及朋友圈，故未加入海报分享功能。

### 分享跳转：

当用户通过扫描二维码进入时，若直接进入文章页，则返回时会直接跳出小程序。而 UniTypecho 采取了先进入主页再跳转到文章页的做法，使得用户返回时会回到主页继续浏览。

![](https://github.com/idealclover/UniTypecho/raw/master/assets/pic4.png)

### 黑白名单

设置评论默认归类为通过/待审核，重重把关避免垃圾评论骚扰

![](https://github.com/idealclover/UniTypecho/raw/master/assets/pic5.png)

### 以及更多：

* 一键更换主题色
* 通过微信赞赏码捐赠
* 首页头图预览文章，自定义分享文案
* 使用微信/QQ/邮箱登录，点赞，回复
* 按分类输出文章列表，按点赞/评论/浏览输出热门文章
* 解析 Markdown & HTML 文章
* 自定义关于页面

## 如何使用 How to Use

参见 [UniTypecho 安装使用全攻略](https://idealclover.top/archives/613/)

## 遇到了问题？Get Stucked?

如果在安装使用中发现问题，你有以下的方式提出问题：

* 在 [issues](https://github.com/idealclover/Uni-WeTypecho) 中提出问题
* Telegram: [@idealclover](https://t.me/idealclover)
* 发邮件给项目维护者傻翠 [idealclover@live.com](mailto://idealclover@live.com)
* 加入 ~~暂时只有我一个人的~~ ~~粉丝群（划去）~~ UniTypecho 用户交流群 [1059333269](https://jq.qq.com/?_wv=1027&k=57glqp9)

![](https://github.com/idealclover/UniTypecho/raw/master/assets/qqgroup.png)

## 开发计划 Next TODOs

- [ ] 接入广告

## 贡献项目 Contributes

**PR Wecome! 欢迎各种形式的 PR**

**一些开发中踩过的坑**

* QQ小程序：Color-UI 在 QQ 小程序编译后 CSS 无法解析图标
* APP：使用 V3 编译器 uParse 模块会报错，故使用 V2 编译器

## 赞助我 Donate Me

欢迎您对我项目的支持！

您可以点击 [这个链接](https://donate.idealclover.cn) 或扫描下方的二维码向我支持

支持微信，支付宝，QQ等多种支付方式

![](https://github.com/idealclover/UniTypecho/raw/master/assets/donate.png)

关于如何用一个链接做到如此，这是我的另一个开源项目 [Click-to-Donate](https://github.com/idealclover/click-to-donate) 。

## 开源许可 Open-source Licenses

Based on:

* [uni-app](https://github.com/dcloudio/uni-app)
* [WeTypecho](https://github.com/MingliangLu/WeTypecho)
* [uni-app-components](https://github.com/MyQuitter/uni-app-components)
* [Color-UI](https://github.com/weilanwl/ColorUI)
* [uParse](https://github.com/gaoyia/parse)

Under MIT LICENSE.

Long live open-source.
