<template>
	<view>
		<view class="margin-xl">
			<articledetail :cid="cid" :isPage="false" :showTools="true" @getInfo="getInfo" />
			<commentdetail :cid="cid" :isPage="false" :refresh="refreshComments" v-if="showComments" />
		</view>
		<commentsender :cid="cid" :isPage="false" :title="title" :thumb="thumb" @onRefreshComments="onRefreshComments" v-if="showComments" />
	</view>
</template>

<script>
	import articledetail from '@/components/articledetail.vue';
	import commentdetail from '@/components/commentdetail.vue';
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
	export default {
		data() {
			return {
				showComments: false,
				refreshComments: false,
				cid: null,
				title: "",
				thumb: null,
				isShowThumb: false
			}
		},
		components: {
			articledetail,
			commentdetail,
			commentsender
		},
		onLoad: function(option) {
			this.showComments = getApp().globalData.showComments;
			this.cid = option.cid;
		},
		methods: {
			getInfo(e) {
				this.title = e.title;
				this.thumb = e.thumb;
				if(!this.isPage) {
					uni.setNavigationBarTitle({
						title: e.title
					});
				}
			},
			onRefreshComments() {
				this.refreshComments = !this.refreshComments;
			}
		},
		onShareAppMessage: function () {
		    qq.showShareMenu({
				showShareItems: ['qq', 'qzone', 'wechatFriends', 'wechatMoment']
		    })
		}
	}
</script>

<style>

</style>
