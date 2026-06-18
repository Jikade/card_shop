(function (window, $) {
    'use strict';

    const API_BASE = '/webbanhang/api';

    function request(options) {
        return $.ajax($.extend({
            dataType: 'json',
            contentType: 'application/json; charset=UTF-8',
            cache: false
        }, options));
    }

    window.ProductApi = {
        getAll: function () {
            return request({
                url: API_BASE + '/product',
                method: 'GET'
            });
        },

        getOne: function (id) {
            return request({
                url: API_BASE + '/product/' + encodeURIComponent(id),
                method: 'GET'
            });
        },

        create: function (data) {
            return request({
                url: API_BASE + '/product',
                method: 'POST',
                data: JSON.stringify(data)
            });
        },

        update: function (id, data) {
            return request({
                url: API_BASE + '/product/' + encodeURIComponent(id),
                method: 'PUT',
                data: JSON.stringify(data)
            });
        },

        remove: function (id) {
            return request({
                url: API_BASE + '/product/' + encodeURIComponent(id),
                method: 'DELETE'
            });
        },

        getCategories: function () {
            return request({
                url: API_BASE + '/category',
                method: 'GET'
            });
        },

        getErrorMessage: function (xhr, fallback) {
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    return xhr.responseJSON.message;
                }

                if (xhr.responseJSON.errors) {
                    return Object.values(xhr.responseJSON.errors).join('<br>');
                }
            }

            return fallback || 'Có lỗi xảy ra khi gọi API.';
        }
    };
})(window, jQuery);
