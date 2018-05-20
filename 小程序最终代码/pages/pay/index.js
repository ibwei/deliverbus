var app = getApp();
var api = require('../../api.js');
export default Page({
  data: {
    '__code__': {
      readme: ''
    },

    isShow: true,
    trade_sn: '',
    isPay: false,
    price: '',
    order_amount: 0,
    orderInfo: {
      order_amount: 1,
      consignee: '白小唯',
      tel: '15723695007',
      address: '清风苑B 522'
    }
  },
  onLoad: function (option) {
    //  this.setData({
    //    orderInfo : wx.getStorageSync('orderInfo')
    //  }) 
    console.log(option);
    this.setData({
      price: option.price,
      trade_sn: option.trade_sn
    });
  },
  payTest() {
    var that = this;
    this.setData({
      isShow: false
    });
    wx.request({
      url: api.order.payok,
      data: {
        '__code__': {
          readme: ''
        },

        trade_sn: this.data.trade_sn
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        console.log(res.data);
        if (res.data == 1) {
          that.setData({
            isPay: true
          });
          setTimeout(function () {
            wx.redirectTo({
              url: '/pages/myticket/index'
            });
          }, 2000);
        }
      }
    });
  },
  pay: function () {
    var page = this;
    wx.login({
      success: function (res) {
        if (res.code) {
          wx.request({
            url: 'https://daban2017.leanapp.cn/pay.php',
            data: {
              '__code__': {
                readme: ''
              },

              code: res.code,
              goods_name: page.data.orderInfo.goods_name,
              order_sn: page.data.orderInfo.order_sn,
              order_amount: page.data.orderInfo.order_amount
            },
            method: 'POST',
            header: {
              'content-type': 'application/x-www-form-urlencoded'
            },
            success: function (response) {
              // 发起支付
              wx.requestPayment({
                'timeStamp': response.data.timeStamp,
                'nonceStr': response.data.nonceStr,
                'package': response.data.package,
                'signType': 'MD5',
                'paySign': response.data.paySign,
                'success': function (res) {
                  // wx.showToast({
                  //     title: '支付成功'
                  // });
                  var url = url = '/pages/order/done/done?order_sn=' + page.data.orderInfo.order_sn;
                  if (page.data.orderInfo.group_id != undefined) {
                    url = '/pages/order/done/done?order_sn=' + page.data.orderInfo.order_sn + '&pinsucess=1';
                  }
                  wx.navigateTo({
                    url: url
                  });

                  console.log(res);
                },
                'fail': function (res) {
                  console.log('3333');
                  console.log(res);
                }
              });
            },
            fail: function (res) {
              console.log('3333');
              console.log(res);
            }
          });
        } else {
          console.log('登录失败');
        }
      }
    });
  }
});