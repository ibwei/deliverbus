var _api_root = 'https:\/\/www.ibwei.com/api/';
var api = {
    index: _api_root + 'default/index',
    user: {
        register: _api_root + 'user/register',
        login: _api_root + 'user/login',
        getUserInfo: _api_root + 'user/getUserInfo', //查询与用户所有信息
        getMoblie: _api_root + 'user/getMoblie',
        saveMoblie: _api_root + 'user/saveMoblie',
        getTicket: _api_root + 'user/getTicket',
        getMyAddresses: _api_root + 'user/getMyAddresses',
        setDefaultAddress: _api_root + 'user/setDefaultAddress',
        delAddress: _api_root + 'user/delAddress',
        editAddress: _api_root + 'user/editAddress',
        saveAddress: _api_root + 'user/saveAddress',
        getMyCoupons: _api_root + 'user/getMyCoupons',
        isDriver: _api_root + 'user/isDriver',
        ifSiJi: _api_root + 'user/ifSiJi',
        uploadCardImg: _api_root + 'user/uploadCardImg'
    },
    driver: {
        newDriver: _api_root + 'driver/newDriver',
        getPassenger: _api_root + 'driver/getPassenger',
        passengerDetail: _api_root + 'driver/passengerDetail'

    },
    bus: {
        newBus: _api_root + 'bus/newBus',
        searchBus1: _api_root + 'bus/searchBus1',
        searchBus2: _api_root + 'bus/searchBus2',
        searchBus3: _api_root + 'bus/searchBus3',
        searchBus4: _api_root + 'bus/searchBus4',
        getBusInfo: _api_root + 'bus/getBusInfo',
        changeStatus: _api_root + 'bus/changeStatus'
    },
    order: {
        pay: _api_root + 'order/pay',
        payok: _api_root + 'order/payok',
        confirmReceived: _api_root + 'order/confirmReceived'
    },
    other: {
        getSite: _api_root + 'other/getSite',
        uploadImage: _api_root + 'other/uploadImage',
        sendFeedback: _api_root + 'other/sendFeedback',
        searchSchool: _api_root + 'other/searchSchool',
        selectAllSchool: _api_root + 'other/selectAllSchool'
    }
};
module.exports = api;