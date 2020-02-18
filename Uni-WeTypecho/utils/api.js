import cfg from "../config.js";

var domain = cfg.getdomain;
var apisec = cfg.getapisecret;
var API_URL = "https://" + domain + "/uniapi/";
var lastin = 0;
var lastout = 0;

function statmember(title, value, showpr, pr) {
	this.title = title;
	this.value = value;
	this.showpr = showpr;
	this.progress = pr;
}

module.exports = {
	appendAPISEC: function(url) {
		var request = url + "&apisec=" + apisec;
		console.log(request);
		return request;
	},
	getDomain: function() {
		return "https://" + domain + "/";
	},
	getConfig: function() {
		return this.appendAPISEC(API_URL + "getConfig?");
	},
	getSwiperPosts() {
		return this.appendAPISEC(API_URL + "getswiperposts?");
	},
	getCategories: function() {
		return this.appendAPISEC(API_URL + "getcategories?");
	},
	getPostsByMid: function(mid) {
		return this.appendAPISEC(API_URL + "getpostsbymid?mid=" + mid);
	},
	getRankedPosts: function(idx) {
		return this.appendAPISEC(API_URL + "getposts?&pageSize=30" + "&idx=" + idx);
	},
	getAboutCid: function() {
		return this.appendAPISEC(API_URL + "getaboutCid?");
	},
	getPostsByCid: function(cid) {
		return this.appendAPISEC(API_URL + "getposts?cid=" + cid);
	},
	getPagesByCid: function(cid) {
		return this.appendAPISEC(API_URL + "getposts?cid=" + cid + "&ispage=1");
	},
	getCommentsByCid: function(cid) {
		return this.appendAPISEC(API_URL + "getcomments?cid=" + cid);
	},
	parsePost: function(ori_item) {
		var that = this;
		var post_date = {
			year: ori_item.year,
			month: ori_item.month,
			day: ori_item.day
		};
		var post = {
			cid: ori_item.cid,
			title: ori_item.title,
			created: ori_item.created,
			date: post_date,
			text: ori_item.text,
			commentsNum: ori_item.commentsNum,
			link: ori_item.permalink,
			thumb: ori_item.thumb[0].str_value,
			views: ori_item.views[0].views,
			likes: ori_item.likes[0].likes,
			//category: ori_item.categories.length > 0 ? ori_item.categories[0].name : null,
			category: ori_item.categories.map(function(item) {
				item.length = item.name.length;
				// item.background = that.randomHexColor();
				return item;
			}),
			mid: ori_item.categories.length > 0 ? ori_item.categories[0].mid : null,
			showshare: ori_item.showshare
		};
		return post;
	},
	getCreatedTime: function(created) {
		var now = Date.parse(new Date()) / 1000;
		var span = now - created > 0 ? now - created : 0;
		if (span <= 3600) {
			var ts = Math.round(span / 60);
			return ts + "分钟前";
		} else if (span < 86400) {
			var ts = Math.round(span / 3600);
			return ts + "小时前";
		} else {
			var ts = Math.round(span / 86400);
			return ts + "天前";
		}
	},
	login: function(userInfo) {
		return this.appendAPISEC(
			API_URL +
			"login?code=" +
			userInfo.code +
			"&nickname=" +
			userInfo.nickName +
			"&avatarUrl=" +
			userInfo.avatarUrl +
			"&city=" +
			userInfo.city +
			"&country=" +
			userInfo.country +
			"&gender=" +
			userInfo.gender +
			"&province=" +
			userInfo.province +
			// #ifdef MP-WEIXIN
			"&mp=weixin"
			// #endif
			// #ifdef MP-QQ
			"&mp=qq"
			// #endif
			// #ifdef APP-PLUS
			"&mp=app"
			// #endif
			// #ifdef H5
			"&mp=app"
			// #endif
		);
	},
	getLikedNum: function(cid) {
		return this.appendAPISEC(API_URL + "getlikednum?cid=" + cid);
	},
	getLikedList: function(cid) {
		return this.appendAPISEC(API_URL + "getlikedlist?cid=" + cid);
	},
	getLikeStatus: function(cid, openid) {
	   return this.appendAPISEC(
	     API_URL + "getLikeStatus?cid=" + cid + "&openid=" + openid
	   );
	 },
	postLike: function(cid, openid) {
		return this.appendAPISEC(
			API_URL + "postLike?cid=" + cid + "&openid=" + openid
		);
	},
	postComment: function(cid, openid, author, text, parent, icon, formid) {
		return this.appendAPISEC(
			API_URL +
			"postcomment?cid=" +
			cid +
			"&openid=" +
			openid +
			"&author=" +
			author +
			"&text=" +
			text +
			"&parent=" +
			parent +
			"&icon=" +
			icon +
			"&formid=" +
			formid
		);
	},
	//  GetPosts: function() {
	//    return this.appendAPISEC(API_URL + "posts?&pageSize=10");
	//  },

	//  GetAccessCode: function(url) {
	//    return this.appendAPISEC(API_URL + "getaccesscode?path=" + url);
	//  },
	//  GetPostsbyMIDLimit: function(mid, limit, except) {
	//    return this.appendAPISEC(
	//      API_URL +
	//        "getpostbymid?mid=" +
	//        mid +
	//        "&pageSize=" +
	//        limit +
	//        "&except=" +
	//        except
	//    );
	//  },

	//  GetPostsReplybyCID: function(cid, parent) {
	//    return this.appendAPISEC(
	//      API_URL + "getcomment?cid=" + cid + "&parent=" + parent
	//    );
	//  },
	//  Postcomment: function(cid, openid, author, text, parent, icon, formid) {
	//    return this.appendAPISEC(
	//      API_URL +
	//        "addcomment?cid=" +
	//        cid +
	//        "&openid=" +
	//        openid +
	//        "&author=" +
	//        author +
	//        "&text=" +
	//        text +
	//        "&parent=" +
	//        parent +
	//        "&icon=" +
	//        icon +
	//        "&formid=" +
	//        formid
	//    );
	//  },


	//  Getuserlikedlist: function(cid) {
	//    return this.appendAPISEC(API_URL + "getuserlikedlist?cid=" + cid);
	//  },
	//  GetServerStat: function() {
	//    return this.appendAPISEC(API_URL + "get_stat?");
	//  },
	//  Search: function(key) {
	//    return this.appendAPISEC(API_URL + "search?keyword=" + key);
	//  },
	//  loginsuccess: function(app) {
	//    return (
	//      app.Data.userInfo != null &&
	//      typeof app.Data.userInfo.openid != "undefined" &&
	//      app.Data.userInfo.openid != undefined &&
	//      app.Data.userInfo.openid.length >= 28
	//    );
	//  },
	//  IsNull(obj) {
	//    return obj != null && obj != undefined;
	//  },


	//  randomHexColor() {
	//    //随机生成十六进制颜色
	//    var hex = Math.floor(Math.random() * 16777216).toString(16); //生成ffffff以内16进制数
	//    while (hex.length < 6) {
	//      //while循环判断hex位数，少于6位前面加0凑够6位
	//      hex = "0" + hex;
	//    }
	//    return "#" + hex; //返回‘#'开头16进制颜色
	//  },
	//  ConfirmAuth: function() {
	//    wx.getSetting({
	//      success: function(res) {
	//        if (res.authSetting["scope.userInfo"]) {
	//          // 已经授权，可以直接调用 getUserInfo 获取头像昵称
	//          wx.getUserInfo({
	//            success: function(res) {}
	//          });
	//        } else {
	//          wx.navigateTo({
	//            url: "../../page/auth/auth" // 页面 A
	//          });
	//        }
	//      }
	//    });
	//  },

};
