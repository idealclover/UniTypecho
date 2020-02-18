import API from "./api.js";
import Net from "./net.js";
import Util from './util.js';

function login(handler) {
	uni.showLoading({
	  title: "加载中",
	  mask: false
	});
	uni.login({
		provider: 'weixin',
		success: function(res) {
			if(Util.isNull(getApp().globalData.userInfo)) {
				uni.hideLoading();
				return;
			}
			getApp().globalData.userInfo.code = res.code;
			// console.log(getApp().globalData.userInfo);
			Net.request({
				url: API.login(getApp().globalData.userInfo),
				success: function(res) {
					var datas = res.data.data;
					getApp().globalData.userInfo.openid = datas;
					console.log(getApp().globalData.userInfo);
					uni.hideLoading();
					handler.success();
				},
				fail: function() {
					uni.hideLoading();
				}
			});
		}
	});
}

module.exports = {
	login: login
};
