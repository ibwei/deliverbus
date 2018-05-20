var api = require('../../api.js');
var app = getApp().globalData;
export default Page({
  data: {
    '__code__': {
      readme: ''
    },

    start_site: '',
    end_site: '',
    inputValue: '',
    schoolList: '',
    currentSchool: '',
    today: '',
    startSites: ''
  },
  //初始化页面需要的数据
  onLoad() {
    //获取今日日期
    let today = wx.getStorageSync('today');
    this.setData({
      today: today
    });
    //console.log('home show')
    let list = wx.getStorageSync('schoolList');
    this.setData({
      schoolList: list
    });
    let school = wx.getStorageSync('currentSchool');
    this.setData({
      currentSchool: school
    });
    let sites = wx.getStorageSync('startSites');
    this.setData({
      startSites: sites
    });
  },
  //获取当前学校的所有站点
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
  noSchool() {
    let school = wx.getStorageSync('startSites');
    //console.log('school list'+school.length)
    if (!school.length) {
      wx.showToast({
        title: '请先选择学校',
        icon: 'none',
        duration: 2000
      });
    }
  },
  bindPickerChange: function (e) {
    //console.log('picker发送选择改变，携带值为', this.data.startSites[e.detail.value].title)
    this.setData({
      index: e.detail.value,
      start_site: this.data.startSites[e.detail.value].title
    });
  },
  saveEndSite(e) {
    console.log(e.detail.value);
    this.setData({
      end_site: e.detail.value
    });
  },
  serachSchool(e) {
    var that = this;
    console.log('input: ', e.detail.value);
    //console.log(api)
    let school_name = e.detail.value;
    wx.request({
      url: api.other.searchSchool,
      data: {
        '__code__': {
          readme: ''
        },

        name: school_name
      },
      method: 'POST',
      success: function (res) {
        console.log(res.data);
        if (res.data) {
          that.setData({
            schoolList: res.data
          });
        } else {
          that.setData({
            schoolList: null
          });
        }
      },
      fail: function () {
        that.setData({
          schoolList: null
        });
        wx.showToast({
          title: '查找失败,请检查网络',
          icon: 'none',
          duration: 2000
        });
      }
    });
  },
  chooseSchool(e) {
    console.log(e.detail.value);
    let choose_id = e.detail.value;
    for (let i = 0; i < this.data.schoolList.length; i++) {
      if (this.data.schoolList[i].value == choose_id) {
        this.setData({
          currentSchool: this.data.schoolList[i]
        });
        wx.setStorageSync('currentSchool', this.data.currentSchool);
        this.getStartSites(this.data.currentSchool.value);
      }
    }
    this.setData({
      inputValue: ''
    });
  },
  navToBus() {
    //正式上线后需更改条件
    if (!this.data.start_site) {
      wx.showToast({
        title: '请输入上车站点',
        icon: 'none',
        duration: 2000
      });
    } else {
      let busUrl = '/pages/bus/index?start=' + this.data.start_site + '&end=' + this.data.end_site;
      console.log(busUrl);
      wx.navigateTo({
        url: busUrl
      });
    }
  },
  showPopup() {
    let popupComponent = this.selectComponent('.school-popup');
    popupComponent && popupComponent.show();
  },
  hidePopup() {
    let popupComponent = this.selectComponent('.school-popup');
    popupComponent && popupComponent.hide();
  }
});