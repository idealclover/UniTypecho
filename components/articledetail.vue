<template name="articledetail">
	<view>
		<uParse :content="article" @preview="preview" @navigate="navigate" />
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
		props: ['cid', 'isPage'],
		mounted() {
			if (typeof this.cid !== 'undefined') {
				this.getdetails(this.cid);
			}
		},
		// onReady: this.getdetails(cid),
		watch: {
			cid: function(cid) {
				this.getdetails(cid)
			}
		},
		data() {
			return {
				article: '加载中',
			}
		},
		methods: {
			getdetails(cid) {
				console.log(cid);
				console.log(typeof cid);
				if (typeof cid == 'undefined' || cid == null) return;
				let that = this;
				Net.request({
					url: that.isPage ? API.GetPagebyCID(cid) : API.GetPostsbyCID(cid),
					success: function(res) {
						let datas = res.data.data;
						if (datas.length !== 0) {
							let item = API.ParseItem(datas[0]);
							console.log(item)
							that.article = marked(item.text);
						}


						// that.data.createdtime = API.getcreatedtime(parsed_item.created);
						// that.setData({
						//   createdtime: that.data.createdtime
						// });
					},
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
