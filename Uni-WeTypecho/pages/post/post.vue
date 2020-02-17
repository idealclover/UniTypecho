<template>
	<view>
		<view class="margin-xl">
			<articledetail :cid="cid" :isPage="false" :showTools="true" @getTitle="getTitle" />
			<commentdetail :cid="cid" :isPage="false" :refresh="refreshComments" v-if="showComments" />
		</view>
		<commentsender :cid="cid" :isPage="false" @onRefreshComments="onRefreshComments" v-if="showComments" />
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
	export default {
		data() {
			return {
				showComments: false,
				refreshComments: false,
				cid: null,
				title: null,
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
			this.title = option.title;
			this.fetchPost();
		},
		methods: {
			getTitle(e) {
				uni.setNavigationBarTitle({
					title: e
				});
			},
			onRefreshComments() {
				this.refreshComments = !this.refreshComments;
			}
		}
	}
</script>

<style>

</style>
