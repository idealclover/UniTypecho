<template>
	<view>
		<list v-if="PageCur=='list'"></list>
		<rank v-if="PageCur=='rank'"></rank>
		<about v-if="PageCur=='about'"></about>
		<view class="cu-bar tabbar bg-white shadow foot">
			<view class="action" @click="NavChange" data-cur="list">
				<view><text class="justify-center" :class="PageCur=='list'?'cuIcon-homefill':'cuIcon-home'"></text></view>
				<view :class="PageCur=='list'?'text-green':'text-gray'">首页</view>
			</view>
			<view class="action" @click="NavChange" data-cur="rank">
				<view><text class="justify-center" :class="PageCur=='rank'?'cuIcon-hotfill':'cuIcon-hot'"></text></view>
				<view :class="PageCur=='rank'?'text-green':'text-gray'">排行</view>
			</view>
			<view class="action" @click="NavChange" data-cur="about">
				<view><text class="justify-center" :class="PageCur=='about'?'cuIcon-myfill':'cuIcon-my'"></text></view>
				<view :class="PageCur=='about'?'text-green':'text-gray'">关于</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				PageCur: 'list'
			}
		},
		methods: {
			NavChange: function(e) {
				this.PageCur = e.currentTarget.dataset.cur
			}
		}
	}
</script>

<style>
</style>
