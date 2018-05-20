var api = require('../../api.js');
export default Page({
  data: {
    '__code__': {
      readme: ''
    },

    card_img: [],
    display: '',
    card_number: '',
    name: '',
    startSites: '',
    school: { value: 0, title: '请点击选择学校' },
    address: '',
    path: '',
    formData: {}
  },
  onLoad(option) {
    var that = this;
    let user = wx.getStorageSync('user');
    let user_id = user.data.uid;
    wx.request({
      url: api.user.isDriver,
      data: {
        '__code__': {
          readme: ''
        },

        user_id: user_id
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        if (res.data == 'ok') {
          console.log('已经是老司机');
          wx.navigateBack({
            delta: 1
          });
          wx.showToast({
            title: '你已经是老司机了！',
            duration: 2000,
            mask: true,
            icon: 'none'
          });
          return;
        } else if (res.data == 'wait') {
          console.log('正在申请');
          wx.navigateBack({
            delta: 1
          });
          wx.showToast({
            title: '你已提交申请，请耐心等待',
            duration: 2000,
            mask: true,
            icon: 'none'
          });
          return;
        } else {
          console.log('不是老司机');
        }
      },
      fail: function (res) {
        wx.showToast({
          title: '异常错误',
          duration: 1000,
          icon: 'none'
        });
      }
    });
    let schoolList = wx.getStorageSync('schoolList');
    that.setData({
      startSites: schoolList
    });
    let currentSchool = wx.getStorageSync('currentSchool');
    if (currentSchool) {
      console.log(currentSchool);
      that.setData({
        school: currentSchool
      });
    }
  },
  bindPickerChange: function (e) {
    console.log('picker发送选择改变，携带值为', this.data.startSites[e.detail.value]);
    // console.log(e);
    this.setData({
      school: this.data.startSites[e.detail.value]
    });
  },
  watchName: function (event) {
    this.setData({
      name: event.detail.value
    });
  },
  watchCardNum: function (event) {
    this.setData({
      card_number: event.detail.value
    });
  },
  watchAddress: function (event) {
    this.setData({
      address: event.detail.value
    });
  },
  applyDriver() {
    console.log('申请老司机');
    let card_img = this.data.card_img;
    let formData = this.data.formData;
    let name = this.data.name;
    let card_number = this.data.card_number;
    let school = this.data.school;
    let address = this.data.address;
    if (name == '' || card_number == '' || school == '' || address == '' || school == '请选择学校') {
      wx.showToast({
        title: '请填写相关信息',
        duration: 1000,
        icon: 'none'
      });
      return;
    }
    this.upload_file(api.user.uploadCardImg, card_img, 'photo', formData);
    // let path = this.data.path;
    // console.log(path);
    // wx.request({
    //   url: 'http://www.driver.com/api/driver/newDriver',
    //   data: {
    //     card_number:card_number,
    //     name:name,
    //     school:school,
    //     address:address,
    //     path:path,
    //   },
    //   method: 'POST',
    //   header: {
    //     'content-type': 'application/x-www-form-urlencoded'
    //   },
    //   success: function (res) {
    //     // console.log(res.data)
    //     if (res.data=='ok') {
    //       wx.navigateBack ({
    //         delta:1,
    //       })
    //       wx.showToast({
    //         title: '申请已提交，请耐心等待',
    //         duration: 2000,
    //         mask: true,
    //         icon: 'none'
    //       })
    //       console.log('跳转')
    //     }else{
    //       console.log('未知错误!')
    //     }
    //   },
    //   fail: function (res) {
    //     // fail
    //   }
    // })
  },
  chooseImageTap() {
    console.log('选择图片');
    let _this = this;
    wx.showActionSheet({
      itemList: ['从相册中选择', '拍照'],
      itemColor: "#000000",
      success: function (res) {
        if (!res.cancel) {
          if (res.tapIndex == 0) {
            _this.chooseWxImage('album');
          } else if (res.tapIndex == 1) {
            _this.chooseWxImage('camera');
          }
        }
      }
    });
  },
  chooseWxImage(type) {
    let _this = this;
    wx.chooseImage({
      sizeType: ['original', 'compressed'],
      sourceType: [type],
      success: function (res) {
        //console.log(res);
        _this.setData({
          card_img: res.tempFilePaths,
          display: true
        });
      }
    });
  },

  upload_file(url, filePaths, name, formData) {
    console.log(filePaths[0]);
    let _this = this;
    wx.uploadFile({
      url: url,
      filePath: filePaths[0],
      name: name,
      formData: formData,
      success: function (res) {
        var data = JSON.parse(res.data);
        if (data.err == 0) {
          console.log('成功');
          console.log(data.path);
          _this.setData({
            path: data.path
          });
          let card_img = _this.data.card_img;
          let formData = _this.data.formData;
          let name = _this.data.name;
          let card_number = _this.data.card_number;
          let school = _this.data.school;
          let address = _this.data.address;
          let user = wx.getStorageSync('user');
          let user_id = user.data.uid;
          wx.request({
            url: api.driver.newDriver,
            data: {
              '__code__': {
                readme: ''
              },

              card_number: card_number,
              name: name,
              school: school.value,
              address: address,
              path: data.path,
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
                wx.showToast({
                  title: '申请已提交，请耐心等待',
                  duration: 2000,
                  icon: 'none'
                });
              } else {
                wx.showToast({
                  title: '异常错误',
                  duration: 1000,
                  icon: 'none'
                });
              }
            },
            fail: function (res) {
              wx.showToast({
                title: '异常错误',
                duration: 1000,
                icon: 'none'
              });
            }
          });
        } else {
          wx.showToast({
            title: '异常错误',
            duration: 1000,
            icon: 'none'
          });
        }
      },
      fail: function (res) {
        wx.showToast({
          title: '异常错误',
          duration: 1000,
          icon: 'none'
        });
      }
    });
  }
});