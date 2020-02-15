<template name="about">
	<view class="margin-xl">
		<articledetail :cid="cid" :isPage="true" />
		<commentdetail :cid="cid" />
	</view>
</template>

<script>
	import articledetail from '@/components/articledetail.vue'
	import commentdetail from '@/components/commentdetail.vue'
	import API from '@/utils/api.js'
	import Net from '@/utils/net.js'
	
	export default {
		mounted() {
			this.getabout();
		},
		components:{
			articledetail,
			commentdetail
		},
		data() {
			return {
				cid: null,
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

</style>
