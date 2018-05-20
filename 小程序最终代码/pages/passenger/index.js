var api = require('../../api.js');
export default Page({
  data: {
    '__code__': {
      readme: ''
    },

    buttonClass: 'button1'

  },
  onLoad() {
    let user = wx.getStorageSync('user');
    let user_id = user.data.uid;
    var _this = this;
    wx.request({
      url: api.user.ifSiJi,
      method: "POST",
      data: {
        '__code__': {
          readme: ''
        },

        user_id: user_id
      },
      header: {
        'content-type': 'application/x-www-form-urlencoded',
        'Authorization': 'Bearer ' + wx.getStorageSync('user').data.token
      },
      success: function (res) {
        //console.log(res.data);
        let data = res.data;
        if (data.message == 'dont') {
          wx.showToast({
            title: '你还不是老司机，请先去申请',
            icon: 'none',
            duration: 2000
          });
        } else {
          //console.log(data.data[0].state)
          if (data.data[0].state == 0 || data.data[0].state == 1) {
            wx.showToast({
              title: '你已提交老司机申请，请耐心等待',
              duration: 2000,
              icon: 'none'
            });
          } else {
            let driver = wx.getStorageSync('driver');
            let driver_id = data.data[0].id;
            let that = _this;
            wx.request({
              url: api.driver.getPassenger,
              method: 'POST',
              data: {
                '__code__': {
                  readme: ''
                },

                user_id: user_id,
                driver_id: driver_id
              },
              header: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + wx.getStorageSync('user').data.token
              },
              success: function (res) {
                if (res.data.length > 0) {
                  //console.log(res.data)
                  let bus = res.data;
                  for (let i = 0; i < bus.length; i++) {
                    bus[i].time = bus[i].start_time.substr(0, 10);
                    bus[i].start_time = bus[i].start_time.substr(11, 5);
                    bus[i].end_time = bus[i].end_time.substr(11, 5);
                    bus[i].buttonClass = 'button' + bus[i].status;
                    if (bus[i].status == 0) {
                      bus[i].message = '未发车';
                      bus[i].button = '确认发车';
                    } else if (bus[i].status == 1) {
                      bus[i].message = '已发车';
                      bus[i].button = '确认到站';
                    } else if (bus[i].status == 2) {
                      bus[i].message = '已到站';
                      bus[i].button = '确认结束';
                    } else {
                      bus[i].message = '已结束';
                      bus[i].button = '已完成';
                    }
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
          }
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
  toDetail(e) {
    console.log(e);
    let bus_id = e.currentTarget.dataset.busid;
    wx.navigateTo({
      url: "/pages/busDetail/index?id=" + bus_id
    });
  },
  confirmStatus(e) {
    console.log('改变状态');
    let bus_id = e.currentTarget.dataset.busid;
    let bus = e.currentTarget.dataset.bus;
    let status = bus.status;
    if (status == 3) {
      wx.showToast({
        title: '该bus已结束，无需在操作',
        duration: 1000,
        icon: 'none'
      });
      return;
    }
    //console.log(status);
    var that = this;
    let user = wx.getStorageSync('user');
    let user_id = user.data.uid;
    let driver = wx.getStorageSync('driver');
    let driver_id = driver[0].id;
    wx.request({
      url: api.bus.changeStatus,
      method: 'POST',
      data: {
        '__code__': {
          readme: ''
        },

        user_id: user_id,
        bus_id: bus_id,
        status: status,
        driver_id: driver_id
      },
      header: {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + wx.getStorageSync('user').data.token
      },
      success: function (res) {
        //console.log(res);
        if (res.data.length > 0) {
          console.log('更新状态成功');
          let bus = res.data;
          for (let i = 0; i < bus.length; i++) {
            bus[i].time = bus[i].start_time.substr(0, 10);
            bus[i].start_time = bus[i].start_time.substr(11, 5);
            bus[i].end_time = bus[i].end_time.substr(11, 5);
            bus[i].buttonClass = 'button' + bus[i].status;
            if (bus[i].status == 0) {
              bus[i].message = '未发车';
              bus[i].button = '确认发车';
            } else if (bus[i].status == 1) {
              bus[i].message = '已发车';
              bus[i].button = '确认到站';
            } else if (bus[i].status == 2) {
              bus[i].message = '已到站';
              bus[i].button = '确认结束';
            } else {
              bus[i].message = '已结束';
              bus[i].button = '已完成';
            }
          }
          that.setData({
            busList: bus
          });
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
  }

});