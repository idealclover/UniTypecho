<script>
	export default {
		onLaunch: function() {
			console.log('App Launch')
		},
		onShow: function() {
			console.log('App Show')
		},
		onHide: function() {
			console.log('App Hide')
		}
	}
</script>

<style>
	/*每个页面公共css */
	@import "libs/colorui/main.css";
	@import "libs/colorui/icon.css";
	@import "libs/uParse/parse.css";
	page{
	  padding-bottom: 100upx;
	}
	
	.wxParse .a {
	    color: blue;
	}
	
	.wxParse view .p view,
	.wxParse view .li view{
		display: inline;
	}
</style>
