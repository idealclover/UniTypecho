<template name="rank">
	<view>
		<scroll-view scroll-x class="bg-white nav">
			<view class="flex text-center">
				<view class="cu-item flex-sub text-lg" :class="index==tabCur?'cur text-' + color :''" v-for="(item,index) in categoryList"
				 :key="index" @tap="tabSelect" :data-id="index">
					{{item}}
				</view>
			</view>
		</scroll-view>
		<view class="cu-list menu">
			<view class="cu-item" v-for="(item,index) in articleList" :key="index" @click="openArticle(item.cid, item.title)">
				<view class="content padding-tb-sm">
					<view class="padding-lr-xs text-lg">{{item.title}}</view>
					<view class="text-gray text-sm">
						<text class="cuIcon-attentionfill padding-lr-xs"></text>{{item.views}}
						<text class="cuIcon-likefill padding-lr-xs"></text> {{item.likes}}
						<text class="cuIcon-communityfill padding-lr-xs"></text> {{item.comments}}
						<text class="cuIcon-timefill padding-lr-xs"></text>{{item.time}}
					</view>
				</view>
			</view>
		</view>
		<footerinfo />
	</view>
</template>

<script>
	import footerinfo from '@/components/footerinfo.vue';
	import cfg from "@/static/config.js";
	import API from '@/utils/api.js'
	import Net from '@/utils/net.js'
	export default {
		components: {
			footerinfo
		},
		data() {
			return {
				tabCur: 0,
				color: cfg.getcolor,
				categoryList: ['浏览量', '评论数', '点赞数'],
				articleList: []
			}
		},
		mounted() {
			this.fetchRank(0);
		},
		methods: {
			tabSelect(e) {
				this.fetchRank(e.currentTarget.dataset.id);
				this.tabCur = e.currentTarget.dataset.id;
			},
			fetchRank(idx) {
				let that = this;
				Net.request({
					url: API.getRankedPosts(idx),
					showLoading: true,
					success: function(res) {
						let datas = res.data.data;
						let rank = 1;
						that.articleList = datas.map(function(ori_item) {
							let item = API.parsePost(ori_item);
							item.time = API.getCreatedTime(item.created);
							item.comments = item.commentsNum;
							item.rank = rank++;
							return item;
						});
					}
				});
			},
			//进入文章
			openArticle(cid, title) {
				uni.navigateTo({
					url: '../post/post?cid=' + cid + '&title=' + title
				});
			}
		}
	}
</script>

<style>

</style>
