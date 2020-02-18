<template name="commentdetail">
	<view>
		<view class="cu-bar">
			<view class="action sub-title">
				<text class="text-xl text-bold text-green">{{likeNum}}人赞</text>
				<text class="bg-green" style="width:2rem"></text>
			</view>
		</view>
		<scroll-view scroll-x="true" scroll-with-animation>
			<!-- <view class="cu-item" :class="index==TabCur?'text-green cur':''" v-for="(item,index) in 10" :key="index" @tap="tabSelect" :data-id="index">
				Tab{{index}}
			</view> -->
			<view class="cu-avatar-group">
				<view class="cu-avatar round lg" v-for="(item,index) in avatarList" :key="index" :style="[{ backgroundImage:'url(' + item + ')' }]"></view>
			</view>
		</scroll-view>
		<view class="cu-bar">
			<view class="action sub-title">
				<text class="text-xl text-bold text-green">评论</text>
				<text class="bg-green" style="width:2rem"></text>
			</view>
		</view>
		<view class="cu-list menu-avatar comment solids-top">
			<view class="cu-item" v-for="(item,index) in commentList" :key="index">
				<view class="cu-avatar round" :style="'background-image:url(' + item.authorImg + ');'"></view>
				<view class="content">
					<view class="text-grey">{{item.author}}</view>
					<view class="text-gray text-content text-df" style="word-break: break-all;">
						<uParse :content="item.text" />
					</view>
					<view class="padding-sm radius margin-top-sm" v-for="(citem,cindex) in item.replay" :key="cindex">
						<view>{{citem.author}}: </view>
						<uParse :content="citem.text" />
					</view>
					<view class="margin-top-sm flex justify-between">
						<view class="text-gray text-df">{{item.createdtime}}</view>
						<!-- TODO: reply -->
						<!-- <view>
							<text class="cuIcon-messagefill text-gray margin-left-sm"></text>
						</view> -->
					</view>
				</view>
			</view>
		</view>
	</view>
</template>
<script>
	import uParse from '@/libs/uParse/parse.vue'
	import API from '@/utils/api.js'
	import Net from '@/utils/net.js'
	import Util from '@/utils/util.js'
	import marked from 'marked'

	export default {
		components: {
			uParse
		},
		props: ['cid', 'isPage', 'refresh'],
		mounted() {
			if (!Util.isNull(this.cid)) {
				this.getdetails(this.cid);
			}
		},
		watch: {
			cid: function(cid) {
				if(this.isPage) this.getdetails(cid);
			},
			refresh: function() {
				this.getdetails(this.cid);
			}
		},
		data() {
			return {
				avatarList: [],
				commentList: [],
				likeNum: "",
				likeList: []
			}
		},
		methods: {
			getdetails(cid) {
				if(Util.isNull(cid)) return;
				let that = this;
				Net.request({
					url: API.getLikedNum(cid),
					showLoading: true,
					success: function(res) {
						that.likeNum = res.data.data[0]['likes'];
						Net.request({
							url: API.getLikedList(cid),
							success: function(res) {
								let datas = res.data.data;
								let avatarList = [];
								for(let i in datas){
									let avatarUrl = datas[i]['avatarUrl'];
									avatarList.push(avatarUrl);
								}
								let leftAvatars = that.likeNum - avatarList.length
								for(let i = 0; i < leftAvatars; i++){
									// #ifdef APP-PLUS
									avatarList.push('static/images/default.jpg');
									// #endif
									// #ifndef APP-PLUS
									avatarList.push('/static/images/default.jpg');
									// #endif
								};
								avatarList.reverse();
								that.avatarList = avatarList;
							}
						});
					}
				});

				Net.request({
					url: API.getCommentsByCid(cid),
					showLoading: true,
					success: function(res) {
						let commentList = res.data.data;
						that.commentList = commentList.map(function(item) {
							if (Util.isNull(item.author)) {
								item.author = "游客";
							}
							if (Util.isNull(item.authorImg)) {
								// #ifdef APP-PLUS
								item.authorImg = "static/images/default.jpg";
								// #endif
								// #ifndef APP-PLUS
								item.authorImg = "/static/images/default.jpg";
								// #endif
							}
							item.createdtime = API.getCreatedTime(item.created);
							return item;
						})
					}
				});
			},
			preview(src, e) {
				// do something
			},
			navigate(href, e) {
				// do something
			}
		}
	}
</script>
<style>
	.cu-bar .action:first-child {
		margin-left: 0;
	}
	.cu-avatar-group {
		white-space: nowrap;
	}
</style>
