<template name="about">
	<view>
		<view class="UCenter-bg">
			<image src="/static/images/logo.png" class="png" mode="widthFix"></image>
			<view class="text-xxl padding">{{name}}</view>
			<image src="/static/images/wave.gif" mode="scaleToFill" class="gif-wave"></image>
		</view>
		<view class="margin-xl">
			<articledetail :cid="cid" :isPage="true" :showTools="false" @getInfo="getInfo"/>
			<commentdetail :cid="cid" :isPage="true" :refresh="refreshComments" v-if="showComments" />
			<footerinfo />
		</view>
		<commentsender :cid="cid" :isPage="true" :title="title" :thumb="thumb" @onRefreshComments="onRefreshComments"  v-if="showComments" />
	</view>
</template>

<script>
	import articledetail from '@/components/articledetail.vue'
	import footerinfo from '@/components/footerinfo.vue';
	import commentdetail from '@/components/commentdetail.vue'
	// #ifdef MP-WEIXIN
	import commentsender from '@/components/commentsender/wx.vue';
	// #endif
	// #ifdef MP-QQ
	import commentsender from '@/components/commentsender/qq.vue';
	// #endif
	// #ifdef APP-PLUS
	import commentsender from '@/components/commentsender/app.vue';
	// #endif
	// #ifdef H5
	import commentsender from '@/components/commentsender/h5.vue';
	// #endif
	// #ifdef MP-TOUTIAO
	import commentsender from '@/components/commentsender/toutiao.vue';
	// #endif
	import cfg from "@/config.js";
	import API from '@/utils/api.js'
	import Net from '@/utils/net.js'
	import Util from '@/utils/util.js'

	export default {
		mounted() {
			this.showComments = getApp().globalData.showComments;
			this.getabout();
		},
		components: {
			articledetail,
			commentdetail,
			commentsender,
			footerinfo
		},
		data() {
			return {
				cid: null,
				showComments: false,
				refreshComments: false,
				name: cfg.getname,
				title: null,
				title: null,
				thumb: null
			}
		},
		methods: {
			getInfo(e) {
				this.title = e.title;
				this.thumb = e.thumb;
			},
			getabout() {
				let that = this;
				let content = ``
				Net.request({
					url: API.getAboutCid(),
					showLoading: true,
					success: function(res) {
						var datas = res.data.data;
						if (!Util.isNull(datas)) {
							if (datas != "none") {
								that.cid = datas;
								return;
							}
						}
					}
				});
			},
			onRefreshComments() {
				this.refreshComments = !this.refreshComments;
			}
		}
	}
</script>

<style>
	.UCenter-bg {
		/* TODO */
		background-image: url('https://image.weilanwl.com/color2.0/index.jpg');
		background-size: cover;
		height: 450rpx;
		display: flex;
		justify-content: center;
		padding-top: 0rpx;
		overflow: hidden;
		position: relative;
		flex-direction: column;
		align-items: center;
		color: #fff;
		font-weight: 300;
		text-shadow: 0 0 3px rgba(0, 0, 0, 0.3);
	}

	.UCenter-bg text {
		opacity: 0.8;
	}

	.UCenter-bg image {
		width: 200rpx;
		height: 200rpx;
	}

	.UCenter-bg .gif-wave {
		position: absolute;
		width: 100%;
		bottom: 0;
		left: 0;
		z-index: 99;
		mix-blend-mode: screen;
		height: 100rpx;
	}
</style>
