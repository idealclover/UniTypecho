<template name="about">
	<view>
		<view class="UCenter-bg">
			<image src="/static/images/logo.png" class="png" mode="widthFix"></image>
			<view class="text-xl">{{name}}</view>
			<image src="/static/images/wave.gif" mode="scaleToFill" class="gif-wave"></image>
		</view>
		<view class="margin-xl">
			<articledetail :cid="cid" :isPage="true" />
			<commentdetail :cid="cid" />
		</view>
	</view>
</template>

<script>
	import articledetail from '@/components/articledetail.vue'
	import commentdetail from '@/components/commentdetail.vue'
	import cfg from "@/config.js";
	import API from '@/utils/api.js'
	import Net from '@/utils/net.js'

	export default {
		mounted() {
			this.getabout();
		},
		components: {
			articledetail,
			commentdetail
		},
		data() {
			return {
				cid: null,
				name: cfg.getname
			}
		},
		methods: {
			getabout() {
				let that = this;
				let content = ``
				Net.request({
					url: API.GetAboutCid(),
					success: function(res) {
						var datas = res.data.data;
						if (API.IsNull(datas)) {
							if (datas != "none") {
								// that.article = marked(datas);
								that.cid = datas;
								// that.getdetails(datas);
							}
						}
					}
				});
			},
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
