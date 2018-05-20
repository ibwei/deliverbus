var api = require('../../api.js');
export default Page({
  data: {
    '__code__': {
      readme: ''
    },

    price: '',
    ticketInfo: '',
    user: '',
    userInfo: '',
    type: '',
    count_time: '',
    typeList: ['小件', '中件', '大件'],
    addressList: '',
    addressDetail: '',
    address_id: '',
    deliver_number: '',
    express_name: '',
    consignee_name: '',
    consignee_tel: ''
  },
  onLoad(option) {
    console.log(option);
    let type = option.type;
    let price = option.price;
    let ticketInfo = wx.getStorageSync('ticketInfo');
    this.setData({
      ticketInfo: ticketInfo
    });
    let userInfo = wx.getStorageSync('userInfo');
    let user = wx.getStorageSync('user');
    let count_time = this.countTime(ticketInfo.start_time, ticketInfo.end_time);
    this.setData({
      user: user,
      type: type,
      userInfo: userInfo,
      count_time: count_time,
      price: price
    });
    this.getMyAddressList();
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
  getMyAddressList() {
    var that = this;
    let addressList = wx.getStorageSync('addressList');
    if (addressList.length) {
      this.setData({
        addressList: addressList
      });
    } else {
      wx.request({
        url: api.user.getMyAddresses,
        method: 'POST',
        header: {
          'content-type': 'application/x-www-form-urlencoded'
        },
        data: {
          '__code__': {
            readme: ''
          },

          user_id: this.data.user.data.uid
        },
        success: function (res) {
          console.log(res.data);
          if (res.data.length) {
            that.setData({
              addressList: res.data
            });
            wx.setStorageSync('addressList', res.data);
          } else {
            wx.showToast({
              title: '没有收获地址,先去填收获地址吧',
              icon: 'none',
              duration: 1500
            });
          }
          setTimeout(function () {
            wx.navigateTo({
              url: '/pages/address/index'
            });
          }, 2000);
        },
        fail: function () {
          wx.showToast({
            title: '获取收获地址失败',
            icon: 'none',
            duration: 1500
          });
        }
      });
    }
  },
  saveConsigneeName(e) {
    this.setData({
      consignee_name: e.detail.value
    });
  },
  saveConsigneeTel(e) {
    this.setData({
      consignee_tel: e.detail.value
    });
  },
  saveExpressName(e) {
    this.setData({
      express_name: e.detail.value
    });
  },
  saveDeliverNumber(e) {
    this.setData({
      deliver_number: e.detail.value
    });
    console.log(this.data);
  },
  showPopup() {
    let popupComponent = this.selectComponent('.J_Popup');
    popupComponent && popupComponent.show();
  },
  hidePopup() {
    let popupComponent = this.selectComponent('.J_Popup');
    popupComponent && popupComponent.hide();
  },
  onChange(e) {
    var index = e.detail.value;
    for (let i = 1; i < this.data.addressList.length; i++) {
      if (this.data.addressList[i].value === index) {
        var address = this.data.addressList[i].title;
      }
    }
    this.setData({
      addressDetail: address
    });
  },
  toPay() {
    var that = this;
    if (this.data.consignee_tel && this.data.consignee_name && this.data.express_name && this.data.deliver_number) {
      wx.request({
        url: api.order.pay,
        method: 'POST',
        data: {
          '__code__': {
            readme: ''
          },

          bus_id: this.data.ticketInfo.id,
          user_id: this.data.user.data.uid,
          address_id: this.data.address_id,
          type: this.data.type,
          price: this.data.price,
          deliver_number: this.data.deliver_number,
          express_name: this.data.express_name,
          consignee_name: this.data.consignee_name,
          consignee_tel: this.data.consignee_tel
        },
        success: function (res) {
          console.log(res.data);
          if (res.data) {
            wx.navigateTo({
              url: '/pages/pay/index?price=' + that.data.price + '&trade_sn=' + res.data
            });
          } else {
            wx.showToast({
              title: '提交失败,请稍后再试',
              icon: 'none',
              duration: 1500
            });
          }
        }
      });
    } else {
      wx.showToast({
        title: '请输入所有的必填项',
        icon: 'none',
        duration: 1500
      });
      return false;
    }
  },
  bindPickerChange: function (e) {
    console.log(this.data.addressList);
    console.log('picker发送选择改变，携带值为', this.data.addressList[e.detail.value].id);
    this.setData({
      index: e.detail.value,
      address_id: this.data.addressList[e.detail.value].id
    });
  }
});