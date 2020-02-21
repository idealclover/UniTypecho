<template name="list">
	<view>
		<swiper class="screen-swiper square-dot" v-if="!swiperList.length == 0" :indicator-dots="true" :circular="true"
		 :autoplay="true" interval="5000" duration="500">
			<swiper-item v-for="(item,index) in swiperList" :key="index" class="cur" @click="openArticle(item.cid, item.title)">
				<view class="bg-img flex align-end" mode="aspectFill" :style="[{backgroundImage: 'url(' + item.src + ')', height: '100%'}]">
					<view class="bg-shadeBottom padding flex-sub">
						{{item.title}}
					</view>
				</view>
			</swiper-item>
		</swiper>
		<scroll-view scroll-x class="bg-white nav">
			<view class="flex text-center">
				<view class="cu-item flex-sub" :class="index==tabCur?'cur text-' + color :''" v-for="(item,index) in categoryList"
				 :key="index" @tap="tabSelect" :data-id="index">
					{{item.name}}
				</view>
			</view>
		</scroll-view>
		<view class="cu-list menu">
			<view class="cu-item" v-for="(item,index) in articleList" :key="index" @click="openArticle(item.cid, item.title)">
				<view class="content padding-tb-sm">
					<view class="padding-lr-xs">{{item.title}}</view>
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
	import cfg from "@/config.js";;
	import API from '@/utils/api.js';
	import Net from '@/utils/net.js';
	export default {
		components: {
			footerinfo
		},
		mounted() {
			this.fetchTopPosts();
			this.fetchCategotyList();
		},
		data() {
			return {
				tabCur: 0,
				color: cfg.getcolor,
				swiperList: [],
				categoryList: [],
				articleList: []
			}
		},
		methods: {
			tabSelect(e) {
				this.fetchpostbymid(this.categoryList[e.currentTarget.dataset.id]['mid']);
				this.tabCur = e.currentTarget.dataset.id;
				// this.scrollLeft = (e.currentTarget.dataset.id - 1) * 60
			},
			// 加载方法
			// 获取顶栏
			fetchTopPosts() {
				let that = this;
				Net.request({
					url: API.getSwiperPosts(),
					showLoading: true,
					success: function(res) {
						var datas = res.data.data;
						let swiperList = [];
						datas.forEach(function(data) {
							let result = {};
							result['cid'] = data['cid'];
							result['title'] = data['title'];
							result['ispost'] = data['type'] == 'post' ? true : false;
							result['src'] = data['thumb']['url'];
							swiperList.push(result);
						});
						that.swiperList = swiperList;
					},
				});
			},
			//获取类别
			fetchCategotyList() {
				let that = this;
				Net.request({
					url: API.getCategories(),
					showLoading: true,
					success: function(res) {
						var datas = res.data.data;
						let categoryList = [];
						datas.forEach(function(data) {
							let result = {};
							result.name = data.name;
							result.mid = data.mid
							categoryList.push(result);
						});
						that.categoryList = categoryList;
						if (that.articleList.length === 0) {
							that.fetchpostbymid(categoryList[0]['mid']);
						}
					}
				});
			},
			//获取文章
			fetchpostbymid(mid) {
				let that = this;
				Net.request({
					url: API.getPostsByMid(mid),
					showLoading: true,
					success: function(res) {
						var datas = res.data.data;
						if (datas != null && datas != undefined) {
							let articleList = [];
							datas.forEach(function(data) {
								let result = {};
								result.title = data.title;
								result.cid = data.cid;
								result.views = data.views;
								result.likes = data.likes;
								result.comments = data.commentsNum;
								result.time = API.getCreatedTime(data.created);
								articleList.push(result);
							});
							that.articleList = articleList;
						} else {
							wx.showToast({
								title: "该分类没有文章",
								icon: "none",
								duration: 2000
							});
						}
					}
				});
			},
			//进入文章
			openArticle(cid, title) {
				uni.navigateTo({
					url: '../post/post?cid=' + cid
				});
			}
		}
	}
</script>

<style>
</style>
