var app = getApp();
Page({
    data:{
      orderId:0,
        goodsList:[
        ],
        yunPrice:"10.00"
    },
    onLoad:function(e){
      var orderId = e.id;
      this.data.orderId = orderId;
      this.setData({
        orderId: orderId
      });
    },
    onShow : function () {
      var that = this;
      wx.request({
        url: app.globalData.apiDomain + '/order/detail',
        data: {
          token: app.globalData.token,
          orderID: that.data.orderId
        },
        header: {
          'Accept': 'application/json', 'Authorization': 'Bearer ' + app.globalData.token
        },
        success: (res) => {
          wx.hideLoading();
          if (res.data.code != 200) {
            wx.showModal({
              title: '错误',
              content: res.data.message,
              showCancel: false
            })
            return;
          }
          that.setData({
            orderDetail: res.data.data
          });
        }
      })
      /**
      var yunPrice = parseFloat(this.data.yunPrice);
      var allprice = 0;
      var goodsList = this.data.goodsList;
      for (var i = 0; i < goodsList.length; i++) {
        allprice += parseFloat(goodsList[0].price) * goodsList[0].number;
      }
      this.setData({
        allGoodsPrice: allprice,
        yunPrice: yunPrice
      });
       */
     
    },
    calling: function (e) {
      wx.makePhoneCall({
        phoneNumber: e.currentTarget.dataset.phone,
      })
    }, 
    wuliuDetailsTap:function(e){
      var orderId = e.currentTarget.dataset.id;
      wx.navigateTo({
        url: "/pages/wuliu/index?id=" + orderId
      })
    },
    confirmBtnTap:function(e){
      var that = this;
      var orderId = e.currentTarget.dataset.id;
      wx.showModal({
          title: '确认您已收到商品？',
          content: '',
          success: function(res) {
            if (res.confirm) {
              wx.showLoading();
              wx.request({
                url: app.globalData.apiDomain + '/order/complete',
                method:"POST",
                data: {
                  token: app.globalData.token,
                  orderID: orderId
                },
                header: {
                  'Accept': 'application/json', 'Authorization': 'Bearer ' + app.globalData.token
                },
                success: (res) => {
                  if (res.data.code == 200) {
                    that.onShow();
                  } else {
                    wx.showModal({
                      title: '提示',
                      content: res.data.message,
                      showCancel: false
                    })
                  }
                }
              })
            }
          }
      })
    },
    submitReputation: function (e) {
      var that = this;
      var postJsonString = {};
      postJsonString.token = app.globalData.token;
      postJsonString.orderId = this.data.orderId;
      var reputations = [];
      var i = 0;
      while (e.detail.value["orderGoodsId" + i]) {
        var orderGoodsId = e.detail.value["orderGoodsId" + i];
        var goodReputation = e.detail.value["goodReputation" + i];
        var goodReputationRemark = e.detail.value["goodReputationRemark" + i];

        var reputations_json = {};
        reputations_json.id = orderGoodsId;
        reputations_json.reputation = goodReputation;
        reputations_json.remark = goodReputationRemark;

        reputations.push(reputations_json);
        i++;
      }
      postJsonString.reputations = reputations;
      wx.showLoading();
      wx.request({
        url: 'https://api.it120.cc/' + app.globalData.subDomain + '/order/reputation',
        data: {
          postJsonString: postJsonString
        },
        header: {
          'Accept': 'application/json', 'Authorization': 'Bearer ' + app.globalData.token
        },
        success: (res) => {
          wx.hideLoading();
          if (res.data.code == 0) {
            that.onShow();
          }
        }
      })
    }
})