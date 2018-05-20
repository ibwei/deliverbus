var api = require('../../api.js');
export default Page({
  data: {
    '__code__': {
      readme: ''
    },

    ticketList: '',
    user: ''
  },
  onLoad() {
    var that = this;
    let user = wx.getStorageSync('user');
    this.setData({
      user: user
    });
    wx.request({
      url: api.user.getTicket,
      method: 'POST',
      data: {
        '__code__': {
          readme: ''
        },

        user_id: that.data.user.data.uid
      },
      success: function (res) {
        console.log(res.data);
        if (res.data.length) {
          let bus = res.data;
          for (let i = 0; i < bus.length; i++) {
            bus[i].start_time = bus[i].start_time.substr(11, 5);
            bus[i].end_time = bus[i].end_time.substr(11, 5);
            bus[i].count_time = that.countTime(bus[i].start_time, bus[i].end_time);
            if (bus[i].status == 0) {
              bus[i].statusText = '未发车';
            } else if (bus[i].status == 1) {
              bus[i].statusText = '已发车';
            } else if (bus[i].status == 2) {
              bus[i].statusText = '已到站';
            } else {
              bus[i].statusText = '已结束';
            }
          }
          that.setData({
            ticketList: bus
          });
        } else {
          wx.showToast({
            title: '获取数据失败',
            icon: 'none',
            duration: 1500
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
  confirmReceived(e) {
    let index = e.currentTarget.dataset.index;
    var received = 'ticketList[' + index + '].received';
    var that = this;
    wx.request({
      url: api.order.confirmReceived,
      data: {
        '__code__': {
          readme: ''
        },

        trade_sn: this.data.ticketList[index].trade_sn
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        //console.log(res.data)
        if (res.data == 1) {
          that.setData({
            [received]: 1
          });
          wx.showToast({
            title: '确认收货成功',
            icon: 'yes',
            duration: 1500
          });
        } else {
          wx.showToast({
            title: '确认收货失败',
            icon: 'none',
            duration: 1500
          });
        }
      },
      fail: function () {
        wx.showToast({
          title: '异常错误',
          icon: 'none',
          duration: 1500
        });
      }
    });
  }

});