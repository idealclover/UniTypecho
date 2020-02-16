<template name="commentdetail">
	<view>
		<!-- 		<view class="cu-item" v-for="(item,index) in commentList" :key="index">
			<view class="content padding-tb-sm">
				<view class="padding-lr-xs">{{item.text}}</view>
			</view>
		</view> -->
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
					<!-- TODO: replay to reply -->
					<view class="padding-sm radius margin-top-sm" v-for="(citem,cindex) in item.replays" :key="cindex">
						<view>{{citem.author}}: </view>
						<uParse :content="citem.text" />
					</view>
					<view class="margin-top-sm flex justify-between">
						<view class="text-gray text-df">{{item.comcreatedtime}}</view>
						<view>
							<text class="cuIcon-messagefill text-gray margin-left-sm"></text>
						</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>
<script>
	import uParse from '@/static/libs/uParse/wxParse.vue'
	import API from '@/utils/api.js'
	import Net from '@/utils/net.js'
	import marked from 'marked'

	export default {
		components: {
			uParse
		},
		props: ['cid'],
		mounted() {
			if (typeof this.cid !== 'undefined') {
				this.getdetails(this.cid);
			}
		},
		watch: {
			cid: function(cid) {
				this.getdetails(cid)
			}
		},
		data() {
			return {
				// article: '加载中',
				commentList: []
			}
		},
		methods: {
			getdetails(cid) {
				let that = this;
				Net.request({
					url: API.GetPostsCommentbyCID(cid),
					success: function(res) {
						let commentList = res.data.data;
						that.commentList = commentList.map(function(item) {
							if (item.author == null || item.author == "undefined") {
								item.author = "游客";
							}
							if (item.authorImg == null || item.authorImg == "undefined") {
								item.authorImg = "http://secure.gravatar.com/avatar/";
							}
							item.comcreatedtime = API.getcreatedtime(item.created);
							return item;
						})
						// console.log(that.commentList);
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
</style>
