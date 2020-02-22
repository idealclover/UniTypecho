<template name="commentsender">
	<view>
		<view class="cu-bar input foot" :style="[{bottom: isPage ? inputBottom + tempMargin +'px': inputBottom + 'px'}]">
			<input class="solid-bottom" :adjust-position="false" :focus="false" maxlength="300" placeholder="说些什么吧!" v-model="value"
			 @focus="inputFocus" @blur="inputBlur"></input>
			<button class="send cu-btn shadow" :class="'bg-' + color" @click="sendComment">发送</button>
			<button class="share" @click="sendLike">
				<view class="action"><text class="shareText" :class="isLike ? 'cuIcon-appreciatefill text-' + color :'cuIcon-appreciate text-grey'"></text></view>
			</button>
			<button class="share" open-type="share">
				<view class="action"><text class="shareText cuIcon-share text-grey"></text></view>
			</button>
			<button class="share" @click="getPoster">
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
						<button class="cu-btn bg-green margin-left" open-type="getUserInfo" @getuserinfo="comfirmModal">确定</button>
					</view>
				</view>
			</view>
		</view>
		<view class="cu-modal" v-if="loadDialogModal2" :class="modalName=='DialogModal2'?'show':''">
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">保存海报</view>
					<view class="action" @tap="hidePoster">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<wm-poster Width="680" :imgSrc="imgUrl" :QrSrc="qrUrl" :Title="title" :Referrer="referrer" @success="posterGetSuccess"></wm-poster>
				<view class="cu-bar bg-white justify-end">
					<view class="action">
						<button class="cu-btn line-green text-green" @tap="hidePoster">取消</button>
						<button class="cu-btn bg-green margin-left" @tap="savePoster">保存</button>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import cfg from "@/static/config.js";
	import API from '@/utils/api.js';
	import Net from '@/utils/net.js';
	import Util from '@/utils/util.js';
	import Login from '@/utils/login.js';
	import wmPoster from "@/libs/wm-poster/wm-poster.vue";

	export default {
		props: ['cid', 'isPage', 'title', 'thumb'],
		components: {
			wmPoster
		},
		data() {
			return {
				value: "",
				isLike: false,
				color: cfg.getcolor,
				modalName: null,
				inputBottom: 0,
				tempAction: "",
				tempMargin: "40",
				loadDialogModal2: false,
				qrUrl: "",
				posterURL: "",
				referrer: "",
				// TODO
				imgUrl: ""
			};
		},
		mounted() {
			this.getLikeStatus();
		},
		methods: {
			showModal() {
				this.modalName = "DialogModal1"
			},
			hideModal() {
				this.modalName = null
			},
			comfirmModal(res) {
				let that = this;
				this.modalName = null;
				getApp().globalData.userInfo = res['detail']['userInfo']
				Login.login({
					success: function() {
						that.getLikeStatus();
						if (that.tempAction == "like") that.sendLike();
						else if (that.tempAction == "comment") that.sendComment();
					}
				})
			},
			getPoster(){
				uni.showLoading({
				  title: "加载中",
				  mask: false
				});
				if(!this.isPage) {
					this.qrUrl = API.getPosterUrl('/page/index/index?cid=' + this.cid);
				} else this.qrUrl = API.getPosterUrl('/pages/index/index');
				this.imgUrl = Util.isNull(this.thumb) ? "https://api.isoyu.com/bing_images.php" : this.thumb;
				this.referrer = this.isLogin() ? getApp().globalData.userInfo.nickName + '分享给你' : '分享给你';
				this.loadDialogModal2 = true;
			},
			posterGetSuccess(res) {
				uni.hideLoading();
				this.modalName = "DialogModal2"
				this.posterURL = res.tempFilePath;
			},
			savePoster(){
				let that = this;
				uni.authorize({
				    scope: 'scope.writePhotosAlbum',
				    success() {
				        uni.saveImageToPhotosAlbum({
				        	filePath: that.posterURL,
				        	success: function () {
				        		that.loadDialogModal2 = false;
				        		that.modalName = "";
				        	}
				        });
				    }
				})
			},
			hidePoster() {
				this.loadDialogModal2 = false;
				this.modalName = "";
			},
			inputFocus(e) {
				this.tempMargin = 0
				this.inputBottom = e.detail.height
			},
			inputBlur(e) {
				this.tempMargin = "40"
				this.inputBottom = 0
			},
			isLogin() {
				return (
					!Util.isNull(getApp().globalData.userInfo) &&
					!Util.isNull(getApp().globalData.userInfo.openid)
					// getApp().globalData.userInfo.openid.length >= 28
				);
			},
			getLikeStatus() {
				let that = this;
				Net.request({
					url: API.getLikeStatus(that.cid, getApp().globalData.userInfo.openid),
					showLoading: true,
					success: function(res) {
						let datas = res.data.data;
						that.isLike = datas;
					}
				});
			},
			sendLike() {
				if (!this.isLogin()) {
					this.tempAction = "like";
					this.showModal1();
					return;
				}
				let that = this;
				Net.request({
					url: API.postLike(that.cid, getApp().globalData.userInfo.openid),
					showLoading: true,
					success: function(res) {
						var datas = res.data.data;
						var status = datas.status;
						that.$emit('onRefreshComments');
						that.isLike = !that.isLike;
					}
				});
			},
			sendComment() {
				this.tempAction = "comment";
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
					),
					success: function(res) {
						uni.showToast({
							title: "评论成功",
							duration: 2000
						});
						console.log('success');
						that.$emit('onRefreshComments');
						that.value = ""
						if(Util.isNull(getApp().globalData.templateIds)) return;
						uni.requestSubscribeMessage({
						  tmplIds: getApp().globalData.templateIds,
						  success (res) {
							  uni.showToast({
							  	title: "订阅成功",
							  	duration: 2000
							  });
						  },
						  fail (e) {
							  console.log(e);
						  }
						})
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
	.share .cuIcon {
		margin: 0 !important;
	}

	.shareText {
		margin: 0 10rpx !important;
	}
</style>
