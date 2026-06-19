(function (window, $) {
    'use strict';

    const API_BASE = '/webbanhang/api';
    const TOKEN_KEY = 'jwt_token';

    function getToken() {
        return window.localStorage.getItem(TOKEN_KEY) || '';
    }

    function setToken(token) {
        if (token) {
            window.localStorage.setItem(TOKEN_KEY, token);
        }
    }

    function clearToken() {
        window.localStorage.removeItem(TOKEN_KEY);
    }

    function request(options) {
        const token = getToken();
        const headers = $.extend({}, options.headers || {});

        if (token) {
            headers.Authorization = 'Bearer ' + token;
        }

        return $.ajax($.extend({
            dataType: 'json',
            contentType: 'application/json; charset=UTF-8',
            cache: false,
            headers: headers
        }, options));
    }

    window.AuthApi = {
        login: function (username, password) {
            return request({
                url: '/webbanhang/account/checkLogin',
                method: 'POST',
                data: JSON.stringify({
                    username: username,
                    password: password
                })
            }).done(function (response) {
                if (response && response.token) {
                    setToken(response.token);
                }
            });
        },

        logoutLocal: function () {
            clearToken();
        },

        getToken: getToken,
        setToken: setToken,
        clearToken: clearToken
    };

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

        createWithImage: function (formData) {
            return request({
                url: API_BASE + '/product',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false
            });
        },

        update: function (id, data) {
            return request({
                url: API_BASE + '/product/' + encodeURIComponent(id),
                method: 'PUT',
                data: JSON.stringify(data)
            });
        },

        updateWithImage: function (id, formData) {
            formData.set('_method', 'PUT');

            return request({
                url: API_BASE + '/product/' + encodeURIComponent(id),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false
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

    $(document).on('click', '.js-logout', function () {
        clearToken();
    });
})(window, jQuery);
