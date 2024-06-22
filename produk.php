<?php
include "utils/auth.php";
check_login();
include "layout/header.php";
include "layout/navbar.php";
?>

<div class="container mt-5">
    <h2 class="text-center">Manajemen Produk</h2>
    <button id="toggleFormButton" class="btn btn-primary mb-3">Tambah Produk</button>

    <div class="form-container" id="form-container" style="display: none;">
        <form id="produkForm">
            <input type="hidden" id="produkId" name="produkId">
            <div class="form-group">
                <label for="kode_produk">Kode Produk:</label>
                <input type="text" class="form-control" id="kode_produk" name="kode_produk" required>
            </div>
            <div class="form-group">
                <label for="nama_produk">Nama Produk:</label>
                <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
            </div>
            <div class="form-group">
                <label for="kategori">Kategori:</label>
                <select class="form-control" id="kategori" name="kategori" required>
                    <option value="makanan">Makanan</option>
                    <option value="minuman">Minuman</option>
                    <option value="cemilan">Cemilan</option>
                </select>
            </div>
            <div class="form-group">
                <label for="harga_beli">Harga Beli:</label>
                <input type="number" class="form-control" id="harga_beli" name="harga_beli" required>
            </div>
            <div class="form-group">
                <label for="harga_jual">Harga Jual:</label>
                <input type="number" class="form-control" id="harga_jual" name="harga_jual" required>
            </div>
            <div class="form-group">
                <label for="stok">Stok:</label>
                <input type="number" class="form-control" id="stok" name="stok" required>
            </div>
            <div class="form-group">
                <label for="satuan">Satuan:</label>
                <select class="form-control" id="satuan" name="satuan" required>
                    <option value="pcs">Pcs</option>
                    <option value="paket">Paket</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success" id="submitButton">Simpan</button>
        </form>
    </div>

    <h3 class="text-center">Daftar Produk</h3>
    <table class="table table-bordered table-striped table-responsive">
        <thead class="thead-dark">
            <tr>
                <th>ID Produk</th>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Stok</th>
                <th>Satuan</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="product-list">
            <!-- Data produk akan di-load di sini -->
        </tbody>
    </table>
</div>

<?php
include "layout/footer.php";
?>

<script>
    document.getElementById('toggleFormButton').addEventListener('click', function() {
        var formContainer = document.getElementById('form-container');
        if (formContainer.style.display === 'none' || formContainer.style.display === '') {
            formContainer.style.display = 'block';
            this.textContent = 'Sembunyikan Form';
        } else {
            formContainer.style.display = 'none';
            this.textContent = 'Tambah Produk';
            document.getElementById('produkForm').reset(); // Reset form saat menyembunyikan
            document.getElementById('produkId').value = ''; // Clear produkId field
        }
    });

    function loadProducts() {
        fetch('controllers/produk_list.php')
            .then(response => response.json())
            .then(data => {
                const productList = document.getElementById('product-list');
                productList.innerHTML = '';
                data.products.forEach(product => {
                    const row = `
                        <tr>
                            <td>${product.id_produk}</td>
                            <td>${product.kode_produk}</td>
                            <td>${product.nama_produk}</td>
                            <td>${product.kategori}</td>
                            <td>${product.harga_beli}</td>
                            <td>${product.harga_jual}</td>
                            <td>${product.stok}</td>
                            <td>${product.satuan}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="editProduct(${product.id_produk})">Edit</button>
                            </td>
                        </tr>
                    `;
                    productList.innerHTML += row;
                });
            });
    }

    function editProduct(id) {
        fetch(`controllers/produk_get.php?id=${id}`)
            .then(response => response.json())
            .then(product => {
                document.getElementById('produkId').value = product.id_produk;
                document.getElementById('kode_produk').value = product.kode_produk;
                document.getElementById('nama_produk').value = product.nama_produk;
                document.getElementById('kategori').value = product.kategori;
                document.getElementById('harga_beli').value = product.harga_beli;
                document.getElementById('harga_jual').value = product.harga_jual;
                document.getElementById('stok').value = product.stok;
                document.getElementById('satuan').value = product.satuan;

                var formContainer = document.getElementById('form-container');
                formContainer.style.display = 'block';
                document.getElementById('toggleFormButton').textContent = 'Sembunyikan Form';
            });
    }

    document.getElementById('produkForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        const jsonData = {};
        formData.forEach((value, key) => jsonData[key] = value);

        const isUpdate = jsonData.produkId !== '';

        fetch(isUpdate ? 'controllers/produk_update.php' : 'controllers/produk_create.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(jsonData)
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    loadProducts();
                    document.getElementById('produkForm').reset();
                    document.getElementById('produkId').value = ''; // Clear produkId field
                    var formContainer = document.getElementById('form-container');
                    formContainer.style.display = 'none';
                    document.getElementById('toggleFormButton').textContent = 'Tambah Produk';
                }
            });
    });

    // Load produk pertama kali
    loadProducts();
</script>