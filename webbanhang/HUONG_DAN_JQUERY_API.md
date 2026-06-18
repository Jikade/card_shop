# Front-end quản lý sản phẩm bằng jQuery AJAX

## Các file chính

- `public/js/product-api.js`: lớp gọi REST API bằng `$.ajax()`.
- `app/views/product/list.php`: GET danh sách và DELETE sản phẩm.
- `app/views/product/add.php`: GET danh mục và POST sản phẩm.
- `app/views/product/edit.php`: GET dữ liệu và PUT sản phẩm.
- `app/views/product/show.php`: GET chi tiết sản phẩm.
- `public/css/product-api.css`: giao diện cho các trang API.

## API sử dụng

- `GET /webbanhang/api/product`
- `GET /webbanhang/api/product/{id}`
- `POST /webbanhang/api/product`
- `PUT /webbanhang/api/product/{id}`
- `DELETE /webbanhang/api/product/{id}`
- `GET /webbanhang/api/category`

POST, PUT và DELETE yêu cầu đăng nhập bằng tài khoản Admin vì API sử dụng PHP Session.

## Lưu ý jQuery

Dự án dùng bản đầy đủ:

```html
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
```

Không dùng `jquery.slim.min.js` vì bản Slim không có `$.ajax()`.
