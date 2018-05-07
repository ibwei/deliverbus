// pages/delivery-orders/index.js
var app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    statusType: {22 : "未确认", 26 : "待配送", 32 : "配送中", 40 : "已完成"},
    currentType: 22,
    tabClass: ["", "", ""]
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
  
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
  
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    // 获取配送订单列表
    wx.showLoading();
    var that = this;
   
    var postData = {
      token: app.globalData.token
    };
    postData.status = that.data.currentType;
    wx.request({
      url: app.globalData.apiDomain + '/delivery-orders/list',
      data: postData,
      header: {
        'Accept': 'application/json', 'Authorization': 'Bearer ' + app.globalData.token
      },
      success: (res) => {
        wx.hideLoading();
        console.log(res.data);
        if (res.data.code == 200) {
          that.setData({
            orderList: res.data.data.orderList,
            goodsMap: res.data.data.goodsMap
          });
        } else {
          this.setData({
            orderList: null,
            goodsMap: {}
          });
        }
      }
    })
  },
  getOrderStatistics: function () {
    var that = this;
    wx.request({
      url: app.globalData.apiDomain + '/delivery-orders/statistics',
      header: {
        'Accept': 'application/json', 'Authorization': 'Bearer ' + app.globalData.token
      },
      data: { 
        token: app.globalData.token 
      },
      success: (res) => {
        wx.hideLoading();
        if (res.data.code == 0) {
          var tabClass = that.data.tabClass;
          if (res.data.data.countPaid > 0) {
            tabClass[0] = "red-dot"
          } else {
            tabClass[0] = ""
          }
          if (res.data.data.countReceived > 0) {
            tabClass[1] = "red-dot"
          } else {
            tabClass[1] = ""
          }
          if (res.data.data.countCompleted > 0) {
            //tabClass[4] = "red-dot"
          } else {
            //tabClass[4] = ""
          }

          that.setData({
            tabClass: tabClass,
          });
        }
      }
    })
  },
  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {
  
  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {
  
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
  
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
  
  },
  /**
   * 用户更换tab
   */
  statusTap: function (e) {
    var curType = e.currentTarget.dataset.index;
    this.data.currentType = curType
    this.setData({
      currentType: curType
    });
    this.onShow();
  },

  /**
   * 用户确认接单
   */
  toRecieveOrderTap: function (e) {
    var that = this;
    var orderId = e.currentTarget.dataset.id;
    wx.request({
      url: app.globalData.apiDomain + '/delivery-orders/receive',
      method: "POST",
      header: {
        'Accept': 'application/json', 'Authorization': 'Bearer ' + app.globalData.token
      },
      data: {
        token: app.globalData.token,
        orderID: orderId
      },
      success: (res) => {
        wx.hideLoading();
        if (res.data.code == 200) {
          that.onShow();
        } else if (res.data.code == 402) {
          wx.showToast({
            title: res.data.message,
            icon: 'none',
            duration: 2000
          })
          that.onShow();
        }
      }
    })
  },

  /**
   * 用户确认配送
   */
  toDeliveryOrderTap: function (e) {
    var that = this;
    var orderId = e.currentTarget.dataset.id;
    wx.showLoading();
    wx.request({
      url: app.globalData.apiDomain + '/delivery-orders/delivery',
      method: "POST",
      header: {
        'Accept': 'application/json', 'Authorization': 'Bearer ' + app.globalData.token
      },
      data: {
        token: app.globalData.token,
        orderID: orderId
      },
      success: (res) => {
        wx.hideLoading();
        if (res.data.code == 200) {
          that.onShow();
        } else if (res.data.code == 402) {
          wx.showToast({
            title: res.data.message,
            icon: 'none',
            duration: 2000
          })
          that.onShow();
        }
      }
    })
  }
})