<template>
	<view>
		<view class="margin-xl">
			<articledetail :cid="cid" :isPage="false" :title="title" />
			<commentdetail :cid="cid" />
		</view>
		<commentsender />
	</view>
</template>

<script>
	import articledetail from '@/components/articledetail.vue';
	import commentdetail from '@/components/commentdetail.vue';
	import commentsender from '@/components/commentsender.vue';
	export default {
		data() {
			return {
				cid: null,
				title: null
			}
		},
		components:{
			articledetail,
			commentdetail,
			commentsender
		},
		onLoad: function (option) {
			uni.setNavigationBarTitle({
			    title: option.title
			});
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
