<?php
include "layout/header.php";
include "layout/navbar.php";
?>

<div class="container mt-5">
    <h2 class="text-center">Laporan Penjualan</h2>
    <div class="form-inline justify-content-center mb-3">
        <label for="start_date" class="mr-2">Dari Tanggal:</label>
        <input type="date" id="start_date" class="form-control mr-3">
        <label for="end_date" class="mr-2">Sampai Tanggal:</label>
        <input type="date" id="end_date" class="form-control mr-3">
        <button id="filterButton" class="btn btn-primary mr-3">Filter</button>
        <button id="sortLatestButton" class="btn btn-info mr-2"><i class="fas fa-sort-amount-down"></i> Terbaru</button>
        <button id="sortOldestButton" class="btn btn-info"><i class="fas fa-sort-amount-up"></i> Terlama</button>
    </div>

    <table class="table table-bordered table-striped table-full-width">
        <thead class="thead-dark">
            <tr>
                <th onclick="sortTable('tanggal_beli')">Tanggal Beli</th>
                <th onclick="sortTable('nama_produk')">Nama Produk</th>
                <th onclick="sortTable('harga_modal')">Harga Modal</th>
                <th onclick="sortTable('harga_jual')">Harga Jual</th>
                <th onclick="sortTable('qty')">Qty</th>
                <th onclick="sortTable('total_harga')">Total Harga</th>
                <th onclick="sortTable('keuntungan')">Keuntungan</th>
            </tr>
        </thead>
        <tbody id="reportTableBody">
            <!-- Data laporan akan di-load di sini -->
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center" id="pagination">
            <!-- Pagination links akan di-load di sini -->
        </ul>
    </nav>

    <!-- Tabel Keuntungan Bulanan dan Barang Terlaris -->
    <h3 class="text-center mt-5">Keuntungan Bulanan dan Barang Terlaris</h3>
    <table class="table table-bordered table-striped table-full-width">
        <thead class="thead-dark">
            <tr>
                <th onclick="sortTableMonthly('bulan')">Bulan</th>
                <th onclick="sortTableMonthly('barang_terlaris')">Nama Produk Terlaris</th>
                <th onclick="sortTableMonthly('qty_terjual')">Qty Terjual</th>
                <th onclick="sortTableMonthly('keuntungan')">Keuntungan</th>
            </tr>
        </thead>
        <tbody id="monthlyReportTableBody">
            <!-- Data keuntungan bulanan dan barang terlaris akan di-load di sini -->
        </tbody>
    </table>

    <!-- Total Keuntungan Bulanan -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="alert alert-info" role="alert">
                Total Keuntungan Bulan Ini: <span id="totalKeuntunganBulanIni">0</span>
            </div>
        </div>
    </div>
</div>

<?php
include "layout/footer.php";
?>

<script>
    let reports = [];
    let monthlyReports = [];
    let currentSortColumn = 'tanggal_beli';
    let currentSortOrder = 'asc';
    let currentMonthlySortColumn = 'bulan';
    let currentMonthlySortOrder = 'asc';

    const loadReportData = (page = 1, sort = 'latest') => {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const params = new URLSearchParams({
            page,
            sort,
            start_date: startDate,
            end_date: endDate
        });

        fetch('controllers/laporan_getdata.php?' + params.toString())
            .then(response => response.json())
            .then(data => {
                reports = data.reports;
                displayReports();
            });

        fetch('controllers/laporan_fetch_monthly_report.php')
            .then(response => response.json())
            .then(data => {
                monthlyReports = data.monthlyReports;
                document.getElementById('totalKeuntunganBulanIni').innerText = data.total_keuntungan_bulan_ini;
                displayMonthlyReports();
            });
    };

    const displayReports = () => {
        const tbody = document.getElementById('reportTableBody');
        tbody.innerHTML = '';

        reports.forEach(report => {
            const keuntungan = report.harga_jual - report.harga_modal;
            const row = `
                <tr>
                    <td>${report.tanggal_beli}</td>
                    <td>${report.nama_produk}</td>
                    <td>${report.harga_modal}</td>
                    <td>${report.harga_jual}</td>
                    <td>${report.qty}</td>
                    <td>${report.total_harga}</td>
                    <td>${keuntungan}</td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    };

    const displayMonthlyReports = () => {
        const tbody = document.getElementById('monthlyReportTableBody');
        tbody.innerHTML = '';

        monthlyReports.forEach(report => {
            const row = `
                <tr>
                    <td>${report.bulan}</td>
                    <td>${report.barang_terlaris}</td>
                    <td>${report.qty_terjual}</td>
                    <td>${report.keuntungan}</td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    };

    const sortTable = (column) => {
        currentSortOrder = currentSortColumn === column && currentSortOrder === 'asc' ? 'desc' : 'asc';
        currentSortColumn = column;

        reports.sort((a, b) => {
            if (currentSortOrder === 'asc') {
                return a[column] > b[column] ? 1 : -1;
            } else {
                return a[column] < b[column] ? 1 : -1;
            }
        });

        displayReports();
    };

    const sortTableMonthly = (column) => {
        currentMonthlySortOrder = currentMonthlySortColumn === column && currentMonthlySortOrder === 'asc' ? 'desc' : 'asc';
        currentMonthlySortColumn = column;

        monthlyReports.sort((a, b) => {
            if (currentMonthlySortOrder === 'asc') {
                return a[column] > b[column] ? 1 : -1;
            } else {
                return a[column] < b[column] ? 1 : -1;
            }
        });

        displayMonthlyReports();
    };

    document.getElementById('filterButton').addEventListener('click', () => loadReportData());
    document.getElementById('sortLatestButton').addEventListener('click', () => loadReportData(1, 'latest'));
    document.getElementById('sortOldestButton').addEventListener('click', () => loadReportData(1, 'oldest'));

    // Load initial data
    loadReportData();
</script>