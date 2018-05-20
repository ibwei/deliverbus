var api = require('../../api.js');
export default Page({
  data: {
    ticketList: '',
    user: ''
  },
  onLoad(option) {
    var that = this;
    let user = wx.getStorageSync('user');
    let bus_id = option.id;
    // console.log(bus_id);
    this.setData({
      user: user
    });
    wx.request({
      url: api.driver.passengerDetail,
      method: 'POST',
      data: {
        bus_id: bus_id
      },
      header: {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + wx.getStorageSync('user').data.token
      },
      success: function (res) {
        console.log(res.data);
        if (res.data.length) {
          let bus = res.data;
          for (var i = 0; i < bus.length; i++) {
            if (bus[i].type == 1) {
              bus[i].type = '小件';
            } else if (bus[i].type == 2) {
              bus[i].tupe = '中件';
            } else if (bus[i].type == 3) {
              bus[i].type = '大件';
            }
            if (bus[i].received == 1) {
              bus[i].received = '已确认';
            } else {
              bus[i].received = '未确认';
            }
          }
          that.setData({
            ticketList: bus
          });
        }
      },
      fai: function () {
        wx.showToast({
          title: '通信异常,请稍后再试',
          icon: 'none',
          duration: 1500
        });
      }
    });
  }
});