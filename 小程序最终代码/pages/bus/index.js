var api = require('../../api.js');
export default Page({
	data: {
		'__code__': {
			readme: ''
		},

		notice: '购票须知：包裹类型划分如下，小件：0--2KG；中件：2KG——4KG；大件：4KG——6KG，超过范围依此类推补差价。',
		userInfo: '',
		isLogin: false,
		today: '',
		currentSchool: '',
		start_site: '',
		end_site: '',
		limit: 20,
		isBottom: false,
		activeMenu: 1,
		startSites: '',
		timeIndex: 0,
		timeInternal: [{
			index: 0,
			time: '08:00-12:00'
		}, {
			index: 1,
			time: '12:00-16:00'
		}, {
			index: 2,
			time: '16:00-20:00'
		}, {
			index: 3,
			time: '20:00-24:00'
		}],
		menuList: [{
			imageSrc: 'https://www.ibwei.com/deliverbus/images/time.png',
			selectedSrc: 'https://www.ibwei.com/deliverbus/images/time_selected.png',
			title: '从早到晚',
			isSelected: true
		}, {
			imageSrc: 'https://www.ibwei.com/deliverbus/images/siteicon.png',
			selectedSrc: 'https://www.ibwei.com/deliverbus/images/siteicon_selected.png',
			title: '始发站点',
			isSelected: false
		}, {
			imageSrc: 'https://www.ibwei.com/deliverbus/images/sx.png',
			selectedSrc: 'https://www.ibwei.com/deliverbus/images/sx_selected.png',
			title: '发车时间',
			isSelected: false
		}],
		busList: ''
	},
	onLoad: function (option) {
		var that = this;
		//console.log(option)
		this.setData({
			start_site: option.start,
			end_site: option.end
		});
		let isLogin = wx.getStorageSync('isLogin');
		this.setData({
			isLogin: isLogin
		});
		let today = wx.getStorageSync('today');
		this.setData({
			today: today
		});
		let currentSchool = wx.getStorageSync('currentSchool');
		this.setData({
			currentSchool: currentSchool
		});
		let startSites = wx.getStorageSync('startSites');
		this.setData({
			startSites: startSites
		});
		wx.request({
			url: api.bus.searchBus1,
			method: 'POST',
			data: {
				'__code__': {
					readme: ''
				},

				start_site: this.data.start_site,
				end_site: this.data.end_site,
				school_id: this.data.currentSchool.value,
				limit: this.data.limit
			},
			success: function (res) {
				//console.log(res.data)
				if (res.data.length > 0) {
					let bus = res.data;
					for (let i = 0; i < bus.length; i++) {
						bus[i].start_time = bus[i].start_time.substr(11, 5);
						bus[i].end_time = bus[i].end_time.substr(11, 5);
						bus[i].count_time = that.countTime(bus[i].start_time, bus[i].end_time);
					}
					console.log(bus);
					that.setData({
						busList: bus
					});
				} else if (res.data == 0) {
					that.setData({
						busList: ''
					});
				} else {
					that.setData({
						busList: ''
					});
					//console.log('未知网络错误')
				}
			},
			fail: function () {
				//console.log('获取数据失败,请稍后再试')
				wx.showToast({
					title: '通信异常,请稍后再试',
					icon: 'none',
					duration: 1500
				});
			}
		});
	},
	countTime(start_time, end_time) {
		let hour1 = parseInt(start_time.substr(0, 2));
		let hour2 = parseInt(end_time.substr(0, 2));
		let minute1 = parseInt(start_time.substr(3, 2));
		let minute2 = parseInt(end_time.substr(3, 2));
		let hour = hour2 - hour1;
		if (minute2 > minute1) {
			var minute = minute2 - minute1;
		} else {
			hour -= 1;
			var minute = minute2 + 60 - minute1;
			if (minute == 60) {
				minute = 0, hour += 1;
			}
		}
		return hour + '时' + minute + '分';
	},
	bindPickerChange: function (e) {
		//console.log('picker发送选择改变，携带值为', this.data.startSites[e.detail.value])
		this.setData({
			index11: e.detail.value,
			start_site: this.data.startSites[e.detail.value].title
		});
		this.searchBus3();
	},
	bindPickerChange1: function (e) {
		//console.log('picker发送选择改变，携带值为' + e.detail.value)
		this.setData({
			index: e.detail.value,
			timeIndex: e.detail.value
		});
		this.searchBus4();
	},
	changeSearch(e) {
		let index = e.currentTarget.dataset.index;
		//var isSelected = 'menuList[' + index + '].isSelected'
		for (let i = 0; i < this.data.menuList.length; i++) {
			let isSelected = 'menuList[' + i + '].isSelected';
			if (index == i) {
				this.setData({
					[isSelected]: true
				});
			} else {
				this.setData({
					[isSelected]: false
				});
			}
		}
		this.setData({
			activeMenu: index
		});
		if (index == 0) {
			this.searchBus2();
		} else if (index == 1) {
			this.showSitePopup();
		} else {
			this.showTimePopup();
		}
	},
	showTimePopup() {
		//console.log('show time')
		let popupComponent = this.selectComponent('.time-popup');
		popupComponent && popupComponent.show();
	},
	showSitePopup() {
		//console.log('show time')
		let popupComponent = this.selectComponent('.site-popup');
		popupComponent && popupComponent.show();
	},
	searchBus2() {
		var that = this;
		wx.request({
			url: api.bus.searchBus2,
			method: 'POST',
			data: {
				'__code__': {
					readme: ''
				},

				school_id: this.data.currentSchool.value,
				limit: this.data.limit
			},
			success: function (res) {
				if (res.data.length > 0) {
					let bus = res.data;
					for (let i = 0; i < bus.length; i++) {
						bus[i].start_time = bus[i].start_time.substr(11, 5);
						bus[i].end_time = bus[i].end_time.substr(11, 5);
						bus[i].count_time = that.countTime(bus[i].start_time, bus[i].end_time);
					}
					console.log(bus);
					that.setData({
						busList: bus
					});
				} else if (res.data == 0) {
					that.setData({
						busList: ''
					});
				} else {
					that.setData({
						busList: ''
					});
					//console.log('未知网络错误')
				}
			},
			fail: function () {
				//console.log('获取数据失败,请稍后再试')
				wx.showToast({
					title: '通信异常,请稍后再试',
					icon: 'none',
					duration: 1500
				});
			}
		});
	},
	searchBus3() {
		var that = this;
		wx.request({
			url: api.bus.searchBus3,
			method: 'POST',
			data: {
				'__code__': {
					readme: ''
				},

				school_id: this.data.currentSchool.value,
				start_site: this.data.start_site,
				limit: this.data.limit
			},
			success: function (res) {
				if (res.data.length > 0) {
					let bus = res.data;
					for (let i = 0; i < bus.length; i++) {
						bus[i].start_time = bus[i].start_time.substr(11, 5);
						bus[i].end_time = bus[i].end_time.substr(11, 5);
						bus[i].count_time = that.countTime(bus[i].start_time, bus[i].end_time);
					}
					console.log(bus);
					that.setData({
						busList: bus
					});
				} else if (res.data == 0) {
					that.setData({
						busList: ''
					});
				} else {
					that.setData({
						busList: ''
					});
					//console.log('未知网络错误')
				}
			},
			fail: function () {
				//console.log('获取数据失败,请稍后再试')
				wx.showToast({
					title: '通信异常,请稍后再试',
					icon: 'none',
					duration: 1500
				});
			}
		});
	},
	searchBus4() {
		var that = this;
		console.log(this.data.timeIndex);
		wx.request({
			url: api.bus.searchBus4,
			method: 'POST',
			data: {
				'__code__': {
					readme: ''
				},

				school_id: this.data.currentSchool.value,
				time_index: this.data.timeIndex,
				limit: this.data.limit
			},
			success: function (res) {
				if (res.data.length > 0) {
					let bus = res.data;
					for (let i = 0; i < bus.length; i++) {
						bus[i].start_time = bus[i].start_time.substr(11, 5);
						bus[i].end_time = bus[i].end_time.substr(11, 5);
						bus[i].count_time = that.countTime(bus[i].start_time, bus[i].end_time);
					}
					console.log(bus);
					that.setData({
						busList: bus
					});
				} else if (res.data == 0) {
					that.setData({
						busList: ''
					});
				} else {
					that.setData({
						busList: ''
					});
					//console.log('未知网络错误')
				}
			},
			fail: function () {
				//console.log('获取数据失败,请稍后再试')
				wx.showToast({
					title: '通信异常,请稍后再试',
					icon: 'none',
					duration: 1500
				});
			}
		});
	},
	noTicket() {
		wx.showToast({
			title: '该类型车票已售完',
			icon: 'none',
			duration: 1000
		});
	},
	navToDriver() {
		if (!this.data.isLogin) {
			wx.showToast({
				title: '请先登录',
				icon: 'none',
				duration: 1500
			});
			return false;
		}
		wx.navigateTo({
			url: '/pages/driver/index'
		});
	},
	navToStartBus() {
		if (!this.data.isLogin) {
			wx.showToast({
				title: '请先登录',
				icon: 'none',
				duration: 1500
			});
			return false;
		}
		wx.navigateTo({
			url: '/pages/newbus/index'
		});
	},
	//票面底部切换,动态修改数组对象某一具体属性
	changeBottom(e) {
		//console.log('finished')
		var that = this;
		var index = e.currentTarget.dataset.index;
		var bShow = 'busList[' + index + '].bottomShow';
		//console.log(bShow)
		//console.log(that.data.busList[index].bottomShow)
		that.setData({
			[bShow]: !that.data.busList[index].bottomShow
		});
		//console.log(that.data.busList[index].bottomShow)
	},
	toBuy(e) {
		if (!this.data.isLogin) {
			wx.showToast({
				title: '还未登录,请先去登录',
				icon: 'none',
				duration: 1500
			});
		} else {
			let index = e.currentTarget.dataset.index;
			let type = e.currentTarget.dataset.type;
			let price = e.currentTarget.dataset.price;
			wx.setStorageSync('ticketInfo', this.data.busList[index]);
			wx.navigateTo({
				url: '/pages/order/index?type=' + type + '&price=' + price
			});
		}
	}

});