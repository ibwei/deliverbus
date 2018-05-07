var commonCityData = require('../../utils/city.js')
//获取应用实例
var app = getApp()
Page({
  data: {
    schools: [],
    dorms: [],
    selSchool: '请选择',
    selDorm: '请选择',
    selSchoolIndex: 0,
    selDormIndex: 0
  },
  bindCancel: function () {
    wx.navigateBack({})
  },
  bindSave: function (e) {
    var that = this;
    var linkMan = e.detail.value.linkMan;
    var address = e.detail.value.address;
    var mobile = e.detail.value.mobile;

    if (linkMan == "") {
      wx.showModal({
        title: '提示',
        content: '请填写联系人姓名',
        showCancel: false
      })
      return
    }
    if (mobile == "") {
      wx.showModal({
        title: '提示',
        content: '请填写手机号码',
        showCancel: false
      })
      return
    }
    if (this.data.selSchool == "请选择") {
      wx.showModal({
        title: '提示',
        content: '请选择学校',
        showCancel: false
      })
      return
    }
    var schoolMap = wx.getStorageSync('schoolMap');
    var schoolID = schoolMap[this.data.selSchoolIndex][0];
    var schoolName = schoolMap[this.data.selSchoolIndex][1];

    if (this.data.selDorm == "请选择") {
      wx.showModal({
        title: '提示',
        content: '请选择宿舍楼',
        showCancel: false
      })
      return
    }
    var dormMap = wx.getStorageSync('dormMap');
    var dormID = dormMap[this.data.selDormIndex][0];
    var dormName = dormMap[this.data.selDormIndex][1];
    
    if (address == "") {
      wx.showModal({
        title: '提示',
        content: '请填写宿舍号',
        showCancel: false
      })
      return
    }
    var apiAddoRuPDATE = "add";
    var apiAddid = that.data.id;
    if (apiAddid) {
      apiAddoRuPDATE = "update";
    } else {
      apiAddid = 0;
    }
    wx.request({
      url: app.globalData.apiDomain + '/addr-inside-add',
      method: "POST",
      data: {
        token: app.globalData.token,
        id: apiAddid,
        schoolID: schoolID,
        schoolName: schoolName,
        dormID: dormID,
        dormName: dormName,
        linkMan: linkMan,
        address: address,
        mobile: mobile,
        isDefault: 'true'
      },
      header: {
        'Accept': 'application/json', 'Authorization': 'Bearer ' + app.globalData.token
      },
      success: function (res) {
        if (res.data.code != 200) {
          // 登录错误 
          wx.hideLoading();
          wx.showModal({
            title: '失败',
            content: res.data.message,
            showCancel: false
          })
          return;
        }
        // 跳转到结算页面
        wx.navigateBack({})
      }
    })
  },
  initSchoolData: function (level, obj) {
    var that = this;
    if (level == 1) {
      wx.request({
        url: app.globalData.apiDomain + '/addr-school-list',
        dataType: "json",
        success: function (res) {
          if (res.data.code == 200) {
            var pinkArray = [];
            var schoolMap = [];

            var data = res.data.data;
            for (var i = 0; i < data.length; i++) {
              pinkArray.push(data[i].name);
              schoolMap.push([data[i].id, data[i].name]);
            }

            wx.setStorageSync('schoolMap', schoolMap);
            that.setData({
              schools: pinkArray
            });
          }
        },
        fail: function (res) {

        }
      });
    } else if (level == 2) {
      wx.request({
        url: app.globalData.apiDomain + '/addr-dorm-list',
        data: {
          sid:obj[0]
        },
        success: function (res) {
          if (res.data.code == 200) {
            var pinkArray = [];
            var dormMap = [];

            var data = res.data.data;
            for (var i = 0; i < data.length; i++) {
              pinkArray.push(data[i].name);
              dormMap.push([data[i].id, data[i].name]);
            }
            wx.setStorageSync('dormMap', dormMap);
            that.setData({
              dorms: pinkArray
            });
          }
        },
        fail: function (res) {

        }
      });
    }
  },
  bindPickerSchoolChange: function (event) {
    var schoolMap = wx.getStorageSync("schoolMap");
    var selIterm = schoolMap[event.detail.value];
    this.setData({
      selSchool: selIterm[1],
      selSchoolIndex: event.detail.value,
      selDorm: '请选择',
      selDormIndex: 0,
    })
    this.initSchoolData(2, selIterm)
  },
  bindPickerDormChange: function (event) {
    var dormMap = wx.getStorageSync("dormMap");
    var selIterm = dormMap[event.detail.value];
    this.setData({
      selDorm: selIterm[1],
      selDormIndex: event.detail.value,
    })
  },
  onLoad: function (e) {
    var that = this;
    this.initSchoolData(1);
    var id = e.id;
    if (id) {
      // 初始化原数据
      wx.showLoading();
      wx.request({
        url: app.globalData.apiDomain + '/addr-detail',
        data: {
          token: app.globalData.token,
          id: id
        },
        header: {
          'Accept': 'application/json', 'Authorization': 'Bearer ' + app.globalData.token
        },
        success: function (res) {
          wx.hideLoading();
          if (res.data.code == 200) {
            that.setData({
              id: id,
              addressData: res.data.data,
              selProvince: res.data.data.provinceStr,
              selCity: res.data.data.cityStr,
              selDistrict: res.data.data.areaStr
            });
            that.setDBSaveAddressId(res.data.data);
            return;
          } else {
            wx.showModal({
              title: '提示',
              content: '无法获取快递地址数据',
              showCancel: false
            })
          }
        }
      })
    }
  },
  setDBSaveAddressId: function (data) {
    var retSelIdx = 0;
    for (var i = 0; i < commonCityData.cityData.length; i++) {
      if (data.provinceId == commonCityData.cityData[i].id) {
        this.data.selProvinceIndex = i;
        for (var j = 0; j < commonCityData.cityData[i].cityList.length; j++) {
          if (data.cityId == commonCityData.cityData[i].cityList[j].id) {
            this.data.selCityIndex = j;
            for (var k = 0; k < commonCityData.cityData[i].cityList[j].districtList.length; k++) {
              if (data.districtId == commonCityData.cityData[i].cityList[j].districtList[k].id) {
                this.data.selDistrictIndex = k;
              }
            }
          }
        }
      }
    }
  },
  selectCity: function () {

  },
  deleteAddress: function (e) {
    var that = this;
    var id = e.currentTarget.dataset.id;
    wx.showModal({
      title: '提示',
      content: '确定要删除该收货地址吗？',
      success: function (res) {
        if (res.confirm) {
          wx.request({
            url: app.globalData.apiDomain + '/addr-delete',
            header: {
              'Accept': 'application/json', 'Authorization': 'Bearer ' + app.globalData.token
            },
            data: {
              token: app.globalData.token,
              id: id
            },
            success: (res) => {
              wx.navigateBack({})
            }
          })
        } else if (res.cancel) {
          console.log('用户点击取消')
        }
      }
    })
  },
  readFromWx: function () {
    let that = this;
    wx.chooseAddress({
      success: function (res) {
        let provinceName = res.provinceName;
        let cityName = res.cityName;
        let diatrictName = res.countyName;
        let retSelIdx = 0;
        for (var i = 0; i < commonCityData.cityData.length; i++) {
          if (provinceName == commonCityData.cityData[i].name) {
            that.data.selProvinceIndex = i;
            for (var j = 0; j < commonCityData.cityData[i].cityList.length; j++) {
              if (cityName == commonCityData.cityData[i].cityList[j].id) {
                that.data.selCityIndex = j;
                for (var k = 0; k < commonCityData.cityData[i].cityList[j].districtList.length; k++) {
                  if (diatrictName == commonCityData.cityData[i].cityList[j].districtList[k].id) {
                    that.data.selDistrictIndex = k;
                  }
                }
              }
            }
          }
        }

        that.setData({
          wxaddress: res,
          selProvince: provinceName,
          selCity: cityName,
          selDistrict: diatrictName
        });
      }
    })
  }
})