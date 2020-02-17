<template name="footerinfo">
	<view class="tm-every-center flex-direction text-sm line-sub justify-center">
		<view class="margin-sm text-grey text-center">{{domain}}</view>
		<view class="margin-sm text-grey text-center">Made with ❤ by idealclover.</view>
		<view class="margin-sm text-grey text-center">UniTypecho based on UniAPP.</view>
		<view class="margin-sm text-grey text-center">@2017-2020 {{name}}</view>
	</view>
</template>

<script>
	import cfg from '@/config.js';
	import API from '@/utils/api.js';
	export default {
		data() {
			return {
				domain: API.getDomain(),
				name: cfg.getname
			};
		}
	}
</script>

<style>

</style>
