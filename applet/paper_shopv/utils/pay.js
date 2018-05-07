function wxpay(app, money, orderId, redirectUrl) {
  let remark = "在线充值";
  let nextAction = {};
  if (orderId != 0) {
    remark = "支付订单 ：" + orderId;
  }
  wx.request({
    url: app.globalData.apiDomain + '/order-pay',
    header: {
      'Accept': 'application/json', 'Authorization': 'Bearer ' + app.globalData.token
    },
    data: {
      token:app.globalData.token,
      money:money,
      remark: remark,
      payName:"在线支付",
      orderId: orderId
    },
    //method:'POST',
    success: function(res){
      console.log(res);
      if(res.data.code == 200){
        // 发起支付
        wx.requestPayment({
          timeStamp: res.data.data.timeStamp.toString(),
          nonceStr: res.data.data.nonceStr,
          package: res.data.data.package,
          signType:'MD5',
          paySign: res.data.data.paySign,
          fail:function (aaa) {
            console.log(aaa)
            wx.showToast({title: '支付失败:' + aaa})
          },
          success:function () {
            wx.showToast({title: '支付成功'})
            wx.reLaunch({
              url: redirectUrl
            });
          }
        })
      } else {
        wx.showToast({ title: '服务器忙' + res.data.code + res.data.msg})
      }
    }
  })
}

module.exports = {
  wxpay: wxpay
}
