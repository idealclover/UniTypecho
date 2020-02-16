<template name="commentsender">
	<view>
		<view class="cu-bar foot input" :style="[{bottom:InputBottom+'px'}]">
			<!-- <form report-submit='true' :bindsubmit="userSubmit"> -->
			<input class="solid-bottom" :adjust-position="false" :focus="false" maxlength="300" cursor-spacing="10" @focus="InputFocus"
			 @blur="InputBlur" placeholder="说些什么吧!" ref="commentInput"></input>
			<!-- <view class="action"><text class="cuIcon-emojifill text-grey"></text></view> -->
			<button class="cu-btn bg-green shadow" @click="sendComment()" open-type="getUserInfo">发送</button>
			<!-- form-type='submit' -->
			<!-- </form> -->
			<view class="action"><text class="cuIcon-appreciate text-grey"></text></view>
			<view class="action"><text class="cuIcon-share text-grey"></text></view>
			<view class="action"><text class="cuIcon-forward text-grey"></text></view>
		</view>
	</view>
</template>

<script>
	import API from '@/utils/api.js'
	import Net from '@/utils/net.js'
	export default {
		props: ['cid'],
		data() {
			return {};
		},
		methods: {
			// userSubmit: function(e) {
			// 	console.log(e.detail.formId);
			// },
			sendComment() {
				console.log(this.$refs.commentInput);
				let text = this.$refs.commentInput.inputValue;
				console.log(text);
				if (typeof text !== 'undefined' || text == null || text == "") return;
				
				// let that = this;
				// uni.login({
				// 	provider: 'weixin',
				// 	success: function(loginRes) {
				// 		// console.log(loginRes.authResult);
				// 		uni.getUserInfo({
				// 			provider: 'weixin',
				// 			success: function(infoRes) {
				// 				// console.log('用户昵称为：' + infoRes.userInfo.nickName);
				// 				console.log(infoRes.userInfo);
				// 				Net.request({
				// 				  url: API.Login(infoRes.userInfo),
				// 				  success: function(res) {
				// 					let datas = res.data.data;
				// 					infoRes.userInfo.openid = datas;
				// 					console.log(infoRes);
				// 				  }
				// 				});
								// if (API.loginsuccess(app))
								// Net.request({
								// 	url: API.Postcomment(
								// 		that.cid,
								// 		infoRes.userInfo.openid,
								// 		infoRes.userInfo.nickName,
								// 		"aaa",
								// 		0,
								// 		infoRes.userInfo.avatarUrl,
								// 		// e.detail.formId
								// 	),
								// 	success: function(res) {
								// 		// TODO
								// 		// that.getcomments(that.data.item.cid);
								// 	}
								// });
				// 			}
				// 		});
				// 	}
				// });

				// if (API.loginsuccess(app)) {
				// 	if (this.data.replyauthor) {
				// 		this.data.replaycoid = 0;
				// 	}
				// 	this.data.replyauthor = true;
				// 	Net.request({
				// 		url: API.Postcomment(
				// 			that.data.item.cid,
				// 			app.Data.userInfo.openid,
				// 			app.Data.userInfo.nickName,
				// 			that.data.cmtext,
				// 			that.data.replaycoid,
				// 			app.Data.userInfo.avatarUrl,
				// 			e.detail.formId
				// 		),
				// 		success: function(res) {
				// 			// TODO
				// 			that.getcomments(that.data.item.cid);
				// 		}
				// 	});

				// 	this.$refs.commentInput.inputValue = "";
			}
		}
	}
</script>

<style>

</style>
