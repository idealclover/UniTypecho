<template name="commentsender">
	<view>
		<view class="cu-bar input foot" :style="[{bottom: isPage ? inputBottom + tempMargin +'px': inputBottom + 'px'}]">
			<input class="solid-bottom" :adjust-position="false" :focus="false" maxlength="300" placeholder="说些什么吧!" v-model="value"
			 @focus="inputFocus" @blur="inputBlur"></input>
			<button class="send cu-btn shadow" :class="'bg-' + color" @click="sendComment">发送</button>
			<button class="share" @click="sendLike">
				<view class="action"><text class="shareText" :class="isLike ? 'cuIcon-appreciatefill text-' + color :'cuIcon-appreciate text-grey'"></text></view>
			</button>
			<!-- <button class="share" open-type="share">
				<view class="action"><text class="shareText cuIcon-share text-grey"></text></view>
			</button> -->
		</view>
		<view class="cu-modal" :class="modalName=='DialogModal1'?'show':''">
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">起个名字吧！</view>
					<view class="action" @tap="hideModal">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<form @submit="comfirmModal">
					<view class="text-left text-sm padding" v-if="needName">
						<text class="text-red">*请输入昵称</text>
					</view>
					<view class="cu-form-group">
						<view class="nameInput">昵称（必填）*</view>
						<input name="nameInput"></input>
					</view>
					<view class="text-left text-sm padding" v-if="needMail">
						<text class="text-red">*请输入正确邮箱</text>
					</view>
					<view class="cu-form-group">
						<view class="mailInput">邮箱（必填）*</view>
						<input name="mailInput"></input>
					</view>
					<view class="cu-form-group">
						<view class="websiteInput">网站（选填）</view>
						<input name="websiteInput"></input>
					</view>
					<view class="text-left text-sm padding">
						<text class="text-red">*您的邮箱信息不会被公开</text>
					</view>
					<!-- <view class="cu-form-group">
						<view class="isRememberSwitch">记住我</view>
						<switch @change="IsRemember" :class="isRemember?'checked':''" :checked="isRemember?true:false"></switch>
					</view> -->
					<view class="cu-bar bg-white justify-end">
						<view class="action">
							<button :class="'cu-btn line-' + color + ' text-' + color" @tap="hideModal">取消</button>
							<button :class="'cu-btn bg-' + color +  ' margin-left'" form-type="submit">确定</button>
						</view>
					</view>
				</form>
			</view>
		</view>
	</view>
</template>

<script>
	import cfg from "@/static/config.js";
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
				modalName: null,
				inputBottom: 0,
				tempAction: "",
				tempMargin: "40",
				isRemember: true,
				needName: false,
				needMail: false
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
				let data = res.detail.value;
				if (Util.isNull(data.nameInput)) {
					this.needName = true;
				} else {
					this.needName = false;
				}
				let re =
					/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				if (Util.isNull(data.mailInput) || !re.test(String(data.mailInput).toLowerCase())) {
					this.needMail = true;
				} else {
					this.needMail = false;
				}
				if (this.needName || this.needMail) return;

				let that = this;
				if (this.isRemember) {
					uni.setStorage({
						key: 'userInfo',
						data: {
							'name': data.nameInput,
							'mail': data.mailInput,
							'website': data.websiteInput,
						},
						success: function() {
							that.modalName = null;
							if (that.tempAction == "like") that.sendLike();
							else if (that.tempAction == "comment") that.sendComment();
						}
					});
				}
			},
			inputFocus(e) {
				this.tempMargin = 0
				this.inputBottom = e.detail.height
			},
			inputBlur(e) {
				this.tempMargin = "40"
				this.inputBottom = 0
			},
			IsRemember(e) {
				this.isRemember = e.detail.value
			},
			getLikeStatus() {
				let that = this;
				uni.getStorage({
					key: 'userInfo',
					success: function(res) {
						let userInfo = res.data;
						Net.request({
							url: API.getLikeStatus(that.cid, userInfo.mail),
							showLoading: true,
							success: function(res) {
								let datas = res.data.data;
								that.isLike = datas;
							}
						});
					}
				})
			},
			sendLike() {
				let that = this;
				uni.getStorage({
					key: 'userInfo',
					success: function(res) {
						let userInfo = res.data;
						Net.request({
							url: API.postLike(that.cid, userInfo.mail),
							showLoading: true,
							success: function(res) {
								var datas = res.data.data;
								var status = datas.status;
								that.$emit('onRefreshComments');
								that.isLike = !that.isLike;
							}
						});
					},
					fail: function() {
						that.tempAction = "like";
						that.showModal();
					}
				})
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
				};
				let that = this;
				uni.getStorage({
					key: 'userInfo',
					success: function(res) {
						let userInfo = res.data;
						Net.request({
							showLoading: true,
							url: API.postComment(
								that.cid,
								userInfo.name,
								userInfo.mail,
								userInfo.website,
								that.value,
								0,
							),
							success: function(res) {
								if(res.data.data.status == "waiting"){
									uni.showToast({
										title: "请等待审核",
										duration: 2000
									});
								}else if(res.data.data.status == "approved"){
									uni.showToast({
										title: "评论成功",
										duration: 2000
									});
								}
								console.log('success');
								that.$emit('onRefreshComments');
								that.value = ""
							}
						});
					},
					fail: function() {
						that.tempAction = "comment";
						that.showModal();
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
