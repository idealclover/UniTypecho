<template name="articledetail">
	<view>
		<image class="self-center" v-if="thumb != null" :src="thumb" mode="aspectFit" style="width: 100%;"></image>
		<view class="solid-bottom text-xxl padding-tb-sm">{{title}}</view>
		<view  v-if="link != null"class="text-gray ">Link: {{link}}</view>
		<view class="text-gray padding-tb-lg" v-if="showTools && views != null">
			<text class="cuIcon-attentionfill padding-lr-xs"></text>{{views}}
			<text class="cuIcon-likefill padding-lr-xs"></text> {{likes}}
			<text class="cuIcon-communityfill padding-lr-xs"></text> {{comments}}
			<text class="cuIcon-timefill padding-lr-xs"></text>{{time}}
		</view>
		<hr v-if="showTools" />
		<uParse :content="article" @preview="preview" @navigate="navigate" />
	</view>
</template>
<script>
	import uParse from '@/libs/uParse/parse.vue'
	import cfg from '@/config.js'
	import API from '@/utils/api.js'
	import Net from '@/utils/net.js'
	import Util from '@/utils/util.js'
	import marked from 'marked'

	export default {
		components: {
			uParse
		},
		props: ['cid', 'isPage', 'showTools'],
		mounted() {
			if (!Util.isNull(this.cid)) {
				this.getdetails(this.cid);
			}
		},
		watch: {
			cid: function(cid) {
				if (this.isPage) this.getdetails(cid);
			}
		},
		data() {
			return {
				article: '加载中',
				title: '',
				views: null,
				likes: null,
				comments: null,
				time: null,
				thumb: null,
				link: null
			}
		},
		methods: {
			getdetails(cid) {
				if (Util.isNull(cid)) return;
				let that = this;
				Net.request({
					url: that.isPage ? API.getPagesByCid(cid) : API.getPostsByCid(cid),
					showLoading: true,
					success: function(res) {
						let datas = res.data.data;
						if (datas.length !== 0) {
							let item = API.parsePost(datas[0]);
							let text = item.text.replace(new RegExp('!!!', 'g'), '');
							that.article = marked(text);
							that.time = API.getCreatedTime(item.created);
							that.comments = item.commentsNum;
							that.views = item.views;
							that.likes = item.likes;
							that.title = item.title;
							that.thumb = item.thumb.type == 'self' ? item.thumb.url : null;
							that.link = item.link;
							that.$emit('getInfo', {
								"title": item.title,
								"thumb": item.thumb.url
							});
						}
					},
				});
			},
			preview(src, e) {
				// do something
				console.log(src);
			},
			navigate(href, e) {
				// do something
				let re = new RegExp("^https:\/\/" + cfg.getdomain + "\/archives\/([0-9]*)\/?");
				let str = href.match(re);
				if(!Util.isNull(str)) {
					uni.navigateTo({
						url: '/pages/post/post?cid=' + str[1]
					});
					return;
				}
				// #ifdef H5
				window.open(href);
				// #endif
				// #ifdef APP-PLUS
				plus.runtime.openURL(href, function(res) {
				});
				// #endif
				// #ifdef MP
				uni.setClipboardData({
					data: href,
					success: function(){
						uni.showToast({
						    title: '链接已复制',
						    duration: 2000
						});
					}
				});
				// #endif
			}
		}
	}
</script>
<style>
</style>
