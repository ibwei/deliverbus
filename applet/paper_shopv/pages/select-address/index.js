//index.js
//获取应用实例
var app = getApp()
Page({
  data: {
    addressList:[]
  },

  selectTap: function (e) {
    var id = e.currentTarget.dataset.id;
    wx.request({
      url: app.globalData.apiDomain +'/addr-update-default',
      header: {
        'Accept': 'application/json', 'Authorization': 'Bearer ' + app.globalData.token
      },
      data: {
        token:app.globalData.token,
        id:id,
        isDefault:'true'
      },
      success: (res) =>{
        wx.navigateBack({})
      }
    })
  },

  addAddess : function () {
    wx.showActionSheet({
      itemList: ['添加校内地址', '添加校外地址'],
      success: function (res) {
        if (res.tapIndex == 0) {
          wx.navigateTo({
            url: "/pages/address-inside-add/index"
          })
        } else if (res.tapIndex == 1) {
          wx.navigateTo({
            url: "/pages/address-add/index"
          })
        }
      },
      fail: function (res) {
        wx.showModal({
          title: '提示',
          content: '选择错误',
          showCancel: false
        })
        return
      }
    });
  },
  
  editAddess: function (e) {
    var typeFlag = e.currentTarget.dataset.typeflag;
    if (typeFlag == 0) {
      wx.navigateTo({
        url: "/pages/address-inside-add/index?id=" + e.currentTarget.dataset.id
      })
    } else {
      wx.navigateTo({
        url: "/pages/address-add/index?id=" + e.currentTarget.dataset.id
      })
    }
    
  },
  
  onLoad: function () {
    console.log('onLoad')

   
  },
  onShow : function () {
    this.initShippingAddress();
  },
  initShippingAddress: function () {
    var that = this;
    wx.request({
      url: app.globalData.apiDomain +'/addr-list',
      data: {
        token:app.globalData.token
      },
      header: {
        'Accept': 'application/json', 'Authorization': 'Bearer ' + app.globalData.token
      },
      success: (res) =>{
        if (res.data.code == 200) {
          that.setData({
            addressList:res.data.data
          });
        } else if (res.data.code == 401){
          that.setData({
            addressList: null
          });
        }
      }
    })
  }

})
