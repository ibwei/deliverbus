var api = require('../../api.js');
export default Page({
  data: {
    '__code__': {
      readme: ''
    },

    user: '',
    message: ''
  },
  sendFeedback(e) {
    var that = this;
    var user = wx.getStorageSync('user');
    this.setData({
      user: user
    });
    if (this.data.message == '') {
      this.showToast('您暂未输入任何反馈消息');
      return false;
    }
    wx.request({
      url: api.other.sendFeedback,
      data: {
        '__code__': {
          readme: ''
        },

        user_id: this.data.user.data.uid,
        message: this.data.message
      },
      method: 'POST',
      success: function (res) {
        console.log(res.data);
        if (res.data == 1) {
          that.showToast1();
          that.setData({
            message: ''
          });
        } else {
          that.showToast('服务器接收失败,清稍后再试');
        }
      },
      fail: function (e) {
        that.showToast('请检查网络状态');
      }
    });
  },
  saveMessage(e) {
    let inputValue = e.detail.value;
    this.setData({
      message: inputValue
    });
    console.log(this.data.message);
  },
  showToast(toastText) {
    let $toast = this.selectComponent(".J_toast");
    $toast && $toast.show(toastText);
  },
  showToast1() {
    let $toast = this.selectComponent(".login_toast");
    $toast && $toast.show('发表成功');
  }
});