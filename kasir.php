<?php
include "utils/auth.php";
check_login();
include "layout/header.php";
include "layout/navbar.php";
?>

<div class="container mt-5">
    <h2 class="text-center">Kasir</h2>

    <!-- Daftar Produk -->
    <h3>Daftar Produk</h3>
    <table class="table table-bordered table-striped table-full-width">
        <thead class="thead-dark">
            <tr>
                <th>ID Produk</th>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga Jual</th>
                <th>Stok</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="product-list">
            <!-- Data produk akan di-load di sini -->
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center" id="pagination">
            <!-- Pagination links akan di-load di sini -->
        </ul>
    </nav>

    <!-- Keranjang Belanja -->
    <h3>Keranjang Belanja</h3>
    <table class="table table-bordered table-striped table-full-width" id="cart-table">
        <thead class="thead-dark">
            <tr>
                <th>Nama Produk</th>
                <th>Harga Jual</th>
                <th>Qty</th>
                <th>Total Harga</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="cart-list">
            <!-- Produk dalam keranjang akan muncul di sini -->
        </tbody>
    </table>

    <!-- Total Belanja -->
    <div class="row">
        <div class="col-md-4">
            <h4>Total Belanja: <span id="total-belanja">0</span></h4>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="total-bayar">Total Bayar:</label>
                <input type="number" class="form-control" id="total-bayar">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="kembalian">Kembalian:</label>
                <input type="number" class="form-control" id="kembalian" readonly>
            </div>
        </div>
    </div>
    <button class="btn btn-success" id="bayar-button">Bayar</button>

    <div id="message"></div>
</div>

<?php
include "layout/footer.php";
?>

<script>
    let cart = [];

    function updateCart() {
        let cartList = document.getElementById('cart-list');
        cartList.innerHTML = '';
        let totalBelanja = 0;

        cart.forEach((item, index) => {
            let totalHarga = item.harga * item.qty;
            totalBelanja += totalHarga;

            let row = `
                <tr>
                    <td>${item.nama}</td>
                    <td>${item.harga}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="decreaseQty(${index})">-</button>
                        ${item.qty}
                        <button class="btn btn-sm btn-success" onclick="increaseQty(${index})">+</button>
                    </td>
                    <td>${totalHarga}</td>
                    <td><button class="btn btn-danger btn-sm" onclick="removeFromCart(${index})">Hapus</button></td>
                </tr>
            `;
            cartList.innerHTML += row;
        });

        document.getElementById('total-belanja').innerText = totalBelanja;
    }

    function decreaseQty(index) {
        if (cart[index].qty > 1) {
            cart[index].qty--;
        } else {
            cart.splice(index, 1);
        }
        updateCart();
    }

    function increaseQty(index) {
        if (cart[index].qty < cart[index].stok) {
            cart[index].qty++;
        } else {
            showMessage('danger', 'Qty melebihi stok.');
        }
        updateCart();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCart();
    }

    function loadProducts(page = 1) {
        fetch(`controllers/produk_list.php?page=${page}`)
            .then(response => response.json())
            .then(data => {
                const productList = document.getElementById("product-list");
                const pagination = document.getElementById("pagination");
                productList.innerHTML = '';
                pagination.innerHTML = '';

                data.products.forEach(product => {
                    const row = `
                        <tr>
                            <td>${product.id_produk}</td>
                            <td>${product.kode_produk}</td>
                            <td>${product.nama_produk}</td>
                            <td>${product.kategori}</td>
                            <td>${product.harga_jual}</td>
                            <td>${product.stok}</td>
                            <td>
                                ${product.stok > 0 
                                    ? `<button class="btn btn-primary add-to-cart" data-id="${product.id_produk}" data-kode="${product.kode_produk}" data-nama="${product.nama_produk}" data-harga_beli="${product.harga_beli}" data-harga="${product.harga_jual}" data-stok="${product.stok}">Add to Cart</button>` 
                                    : `<button class="btn btn-secondary" disabled>Out of Stock</button>`}
                            </td>
                        </tr>
                    `;
                    productList.innerHTML += row;
                });

                document.querySelectorAll('.add-to-cart').forEach(button => {
                    button.addEventListener('click', function() {
                        let id = this.dataset.id;
                        let kode = this.dataset.kode;
                        let nama = this.dataset.nama;
                        let harga_beli = this.dataset.harga_beli;
                        let harga = parseFloat(this.dataset.harga);
                        let stok = parseInt(this.dataset.stok);

                        let found = false;
                        cart.forEach(item => {
                            if (item.id === id) {
                                if (item.qty < stok) {
                                    item.qty++;
                                } else {
                                    showMessage('danger', 'Qty melebihi stok.');
                                }
                                found = true;
                            }
                        });

                        if (!found) {
                            cart.push({
                                id,
                                kode,
                                nama,
                                harga_beli,
                                harga,
                                qty: 1,
                                stok
                            });
                        }

                        updateCart();
                    });
                });

                for (let i = 1; i <= data.pages; i++) {
                    const active = page === i ? 'active' : '';
                    const pageLink = `
                        <li class="page-item ${active}">
                            <a class="page-link" href="#" onclick="loadProducts(${i})">${i}</a>
                        </li>
                    `;
                    pagination.innerHTML += pageLink;
                }
            });
    }

    document.getElementById('total-bayar').addEventListener('input', function() {
        let totalBelanja = parseFloat(document.getElementById('total-belanja').innerText);
        let totalBayar = parseFloat(this.value);
        let kembalian = totalBayar - totalBelanja;

        document.getElementById('kembalian').value = kembalian;
    });

    document.getElementById('bayar-button').addEventListener('click', function() {
        let totalBelanja = parseFloat(document.getElementById('total-belanja').innerText);
        let totalBayar = parseFloat(document.getElementById('total-bayar').value);
        let kembalian = parseFloat(document.getElementById('kembalian').value);

        if (isNaN(totalBayar) || totalBayar < totalBelanja) {
            showMessage('danger', 'Total bayar tidak mencukupi.');
            return;
        }

        let nomorFaktur = 'INV' + Date.now();
        let tanggalBeli = new Date().toISOString().slice(0, 19).replace('T', ' ');

        let jualData = {
            nomor_faktur: nomorFaktur,
            tanggal_beli: tanggalBeli,
            total_belanja: totalBelanja,
            total_bayar: totalBayar,
            kembalian: kembalian,
            cart: cart
        };

        fetch('controllers/kasir_proses.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(jualData)
            })
            .then(response => response.json())
            .then(data => {
                showMessage(data.status === 'success' ? 'success' : 'danger', data.message);
                if (data.status === 'success') {
                    cart = [];
                    updateCart();
                    document.getElementById('total-bayar').value = '';
                    document.getElementById('kembalian').value = '';
                }
            })
            .catch(error => console.error('Error:', error));
    });

    function showMessage(type, message) {
        const messageDiv = document.getElementById('message');
        messageDiv.innerHTML = `
        <div class="toast toast-top-center align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

        // Initialize Bootstrap toast
        const toastElement = messageDiv.querySelector('.toast');
        const toast = new bootstrap.Toast(toastElement, {
            delay: 3000
        }); // Delay in milliseconds
        toast.show();
    }

    // Load produk pertama kali
    loadProducts();
</script>