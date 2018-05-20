var api = require('../../api.js');
export default Page({
  data: {
    '__code__': {
      readme: ''
    },

    notice: '发车须知：包裹类型划分如下，小件：0--2KG；中件：2KG——4KG；大件：4KG——6KG，请量力发布,避免用户投诉。',
    siteList: '',
    start_time: '',
    end_time: '',
    small_price: '',
    small_count: '',
    normall_price: '',
    normall_count: '',
    big_price: '',
    big_count: '',
    driver_line: '',
    end_site: '',
    site_id: '',
    school_id: '',
    schoolList: '',
    currentSchool: '',
    driver_id: ''
  },
  isInteger(obj) {
    return obj % 1 === 0;
  },
  newBus() {
    //数据验证
    console.log(this.data);
    let small = Number(this.data.small_count);
    let normall = Number(this.data.normall_count);
    let big = Number(this.data.big_count);
    if (this.data.driver_id == '' || this.data.school_id == '' || this.data.driver_line == '' || this.data.site_id == '' || this.data.end_site == '' || this.data.small_price == '' || this.data.small_count == '' || this.data.normall_price == '' || this.data.normall_count == '' || this.data.big_price == '' || this.data.big_count == '' || this.data.start_time == '' || this.data.end_time == '') {
      wx.showToast({
        title: '还有必选项未填写',
        icon: 'none',
        duration: 1000
      });
      return false;
    }
    if (!(this.isInteger(big) && this.isInteger(small) && this.isInteger(normall))) {
      wx.showToast({
        title: '包裹数量只能是整数',
        icon: 'none',
        duration: 1000
      });
      return false;
    }
    if (this.data.start_time > this.data.end_time) {
      wx.showToast({
        title: '开始时间不能晚于结束时间',
        icon: 'none',
        duration: 1000
      });
      return false;
    }
    var that = this;
    wx.request({
      url: api.bus.newBus,
      method: 'POST',
      data: {
        '__code__': {
          readme: ''
        },

        driver_id: this.data.driver_id,
        school_id: this.data.school_id,
        driver_line: this.data.driver_line,
        site_id: this.data.site_id,
        end_site: this.data.end_site,
        small_price: this.data.small_price,
        small_count: this.data.small_count,
        normall_price: this.data.normall_price,
        normall_count: this.data.normall_count,
        big_price: this.data.big_price,
        big_count: this.data.big_count,
        start_time: this.data.start_time,
        end_time: this.data.end_time
      },
      success: function (res) {
        console.log(res.data);
        if (res.data == 1) {
          wx.showToast({
            title: '发布成功',
            icon: 'yes',
            duration: 2000
          });
          setTimeout(function () {
            wx.redirectTo({
              url: '/pages/passenger/index'
            });
          }, 1500);
        } else {
          wx.showToast({
            title: '发布失败',
            icon: 'none',
            duration: 1000
          });
        }
      },
      fail: function () {
        wx.showToast({
          title: '服务器连接失败',
          icon: 'none',
          duration: 1000
        });
      }

    });
  },
  onLoad() {
    let schoolList = wx.getStorageSync('schoolList');
    this.setData({
      schoolList: schoolList
    });
    let currentSchool = wx.getStorageSync('currentSchool');
    if (currentSchool) {
      this.setData({
        currentSchool: currentSchool,
        school_id: currentSchool.value
      });
    }
    let driver = wx.getStorageSync('driver');
    console.log(driver[0].id);
    if (driver) {
      this.setData({
        driver_id: driver[0].id
      });
    }
    let startSites = wx.getStorageSync('startSites');
    if (startSites.length) {
      this.setData({
        startSites: startSites
      });
    } else {
      this.getStartSites(this.data.school_id);
    }
  },
  getStartSites(school_id) {
    var that = this;
    console.log(this.shcool_id);
    wx.request({
      url: api.other.getSite,
      method: 'POST',
      data: {
        '__code__': {
          readme: ''
        },

        school_id: school_id
      },
      success: function (res) {
        console.log(res.data);
        if (res.data.length) {
          that.setData({
            startSites: res.data
          });
          wx.setStorageSync('startSites', that.data.startSites);
        } else if (res.data) {
          wx.showToast({
            title: '该学校暂时没有开通站点',
            icon: 'none',
            duration: 2000
          });
        }
      },
      fail: function () {
        wx.showToast({
          title: '链接服务器失败',
          icon: 'none',
          duration: 2000
        });
      }
    });
  },
  bindTimeChange: function (e) {
    console.log('picker发送选择改变，携带值为', e.detail.value);
    this.setData({
      start_time: e.detail.value
    });
  },
  bindPickerChange1: function (e) {
    let school_id = this.data.schoolList[e.detail.value].value;
    this.setData({
      index: e.detail.value,
      school_id: school_id
    });
    this.getStartSites(school_id);
  },
  bindPickerChange2: function (e) {
    console.log('picker发送选择改变，携带值为', this.data.startSites[e.detail.value].value);
    this.setData({
      index1: e.detail.value,
      site_id: this.data.startSites[e.detail.value].value
    });
  },
  bindEndTimeChange: function (e) {
    console.log('picker1发送选择改变，携带值为', e.detail.value);
    this.setData({
      end_time: e.detail.value
    });
  },
  showPopup() {
    let popupComponent = this.selectComponent('.J_Popup');
    popupComponent && popupComponent.show();
  },
  hidePopup() {
    let popupComponent = this.selectComponent('.J_Popup');
    popupComponent && popupComponent.hide();
  },
  saveSmallCount(e) {
    this.setData({
      small_count: e.detail.value
    });
  },
  saveSmallPrice(e) {
    this.setData({
      small_price: e.detail.value
    });
  },
  saveNormallCount(e) {
    this.setData({
      normall_count: e.detail.value
    });
  },
  saveNormallPrice(e) {
    this.setData({
      normall_price: e.detail.value
    });
  },
  saveBigCount(e) {
    this.setData({
      big_count: e.detail.value
    });
  },
  saveBigPrice(e) {
    this.setData({
      big_price: e.detail.value
    });
  },
  saveDriverLine(e) {
    this.setData({
      driver_line: e.detail.value
    });
  },
  saveEndSite(e) {
    this.setData({
      end_site: e.detail.value
    });
  }

});