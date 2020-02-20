<template>
	<view>
		<list v-if="pageCur=='list'"></list>
		<rank v-if="pageCur=='rank'"></rank>
		<about v-if="pageCur=='about'"></about>
		<view class="cu-bar tabbar bg-white shadow foot">
			<view class="action" @click="NavChange" data-cur="list">
				<view><text class="justify-center" :class="pageCur=='list'?'cuIcon-homefill text-' + color:'cuIcon-home'"></text></view>
				<view :class="pageCur=='list'?'text-' + color :'text-gray'">首页</view>
			</view>
			<view class="action" @click="NavChange" data-cur="rank">
				<view><text class="justify-center" :class="pageCur=='rank'?'cuIcon-hotfill text-' + color:'cuIcon-hot'"></text></view>
				<view :class="pageCur=='rank'?'text-' + color :'text-gray'">排行</view>
			</view>
			<view class="action" @click="NavChange" data-cur="about">
				<view><text class="justify-center" :class="pageCur=='about'?'cuIcon-myfill text-' + color :'cuIcon-my'"></text></view>
				<view :class="pageCur=='about'? 'text-' + color :'text-gray'">关于</view>
			</view>
		</view>
	</view>
</template>

<script>
	import cfg from "@/config.js";
	import API from '@/utils/api.js';
	import Net from '@/utils/net.js';
	import Util from '@/utils/util.js'
	import Login from '@/utils/login.js';

	export default {
		data() {
			return {
				pageCur: 'list',
				color: cfg.getcolor
			}
		},
		methods: {
			NavChange: function(e) {
				this.pageCur = e.currentTarget.dataset.cur
			}
		},
		onLoad: function(option) {
			// #ifdef MP
			uni.getUserInfo({
				provider: 'weixin',
				success: function(res) {
					getApp().globalData.userInfo = res.userInfo;
					Login.login({
						success: function() {
							// if (options.item) {
							// 	wx.navigateTo({
							// 		url: "../detail/detail?item=" + options.item
							// 	});
							// }
						}
					});
				}
			});
			// #endif
			Net.request({
				url: API.getConfig(),
				success: function(res) {
					var datas = res.data.data;
					let showComments = datas['showComments'] == "1" ? true : false;
					let showShare = datas['showShare'] == "1" ? true : false;
					getApp().globalData.showComments = showComments;
					getApp().globalData.showShare = showShare;
				}
			});
			this.cid = option.cid;
		},
		onReady: function() {
			if(!Util.isNull(this.cid)){
				uni.navigateTo({
					url: '../post/post?cid=' + this.cid
				})
			}
		},
		// #ifdef MP-QQ
		onShareAppMessage: function () {
		    qq.showShareMenu({
				showShareItems: ['qq', 'qzone', 'wechatFriends', 'wechatMoment']
		    })
		}
		// #endif
	}
</script>

<style>
	.cu-bar.tabbar {
		height: 40px;
	}
</style>
