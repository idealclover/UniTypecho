<template>
	<view>
		<view class="margin-xl">
			<articledetail :cid="cid" :isPage="false" :showTools="true" @getInfo="getInfo" />
			<commentdetail :cid="cid" :isPage="false" :refresh="refreshComments" v-if="showComments" />
			<footerinfo />
		</view>
		<commentsender :cid="cid" :isPage="false" :title="title" :thumb="thumb" @onRefreshComments="onRefreshComments" v-if="showComments" />
		<canvas style="position: absolute; top: -3000px; left: -3000px; width: 2000px; height: 2000px; background: #000"
		 canvas-id="canvas"></canvas>
	</view>
</template>

<script>
	import articledetail from '@/components/articledetail.vue';
	import commentdetail from '@/components/commentdetail.vue';
	import footerinfo from '@/components/footerinfo.vue';
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
	import commentsender from '@/components/commentsender/tt.vue';
	// #endif
	export default {
		data() {
			return {
				showComments: false,
				refreshComments: false,
				cid: null,
				title: "",
				thumb: null,
				cutThumb: null,
				isShowThumb: false
			}
		},
		components: {
			articledetail,
			commentdetail,
			commentsender,
			footerinfo
		},
		onLoad: function(option) {
			this.showComments = getApp().globalData.showComments;
			this.cid = option.cid;
		},
		methods: {
			getInfo(e) {
				this.title = e.title;
				this.thumb = e.thumb;
				if (!this.isPage) {
					uni.setNavigationBarTitle({
						title: e.title
					});
				}
				// console.log(e.thumb);
				// this.cutImg(e.thumb);
			},
			onRefreshComments() {
				this.refreshComments = !this.refreshComments;
			},
			// cutImg(thumb) {
			// 	console.log(thumb);
			// 	let that = this;
			// 	uni.getImageInfo({
			// 		src: thumb,
			// 		success: (res) => {
			// 			// 'canvas'为前面创建的canvas标签的canvas-id属性值
			// 			let ctx = uni.createCanvasContext('canvas');
			// 			let canvasW = 640,
			// 				canvasH = res.height;
			// 			if (res.width / res.height > 5 / 4) { // 长宽比大于5:4
			// 				canvasW = res.height * 5 / 4;
			// 			}
			// 			console.log(canvasW);
			// 			console.log(canvasH);
			// 			// 将图片绘制到画布
			// 			ctx.drawImage(res.path, (res.width - canvasW) / 2, 0, canvasW, canvasH, 0, 0, canvasW, canvasH)
			// 			// draw()必须要用到，并且需要在绘制成功后导出图片
			// 			ctx.draw(false, () => {
			// 				uni.canvasToTempFilePath({
			// 					width: canvasW,
			// 					height: canvasH,
			// 					destWidth: canvasW,
			// 					destHeight: canvasH,
			// 					canvasId: 'canvas',
			// 					fileType: 'jpg',
			// 					success: (res) => {
			// 						that.cutThumb = res.tempFilePath
			// 						// res.tempFilePath为导出的图片路径
			// 					}
			// 				})
			// 			})
			// 		}
			// 	});
			// }
		},
		onShareAppMessage: function() {
			// #ifdef MP-QQ
			qq.showShareMenu({
				showShareItems: ['qq', 'qzone', 'wechatFriends', 'wechatMoment']
			});
			// #endif		
			// console.log(this.cutThumb);
			return {
				title: this.title,
				path: '/pages/index/index?cid=' + this.cid,
				// imageUrl: this.cutThumb
			}
		}
	}
</script>

<style>

</style>
