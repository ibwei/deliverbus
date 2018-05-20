var api = require('../../api.js');
var app = getApp().globalData;
export default Page({
	data: {
		'__code__': {
			readme: ''
		},

		driver: '',
		isLogin: false,
		isDriver: '',
		user: '',
		userInfo: '',
		busList: [{
			title: '我要发车',
			desc: '发布你的bus路线',
			slot: false,
			src: 'https://www.ibwei.com/deliverbus/images/bus_icon.png',
			navUrl: '/pages/newbus/index'
		}, {
			title: '我的乘客',
			desc: '',
			slot: false,
			src: 'https://www.ibwei.com/deliverbus/images/chengke.png',
			navUrl: '/pages/passenger/index'
		}, {
			title: '申请老司机',
			desc: '',
			slot: false,
			src: 'https://www.ibwei.com/deliverbus/images/driver.png',
			navUrl: '/pages/driver/index'
		}],
		userList: [{
			title: '我的车票',
			slot: false,
			src: 'https://www.ibwei.com/deliverbus/images/price.png',
			navUrl: '/pages/myticket/index'
		}, {
			title: '我的地址',
			desc: '',
			slot: false,
			src: 'https://www.ibwei.com/deliverbus/images/location.png',
			navUrl: '/pages/address/index'
		}, {
			title: '我的优惠券',
			desc: '你有一张优惠券可用',
			slot: false,
			src: 'https://www.ibwei.com/deliverbus/images/coupon_icon.png',
			navUrl: '/pages/coupon/index'
		}],
		feedbackList: [{
			title: '客服与帮助',
			desc: '',
			slot: false,
			src: 'https://www.ibwei.com/deliverbus/images/wechatHL.png'
		}, {
			title: '意见反馈',
			desc: '',
			slot: false,
			src: 'https://www.ibwei.com/deliverbus/images/feedback_icon.png',
			navUrl: '/pages/message/index'
		}]
	},
	onLoad() {
		let isLogin = wx.getStorageSync('isLogin');
		if (isLogin) {
			let userInfo = wx.getStorageSync('userInfo');
			let user = wx.getStorageSync('user');
			this.setData({
				isLogin: true,
				userInfo: userInfo,
				user: user
			});
			this.isDriver1();
		}
	},
	isDriver1() {
		var that = this;
		wx.request({
			url: api.user.isDriver,
			data: {
				'__code__': {
					readme: ''
				},

				user_id: this.data.user.data.uid
			},
			method: 'POST',
			success: function (res) {
				console.log(res.data);
				if (res.data !== 'wait' && res.data !== 'no' && res.data !== 'dont') {
					that.setData({
						isDriver: true,
						driver: res.data
					});
					wx.setStorageSync('isDriver', true);
					wx.setStorageSync('driver', res.data);
				} else {
					that.setData({
						isDriver: false
					});
					wx.setStorageSync('isDriver', false);
				}
			}
		});
	},
	//跳转到相应的子页面
	navToDetail(e) {
		if (!this.data.isLogin) {
			wx.showToast({
				title: '请先登录',
				icon: 'none',
				duration: 2000
			});
			return false;
		}
		//console.log(e.currentTarget.dataset.index)
		let index = e.currentTarget.dataset.index;
		let navUrl = this.data.userList[index].navUrl;
		wx.navigateTo({
			url: navUrl
		});
	},
	navToDriver(e) {
		if (!this.data.isLogin) {
			wx.showToast({
				title: '请先登录',
				icon: 'none',
				duration: 2000
			});
			return false;
		}
		let index = e.currentTarget.dataset.index;
		console.log(index);
		if (index == 0 || index == 1) {
			if (!this.data.isDriver) {
				wx.showToast({
					title: '您还不是老司机或处于申请中',
					icon: 'none',
					duration: 1500
				});
				return false;
			}
		}
		if (index == 2) {
			if (this.data.isDriver) {
				wx.showToast({
					title: '您已经是老司机了',
					icon: 'none',
					duration: 1500
				});
				return false;
			}
		}
		let navUrl = this.data.busList[index].navUrl;

		wx.navigateTo({
			url: navUrl
		});
	},
	navToFeed(e) {
		let index = e.currentTarget.dataset.index;
		if (!index) {
			this.showToast();
		} else if (!this.data.isLogin) {
			wx.showToast({
				title: '请先登录',
				icon: 'none',
				duration: 2000
			});
		} else {
			let navUrl = this.data.feedbackList[index].navUrl;
			wx.navigateTo({
				url: navUrl
			});
		}
	},
	showToast() {
		let $toast = this.selectComponent(".J_toast");
		$toast && $toast.show('该功能暂未启用');
	},
	showToast1() {
		let $toast = this.selectComponent(".login_toast");
		$toast && $toast.show('登录成功');
	},
	bindGetUserInfo: function (e) {
		let encryptedData = e.detail.encryptedData;
		let iv = e.detail.iv;
		let myInfo = e.detail.userInfo;
		this.setData({
			userInfo: myInfo
		});
		wx.getStorageSync('userInfo', myInfo);
		this.userLogin(encryptedData, iv, myInfo);
		//console.log(this.data.userInfo)
	},
	userLogin(encryptedData, iv, myInfo) {
		var that = this;
		wx.login({
			success: function (res) {
				console.log(res);
				if (res.code) {
					//发起网络请求
					//console.log(res.code + encryptedData + iv + myInfo)
					wx.request({
						url: api.user.login,
						method: 'POST',
						data: {
							'__code__': {
								readme: ''
							},

							code: res.code,
							encryptedData: encryptedData,
							iv: iv,
							userInfo: myInfo
						},
						header: {
							'content-type': 'application/x-www-form-urlencoded'
						},
						success: function (ret) {
							if (ret.data.code == 200) {
								wx.setStorageSync('user', ret.data);
								wx.setStorageSync('userInfo', myInfo);
								wx.setStorageSync('isLogin', true);
								wx.setStorageSync('isDriver', false);
								that.setData({
									isLogin: true,
									userInfo: myInfo,
									user: ret.data
								});
								that.isDriver1();
								that.showToast1();
							} else {
								that.showToast1('登录失败');
							}
						},
						fail: function () {
							wx.showToast({
								title: '服务器异常,请稍后登录',
								icon: 'none',
								duration: 2000
							});
						}
					});
				} else {
					wx.showToast({
						title: '未知异常,请稍后登录',
						icon: 'none',
						duration: 2000
					});
				}
			}
		});
	}
});