var api = require('../../api.js');
export default Page({
  data: {
    school: { value: 0, title: '请选择学校' },
    address: '',
    consignee: '',
    tel: '',
    addressId: ''

  },
  watchConsignee: function (event) {
    this.setData({
      consignee: event.detail.value
    });
  },
  watchTel: function (event) {
    this.setData({
      tel: event.detail.value
    });
  },
  watchAddress: function (event) {
    this.setData({
      address: event.detail.value
    });
  },
  onLoad(option) {
    let schoolList = wx.getStorageSync('schoolList');
    this.setData({
      startSites: schoolList
    });
    let currentSchool = wx.getStorageSync('currentSchool');
    if (currentSchool) {
      console.log(currentSchool);
      this.setData({
        school: currentSchool
      });
    }
    if (option.id != 'no') {
      //console.log(option);
      let idx = option.id;
      let addressList = wx.getStorageSync('addressList');
      let address = addressList[idx];
      let address_id = this.data.addressId;
      this.setData({
        consignee: address.consignee,
        tel: address.tel,
        address: address.address,
        addressId: address.id
      });
    }
  },
  noSchool() {
    let school = wx.getStorageSync('schoolList');
    console.log('school list' + school.length);
  },
  bindPickerChange: function (e) {
    console.log('picker发送选择改变，携带值为', this.data.startSites[e.detail.value]);
    // console.log(e);
    this.setData({
      school: this.data.startSites[e.detail.value]
    });
  },
  addAddress() {
    console.log(this.data.address_id);
    // console.log(this.data.address);
    let consignee = this.data.consignee;
    let tel = this.data.tel;
    let school = this.data.school;
    let address = this.data.address;
    let address_id = this.data.addressId;
    let user = wx.getStorageSync('user');
    let user_id = user.data.uid;
    if (consignee == '' || tel == '' || school.title == '' || address == '' || school.title == '请选择学校' || school.value == '0') {
      wx.showToast({
        title: '请填写相关信息',
        duration: 1000,
        mask: true,
        icon: 'none'
      });
      return;
    }
    if (!/1[3-8]\d{9}/.test(tel)) {
      wx.showToast({
        title: '请输入正确的手机号',
        duration: 1000,
        mask: true,
        icon: 'none'

      });
      return;
    }
    wx.request({
      url: api.user.saveAddress,
      data: {
        tel: tel,
        consignee: consignee,
        school: school.value,
        address: address,
        address_id: address_id,
        user_id: user_id
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        // console.log(res.data)
        if (res.data == 'ok') {
          wx.navigateBack({
            delta: 1
          });
          wx.setStorageSync('addressList', '');
        } else {
          wx.showToast({
            title: '操作失败',
            duration: 1000,
            icon: 'none'

          });
        }
      },
      fail: function (res) {
        wx.showToast({
          title: '未知错误',
          duration: 1000,
          icon: 'none'

        });
      }
    });
  }
});