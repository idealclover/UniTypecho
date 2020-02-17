<template name="commentsender">
	<view>
		<view class="cu-bar foot input" :style="isPage? 'padding-bottom: 100rpx;':''">
			<input class="solid-bottom" :adjust-position="false" :focus="false" maxlength="300" placeholder="说些什么吧!" @input="onInput"></input>
			<button class="send cu-btn shadow" :class="'bg-' + color" @click="sendComment">发送</button>
			<button class="share" @click="sendLike">
				<view class="action"><text class="shareText" :class="isLike ? 'cuIcon-appreciatefill text-' + color :'cuIcon-appreciate text-grey'"></text></view>
			</button>
			<button class="share" open-type="share">
				<view class="action"><text class="shareText cuIcon-share text-grey"></text></view>
			</button>
			</button>
			<button class="share">
				<view class="action"><text class="shareText cuIcon-forward text-grey"></text></view>
			</button>
		</view>
		<view class="cu-modal" :class="modalName=='DialogModal1'?'show':''">
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">授权登录</view>
					<view class="action" @tap="hideModal">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view class="padding-xl">
					1、评论，点赞需要授权登录<br />
					2、授权会获取您的头像和昵称
				</view>
				<view class="cu-bar bg-white justify-end">
					<view class="action">
						<button class="cu-btn line-green text-green" @tap="hideModal">取消</button>
						<button class="cu-btn bg-green margin-left" @tap="hideModal" open-type="getUserInfo">确定</button>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import cfg from "@/config.js";
	import API from '@/utils/api.js';
	import Net from '@/utils/net.js';
	import Util from '@/utils/util.js';

	export default {
		props: ['cid', 'isPage'],
		data() {
			return {
				value: "",
				isLike: false,
				color: cfg.getcolor,
				modalName: null
			};
		},
		methods: {
			onInput(e) {
				// console.log(e.detail['value'])
				this.value = e.detail['value'];
			},
			showModal() {
				this.modalName = "DialogModal1"
			},
			hideModal() {
				this.modalName = null
			},
			// userSubmit: function(e) {
			// 	console.log(e.detail.formId);
			// },
			isLogin() {
				return (
					getApp().globalData.userInfo != null &&
					typeof getApp().globalData.userInfo.openid != "undefined" &&
					getApp().globalData.userInfo.openid != undefined &&
					getApp().globalData.userInfo.openid.length >= 28
				);
			},
			sendLike() {
				console.log(this.value);
				if (!this.isLogin()) {
					this.showModal();
					return;
				}
				console.log(this.cid);
				console.log('login success');
				let that = this;
				Net.request({
					url: API.postLike(that.cid, getApp().globalData.userInfo.openid),
					success: function(res) {
						var datas = res.data.data;
						var status = datas.status;
						console.log(datas);
						// that.data.item.likes = datas[0].likes;
						that.isLike = !that.isLike;
					}
				});
			},
			sendComment() {
				console.log(this.value);
				if (this.value == "") {
					uni.showToast({
						title: "请输入评论内容",
						icon: "none",
						duration: 2000
					});
					return;
				}
				if (!this.isLogin()) {
					this.showModal();
					return;
				}
				console.log(this.cid);
				console.log('login success');
				let that = this;
				Net.request({
					showLoading: true,
					url: API.postComment(
						that.cid,
						getApp().globalData.userInfo.openid,
						getApp().globalData.userInfo.nickName,
						that.value,
						0,
						getApp().globalData.userInfo.avatarUrl,
						// e.detail.formId
					),
					success: function(res) {
						uni.showToast({
							title: "评论成功",
							duration: 2000
						});
						console.log('success')
						that.value = ""
						// TODO
						// that.getcomments(that.data.item.cid);
					}
				});
			}
		}
	}
</script>

<style>
	.send {
		margin-right: 10rpx;
	}
	
	.share::after {
		border: none;
	}

	.share {
		background-color: #fff;
		padding: 0
	}

	.share .action,
	.share .cuIcon{
		margin: 0!important;
	}
	
	.shareText{
		margin: 0 10rpx!important;
	}
</style>
