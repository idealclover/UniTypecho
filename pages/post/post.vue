<template>
	<view class="margin-xl">
		<articledetail :cid="cid" :isPage="false" />
		<commentdetail :cid="cid" />
	</view>
</template>

<script>
	import articledetail from '@/components/articledetail.vue'
	import commentdetail from '@/components/commentdetail.vue'
	export default {
		data() {
			return {
				cid: null,
				title: null
			}
		},
		components:{
			articledetail,
			commentdetail
		},
		onLoad: function (option) {
			this.cid = option.cid;
			this.title = option.title;
			this.fetchPost();
		},
		methods: {
			fetchPost() {
				
			}
		}
	}
</script>

<style>

</style>
