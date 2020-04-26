<template name="footerinfo">
	<view class="tm-every-center flex-direction text-sm line-sub justify-center">
		<view class="margin-sm text-grey text-center" @click="copy(domain)">{{domain}}</view>
		<view class="margin-sm text-grey text-center" @click="copy('https://github.com/idealclover/UniTypecho')">Powered by UniTypecho.</view>
		<view class="margin-sm text-grey text-center">Made with ❤ by idealclover.</view>
		<view class="margin-sm text-grey text-center">©2020 {{name}}</view>
	</view>
</template>

<script>
	import cfg from '@/static/config.js';
	import API from '@/utils/api.js';
	export default {
		data() {
			return {
				domain: "https://idealclover.top",
				name: cfg.getname
			};
		},
		methods: {
			copy(href) {
				// #ifdef H5
				window.open(href);
				// #endif
				// #ifdef APP-PLUS
				plus.runtime.openURL(href, function(res) {});
				// #endif
				// #ifdef MP
				uni.setClipboardData({
					data: href,
					success: function() {
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
