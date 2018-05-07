//index.js
//获取应用实例
var app = getApp()

Page({
  data: {
    goodsList:[],
    isNeedLogistics:0, // 是否需要物流信息
    allGoodsPrice:0,
    yunPrice:0,
    allGoodsAndYunPrice:0,
    goodsJsonStr:"",
    orderType:"", //订单类型，购物车下单或立即支付下单，默认是购物车，

    hasNoCoupons: true,
    coupons: [],
    youhuijine:0, //优惠券金额
    curCoupon:null // 当前选择使用的优惠券
  },
  onShow : function () {
    var that = this;
    var shopList = [];
    //立即购买下单
    if ("buyNow"==that.data.orderType){
      var buyNowInfoMem = wx.getStorageSync('buyNowInfo');
      if (buyNowInfoMem && buyNowInfoMem.shopList) {
        shopList = buyNowInfoMem.shopList
      }
    }else{
      //购物车下单
      var shopCarInfoMem = wx.getStorageSync('shopCarInfo');
      if (shopCarInfoMem && shopCarInfoMem.shopList) {
        // shopList = shopCarInfoMem.shopList
        shopList = shopCarInfoMem.shopList.filter(entity => {
          return entity.active;
        });
      }
    }
    console.log(shopList);
    that.setData({
      goodsList: shopList,
    });
    that.initShippingAddress();
  },

  onLoad: function (e) {
    
    var that = this;
    //显示收货地址标识
    that.setData({
      isNeedLogistics: 1,
      orderType: e.orderType
    });
  },

  getDistrictId : function (obj, aaa){
    if (!obj) {
      return "";
    }
    if (!aaa) {
      return "";
    }
    return aaa;
  },

  createOrder:function (e) {
    wx.showLoading();
    var that = this;
    var loginToken = app.globalData.token // 用户登录 token
    var remark = ""; // 备注信息
    if (e) {
      remark = e.detail.value.remark; // 备注信息
    }
    
    var postData = {
      token: loginToken,
      goodsJsonStr: that.data.goodsJsonStr,
      remark: remark
    };
    if (that.data.isNeedLogistics > 0) {
      if (!that.data.curAddressData) {
        wx.hideLoading();
        wx.showModal({
          title: '错误',
          content: '请先设置您的收货地址！',
          showCancel: false
        })
        return;
      }
      postData.aid = that.data.curAddressData.id;
      postData.provinceId = that.data.curAddressData.provinceId;
      postData.cityId = that.data.curAddressData.cityId;
      if (that.data.curAddressData.districtId) {
        postData.districtId = that.data.curAddressData.districtId;
      }
      postData.address = that.data.curAddressData.address;
      postData.linkMan = that.data.curAddressData.linkMan;
      postData.mobile = that.data.curAddressData.mobile;
      postData.code = that.data.curAddressData.code;
    }
    if (that.data.curCoupon) {
      postData.couponId = that.data.curCoupon.id;
    }
    if (!e) {
      postData.calculate = "true";
    }


    wx.request({
      url: app.globalData.apiDomain + '/order/create',
      method:'POST',
      header: {
        'Accept': 'application/json', 'Authorization': 'Bearer ' + app.globalData.token
      },
      data: postData, // 设置请求的 参数
      success: (res) =>{
        wx.hideLoading();
        if (res.data.code != 200) {
          wx.showModal({
            title: '错误',
            content: res.data.message,
            showCancel: false
          })
          return;
        }

        if (e && "buyNow" != that.data.orderType) {
          // 清空购物车数据
          wx.removeStorageSync('shopCarInfo');
        }
        if (!e) {
          that.setData({
            isNeedLogistics: res.data.data.isNeedLogistics,
            allGoodsPrice: res.data.data.amountTotle,
            allGoodsAndYunPrice: res.data.data.amountLogistics + res.data.data.amountTotle,
            yunPrice: res.data.data.amountLogistics
          });
          return;
        }
        // 配置模板消息推送
        var postJsonString = {};
        postJsonString.keyword1 = { value: res.data.data.dateAdd, color: '#173177' }
        postJsonString.keyword2 = { value: res.data.data.amountReal + '元', color: '#173177' }
        postJsonString.keyword3 = { value: res.data.data.orderNumber, color: '#173177' }
        postJsonString.keyword4 = { value: '订单已关闭', color: '#173177' }
        postJsonString.keyword5 = { value: '您可以重新下单，请在30分钟内完成支付', color:'#173177'}
        // app.sendTempleMsg(res.data.data.id, -1,
        //   'mGVFc31MYNMoR9Z-A9yeVVYLIVGphUVcK2-S2UdZHmg', e.detail.formId,
        //   'pages/index/index', JSON.stringify(postJsonString));
        postJsonString = {};
        postJsonString.keyword1 = { value: '您的订单已发货，请注意查收', color: '#173177' }
        postJsonString.keyword2 = { value: res.data.data.orderNumber, color: '#173177' }
        postJsonString.keyword3 = { value: res.data.data.dateAdd, color: '#173177' }
        // app.sendTempleMsg(res.data.data.id, 2,
        //   'Arm2aS1rsklRuJSrfz-QVoyUzLVmU2vEMn_HgMxuegw', e.detail.formId,
        //   'pages/order-details/index?id=' + res.data.data.id, JSON.stringify(postJsonString));
        // 下单成功，跳转到订单管理界面
        wx.redirectTo({
          url: "/pages/order-list/index"
        });
      }
    })
  },
  initShippingAddress: function () {
    var that = this;
    wx.request({
      url: app.globalData.apiDomain + '/default-addr',
      data: {
        token:app.globalData.token
      },
      header: {'Accept': 'application/json', 'Authorization':'Bearer ' + app.globalData.token},
      success: (res) =>{
        if (res.data.code == 200 && res.data.data) {
          that.setData({
            curAddressData:res.data.data
          });
        }else{
          that.setData({
            curAddressData: null
          });
        }
        console.log(that.data.curAddressData);
        that.processYunfei();
      }
    })
  },
  processYunfei: function () {
    var that = this;
    var goodsList = this.data.goodsList;
    var goodsJsonStr = "[";
    var isNeedLogistics = 0;
    var allGoodsPrice = 0;

    for (let i = 0; i < goodsList.length; i++) {
      let carShopBean = goodsList[i];
      console.log(carShopBean);
      if (carShopBean.logistics) {
        isNeedLogistics = 1;
      }
      allGoodsPrice += carShopBean.price  * carShopBean.number;

      var goodsJsonStrTmp = '';
      if (i > 0) {
        goodsJsonStrTmp = ",";
      }


      let inviter_id = 0;
      let inviter_id_storge = wx.getStorageSync('inviter_id_' + carShopBean.goodsId);
      if (inviter_id_storge) {
        inviter_id = inviter_id_storge;
      }


      goodsJsonStrTmp += '{"goodsId":' + carShopBean.goodsId + ',"number":' + carShopBean.number + ',"propertyChildIds":"' + carShopBean.propertyChildIds + '","logisticsType":0, "inviter_id":' + inviter_id +'}';
      goodsJsonStr += goodsJsonStrTmp;


    }
    goodsJsonStr += "]";
    //console.log(goodsJsonStr);
    that.setData({
      isNeedLogistics: isNeedLogistics,
      goodsJsonStr: goodsJsonStr
    });
    that.createOrder();
  },
  addAddress: function () {
    wx.showActionSheet({
      itemList: ['添加校内地址', '添加校外地址'],
      success: function (res) {
        if (res.tapIndex == 0) {
          wx.navigateTo({
            url: "/pages/address-inside-add/index"
          })
        } else if (res.tapIndex == 1){
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
  selectAddress: function () {
    wx.navigateTo({
      url:"/pages/select-address/index"
    })
  }
})
