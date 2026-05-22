<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>

    <link
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="/webbanhang/public/css/duel-theme.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark duel-navbar sticky-top">
        <div class="container">

            <a class="navbar-brand duel-brand" href="/webbanhang/Product/list">
                ⚡ Product Duel
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation">

                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav ml-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="/webbanhang/Product/list">
                            Sản phẩm
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/webbanhang/Product/add">
                            Thêm sản phẩm
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/webbanhang/Category/list">
                            Danh mục
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/webbanhang/Category/add">
                            Thêm danh mục
                        </a>
                    </li>

                </ul>

            </div>

        </div>
    </nav>

    <section class="duel-hero">
        <div class="container">

            <span class="duel-kicker">
                Inventory Battle System
            </span>

            <h1 class="duel-title">
                Quản lý sản phẩm
            </h1>

            <p class="duel-subtitle">
                Giao diện quản trị sản phẩm theo phong cách card game:
                tối, nổi bật, dễ thao tác và tập trung vào danh sách sản phẩm,
                danh mục, hình ảnh, giá bán.
            </p>

            <div class="mt-4">
                <a href="/webbanhang/Product/add" class="btn btn-duel-primary mr-2 mb-2">
                    + Thêm sản phẩm
                </a>

                <a href="/webbanhang/Category/list" class="btn btn-duel-secondary mb-2">
                    Quản lý danh mục
                </a>
            </div>

        </div>
    </section>

    <main class="container duel-main"></main>