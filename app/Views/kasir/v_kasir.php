<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>

<div class="space-y-4">
    <!-- Header Banner -->
    <div class="card p-4 bg-gradient-to-r from-primary-700 via-primary to-primary-600 text-white flex flex-col sm:flex-row items-center justify-between gap-3 border border-primary-600 shadow-md rounded-[14px]">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 text-white flex items-center justify-center text-xl shrink-0">
                <i class="bi bi-receipt-cutoff"></i>
            </div>
            <div>
                <div class="font-bold text-xs text-white flex items-center gap-2">
                    <span>Terminal POS &bull; Kasir Penjualan</span>
                    <span class="badge bg-white/20 text-white text-[10px] py-0.5 px-2 rounded-full font-mono">Kasir: Online</span>
                </div>
                <span class="text-[11px] text-white/90">Sistem Input Transaksi Visual &amp; Katalog Belanja Kasir</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= getURL('riwayat') ?>" class="btn btn-sm bg-white/20 hover:bg-white/30 text-white border border-white/30 rounded-xl flex items-center gap-1.5 transition-all text-xs font-bold no-underline focus-visible:ring-2 focus-visible:ring-white focus-visible:outline-hidden">
                <i class="bi bi-clock-history"></i> Buka Riwayat Transaksi
            </a>
            <span class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-white/20 text-white flex items-center gap-1.5">
                <i class="bi bi-person-check-fill text-cyan-200"></i> <?= esc(getSession('fullname')) ?>
            </span>
        </div>
    </div>

    <!-- POS Layout: Split Screen Desktop (Kiri Katalog, Kanan Sticky Cart dengan Jarak Bersih) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start" style="gap: 1.5rem;">
        <!-- Kolom Kiri: Katalog Produk (Search + Pills Kategori + Card Grid Compact + Paginasi Server-Side) -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-3.5 min-w-0">
            <!-- Filter Bar: Search + Category Pills Tabs -->
            <div class="card p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700/90 shadow-xs space-y-2.5">
                <!-- Search Input Bar -->
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 dark:text-slate-500 text-sm">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="catalogSearch" class="w-full pl-9 pr-9 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 transition-all" placeholder="Cari nama barang atau kategori...">
                        <button type="button" id="btnClearSearch" class="hidden absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" title="Hapus pencarian" aria-label="Hapus pencarian">
                            <i class="bi bi-x-circle-fill text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Category Pills (Tabs Horizontal Ringkas) -->
                <div class="relative">
                    <div id="categoryPills" class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-xs select-none">
                        <button type="button" class="category-pill active inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg font-bold transition-all shrink-0 cursor-pointer focus-visible:ring-2 focus-visible:ring-sky-500 bg-sky-600 text-white shadow-xs" data-category-id="">
                            <i class="bi bi-grid-fill text-xs"></i>
                            <span>Semua Kategori</span>
                        </button>
                        <?php if (!empty($categories) && is_array($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <button type="button" class="category-pill inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg font-medium transition-all shrink-0 cursor-pointer focus-visible:ring-2 focus-visible:ring-sky-500 bg-slate-100 dark:bg-slate-700/60 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300" data-category-id="<?= esc($cat['categoryid']) ?>">
                                    <span><?= esc($cat['categoryname']) ?></span>
                                </button>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Loading Skeleton Grid (12 Card Ringkas) -->
            <div id="catalogSkeleton" class="pos-catalog-grid">
                <?php for ($i = 0; $i < 12; $i++): ?>
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700/80 p-2.5 space-y-2 animate-pulse">
                        <div class="w-full h-24 sm:h-28 bg-slate-200 dark:bg-slate-700 rounded-lg"></div>
                        <div class="h-3 w-14 bg-slate-200 dark:bg-slate-700 rounded"></div>
                        <div class="h-3.5 w-full bg-slate-200 dark:bg-slate-700 rounded"></div>
                        <div class="flex items-center justify-between pt-1.5 border-t border-slate-100 dark:border-slate-700/50">
                            <div class="h-3.5 w-16 bg-slate-200 dark:bg-slate-700 rounded"></div>
                            <div class="w-7 h-7 bg-slate-200 dark:bg-slate-700 rounded-lg"></div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <!-- Empty State Katalog -->
            <div id="catalogEmpty" class="hidden text-center py-12 px-4 bg-white dark:bg-slate-800 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                <div class="w-12 h-12 mx-auto mb-2.5 rounded-xl bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-cyan-400 flex items-center justify-center text-xl">
                    <i class="bi bi-search"></i>
                </div>
                <h4 class="font-bold text-xs sm:text-sm text-slate-800 dark:text-slate-200 mb-1">Produk Tidak Ditemukan</h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-3">Coba ubah kata kunci pencarian atau pilih tab kategori lain.</p>
                <button type="button" onclick="resetFilterCatalog()" class="btn btn-sm btn-soft-primary px-3 py-1.5 rounded-lg font-bold text-xs inline-flex items-center gap-1.5">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                </button>
            </div>

            <!-- Product Cards Grid (3 sampai 4 Card per Baris) -->
            <div id="catalogGrid" class="hidden pos-catalog-grid">
                <!-- Di-render dinamis oleh JavaScript -->
            </div>

            <!-- Server-Side Pagination Bar -->
            <div id="catalogPaginationBar" class="card p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700/90 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <!-- Info Paginasi & Pilihan Per-Page -->
                <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto justify-between sm:justify-start">
                    <div class="flex items-center gap-2">
                        <label for="perPageSelect" class="text-slate-500 dark:text-slate-400 font-medium">Tampilkan:</label>
                        <select id="perPageSelect" class="form-select text-xs py-1 px-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 font-bold focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-500">
                            <option value="12" selected>12 / hal</option>
                            <option value="24">24 / hal</option>
                            <option value="48">48 / hal</option>
                        </select>
                    </div>
                    <span id="paginationInfo" class="text-slate-500 dark:text-slate-400 tabular-nums font-medium">Memuat katalog...</span>
                </div>

                <!-- Navigasi Halaman -->
                <nav id="paginationNav" aria-label="Navigasi Halaman Produk" class="inline-flex items-center gap-1 w-full sm:w-auto justify-center sm:justify-end">
                    <!-- Tombol halaman di-render dinamis -->
                </nav>
            </div>
        </div>

        <!-- Kolom Kanan: Keranjang Belanja & Pembayaran (Sticky Desktop) -->
        <div class="lg:col-span-5 xl:col-span-4 pos-sticky-cart space-y-4" style="position: sticky; position: -webkit-sticky; top: 80px; align-self: flex-start; z-index: 30;">
            <div class="card shadow-sm border border-slate-200/90 dark:border-slate-700 rounded-xl overflow-hidden bg-white dark:bg-slate-800 flex flex-col">
                <!-- Header Keranjang -->
                <div class="bg-slate-100/90 dark:bg-slate-800/90 text-slate-800 dark:text-slate-100 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-3.5 py-2.5 font-bold text-xs sm:text-sm">
                    <div class="flex items-center gap-2 text-slate-900 dark:text-white">
                        <i class="bi bi-bag-check-fill text-sky-600 dark:text-sky-400 text-base"></i>
                        <span>Keranjang Belanja</span>
                        <span id="cartCountBadge" class="badge badge-soft-primary badge-xs tabular-nums font-mono font-bold">0 Item</span>
                    </div>
                    <button type="button" onclick="kosongkanKeranjang()" class="text-[11px] text-rose-600 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 font-semibold flex items-center gap-1 cursor-pointer transition-colors focus-visible:ring-2 focus-visible:ring-rose-500 rounded px-1" title="Kosongkan keranjang">
                        <i class="bi bi-trash"></i> Kosongkan
                    </button>
                </div>

                <div class="p-3.5 space-y-3.5">
                    <!-- Placeholder Keranjang Kosong -->
                    <div id="cartEmpty" class="text-slate-500 dark:text-slate-400 text-center py-8 text-xs bg-slate-50 dark:bg-slate-900/50 rounded-lg border border-dashed border-slate-200 dark:border-slate-700">
                        <i class="bi bi-cart-x text-2xl text-slate-400 dark:text-slate-600 block mb-1.5"></i>
                        <p class="font-medium text-slate-700 dark:text-slate-300 mb-0.5">Keranjang Masih Kosong</p>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Klik tombol <strong class="text-sky-600 dark:text-cyan-400">+</strong> pada kartu produk di kiri.</p>
                    </div>

                    <!-- List Item Keranjang (Scrollable) -->
                    <div id="cartContainer" class="hidden">
                        <div class="max-h-[260px] xl:max-h-[300px] overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700/60 pr-1 scrollbar-thin space-y-1" id="cartList">
                            <!-- Di-render dinamis oleh JavaScript -->
                        </div>
                    </div>

                    <!-- Section Pembayaran Kasir -->
                    <div class="pt-2.5 border-t border-slate-200 dark:border-slate-700 space-y-3">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-900/60">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider font-bold text-sky-800 dark:text-sky-300 block">TOTAL TAGIHAN:</span>
                                <span class="text-[10px] text-sky-600 dark:text-sky-400">Total belanja pelanggan</span>
                            </div>
                            <span class="text-2xl sm:text-3xl font-extrabold font-mono text-sky-600 dark:text-cyan-400 tabular-nums" id="cartTotal">Rp 0</span>
                        </div>

                        <div class="space-y-2.5">
                            <div>
                                <label for="inputBayar" class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1">Uang Diterima (Tunai)</label>
                                <div class="flex items-center h-[36px] rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 overflow-hidden shadow-2xs focus-within:border-sky-500 focus-within:ring-1 focus-within:ring-sky-500 transition-all">
                                    <span class="px-2.5 h-full flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-500 dark:text-slate-400 border-r border-slate-300 dark:border-slate-600 select-none">Rp</span>
                                    <input type="number" id="inputBayar" class="w-full h-full text-xs font-mono font-bold px-2.5 text-slate-900 dark:text-white bg-transparent focus:outline-none no-spinner tabular-nums" placeholder="0" min="0">
                                </div>
                                <div class="flex flex-wrap gap-1 mt-1.5">
                                    <button type="button" onclick="setNominalBayar('pas')" class="btn btn-soft-secondary py-0.5 px-2 text-[10px] rounded-md font-bold focus-visible:ring-2 focus-visible:ring-sky-500">Uang Pas</button>
                                    <button type="button" onclick="setNominalBayar(10000)" class="btn btn-soft-secondary py-0.5 px-2 text-[10px] rounded-md font-mono focus-visible:ring-2 focus-visible:ring-sky-500">10.000</button>
                                    <button type="button" onclick="setNominalBayar(20000)" class="btn btn-soft-secondary py-0.5 px-2 text-[10px] rounded-md font-mono focus-visible:ring-2 focus-visible:ring-sky-500">20.000</button>
                                    <button type="button" onclick="setNominalBayar(50000)" class="btn btn-soft-secondary py-0.5 px-2 text-[10px] rounded-md font-mono focus-visible:ring-2 focus-visible:ring-sky-500">50.000</button>
                                    <button type="button" onclick="setNominalBayar(100000)" class="btn btn-soft-secondary py-0.5 px-2 text-[10px] rounded-md font-mono focus-visible:ring-2 focus-visible:ring-sky-500">100.000</button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1">Kembalian</label>
                                <div class="p-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex items-center justify-between h-[36px]">
                                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Kembali:</span>
                                    <span id="labelKembalian" class="font-mono font-bold text-sm text-slate-500 dark:text-slate-400 tabular-nums">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Form Submit Transaksi -->
                        <form id="formTransaksi" class="pt-1.5">
                            <input type="hidden" name="items" id="hiddenItems">
                            <input type="hidden" id="csrf_token_form" name="<?= csrf_token() ?>">
                            <button type="submit" id="btnSimpan" class="btn btn-primary w-full py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-2 shadow-md opacity-50 cursor-not-allowed transition-all focus-visible:ring-2 focus-visible:ring-sky-500" disabled>
                                <i class="bi bi-wallet2 text-base"></i> Selesaikan &amp; Simpan Transaksi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('template/v_footer') ?>

<script>
// State Management
var cart = [];
var currentTotalBelanja = 0;
var catalogData = [];
var catalogFilter = {
    search: '',
    categoryId: '',
    page: 1,
    perPage: 12,
    totalPages: 1,
    totalRecords: 0
};
var searchDebounceTimer = null;

// Helper Format Rupiah
function formatRupiahJS(nominal) {
    var val = parseFloat(nominal) || 0;
    return 'Rp ' + val.toLocaleString('id-ID');
}

// Inisialisasi Aplikasi Saat DOM Siap
$(document).ready(function() {
    loadCatalog();

    // Event Listener Search Input dengan Debounce 300ms
    $('#catalogSearch').on('input', function() {
        var val = $(this).val().trim();
        catalogFilter.search = val;
        catalogFilter.page = 1;

        if (val.length > 0) {
            $('#btnClearSearch').removeClass('hidden');
        } else {
            $('#btnClearSearch').addClass('hidden');
        }

        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(function() {
            loadCatalog();
        }, 300);
    });

    // Event Clear Search
    $('#btnClearSearch').on('click', function() {
        $('#catalogSearch').val('');
        catalogFilter.search = '';
        catalogFilter.page = 1;
        $(this).addClass('hidden');
        loadCatalog();
    });

    // Event Klik Category Pills
    $('#categoryPills').on('click', '.category-pill', function() {
        var catId = $(this).data('category-id') || '';
        catalogFilter.categoryId = catId;
        catalogFilter.page = 1;

        // Update active class pada pills
        $('.category-pill').removeClass('active bg-sky-600 text-white shadow-xs font-bold')
            .addClass('bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 font-medium');

        $(this).addClass('active bg-sky-600 text-white shadow-xs font-bold')
            .removeClass('bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 font-medium');

        loadCatalog();
    });

    // Event Pilihan Per Page
    $('#perPageSelect').on('change', function() {
        catalogFilter.perPage = parseInt($(this).val()) || 12;
        catalogFilter.page = 1;
        loadCatalog();
    });

    // Event Input Nominal Bayar
    $('#inputBayar').on('input change', function() {
        hitungKembalian();
    });

    // Submit Transaksi Kasir
    $('#formTransaksi').on('submit', function(e) {
        e.preventDefault();
        if (cart.length === 0) {
            showError('Keranjang belanja masih kosong.');
            return;
        }
        var bayar = parseFloat($('#inputBayar').val()) || 0;
        if (bayar > 0 && bayar < currentTotalBelanja) {
            showError('Uang yang diterima kurang dari total belanja.');
            return;
        }

        Swal.fire({
            title: 'Simpan Transaksi Kasir?',
            text: 'Total: ' + formatRupiahJS(currentTotalBelanja) + '. Pastikan pembayaran sudah sesuai.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0284c7',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-check-circle-fill"></i> Ya, Simpan Transaksi',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'hyperui-swal',
                title: 'hyperui-swal-title',
                htmlContainer: 'hyperui-swal-text'
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                prosesSimpanTransaksi();
            }
        });
    });
});

// Reset Filter Pencarian & Kategori
function resetFilterCatalog() {
    $('#catalogSearch').val('');
    $('#btnClearSearch').addClass('hidden');
    catalogFilter.search = '';
    catalogFilter.categoryId = '';
    catalogFilter.page = 1;

    $('.category-pill').removeClass('active bg-sky-600 text-white shadow-xs font-bold')
        .addClass('bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 font-medium');
    $('.category-pill[data-category-id=""]').addClass('active bg-sky-600 text-white shadow-xs font-bold')
        .removeClass('bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300 font-medium');

    loadCatalog();
}

// Memuat Katalog Produk dari Server (Server-Side Pagination)
function loadCatalog() {
    $('#catalogSkeleton').removeClass('hidden').show();
    $('#catalogGrid').addClass('hidden').hide();
    $('#catalogEmpty').addClass('hidden').hide();

    $.ajax({
        type: 'POST',
        url: '<?= getURL('kasir/products') ?>',
        data: {
            search: catalogFilter.search,
            categoryid: catalogFilter.categoryId,
            page: catalogFilter.page,
            per_page: catalogFilter.perPage,
            csrf_token: decrypter($("#csrf_token").val())
        },
        dataType: 'json',
        success: function(response) {
            if (response.csrfToken) {
                $("#csrf_token").val(encrypter(response.csrfToken));
            }

            $('#catalogSkeleton').addClass('hidden').hide();

            if (response.success && response.data && response.data.length > 0) {
                catalogData = response.data;
                catalogFilter.totalRecords = response.pagination.total;
                catalogFilter.totalPages = response.pagination.total_pages;
                catalogFilter.page = response.pagination.page;

                renderCatalogGrid(catalogData);
                renderPagination(response.pagination);
                $('#catalogGrid').removeClass('hidden').show();
                $('#catalogEmpty').addClass('hidden').hide();
            } else {
                catalogData = [];
                catalogFilter.totalRecords = 0;
                catalogFilter.totalPages = 1;

                $('#catalogGrid').empty().addClass('hidden').hide();
                $('#catalogEmpty').removeClass('hidden').show();
                $('#paginationInfo').text('Tidak ada produk yang sesuai');
                $('#paginationNav').empty();
            }
        },
        error: function() {
            $('#catalogSkeleton').addClass('hidden').hide();
            $('#catalogGrid').addClass('hidden').hide();
            $('#catalogEmpty').removeClass('hidden').show();
            showError('Gagal memuat katalog produk dari server.');
        }
    });
}

// Mapping Ikon dan Aksen Warna per Kategori Produk
var categoryThemes = {
    1:  { icon: 'bi-basket2-fill',     label: 'Sembako',      bg: 'bg-amber-500/10 dark:bg-amber-500/20',   text: 'text-amber-600 dark:text-amber-400' },
    2:  { icon: 'bi-cookie',           label: 'Snack',        bg: 'bg-orange-500/10 dark:bg-orange-500/20', text: 'text-orange-600 dark:text-orange-400' },
    3:  { icon: 'bi-pencil-square',    label: 'Alat Tulis',   bg: 'bg-blue-500/10 dark:bg-blue-500/20',     text: 'text-blue-600 dark:text-blue-400' },
    4:  { icon: 'bi-fire',             label: 'Bumbu Dapur',  bg: 'bg-rose-500/10 dark:bg-rose-500/20',     text: 'text-rose-600 dark:text-rose-400' },
    5:  { icon: 'bi-cup-straw',        label: 'Minuman',      bg: 'bg-sky-500/10 dark:bg-sky-500/20',       text: 'text-sky-600 dark:text-cyan-400' },
    6:  { icon: 'bi-egg-fried',        label: 'Makanan',      bg: 'bg-emerald-500/10 dark:bg-emerald-500/20', text: 'text-emerald-600 dark:text-emerald-400' },
    7:  { icon: 'bi-capsule',          label: 'Obat',         bg: 'bg-teal-500/10 dark:bg-teal-500/20',     text: 'text-teal-600 dark:text-teal-400' },
    8:  { icon: 'bi-stars',            label: 'Perawatan',    bg: 'bg-purple-500/10 dark:bg-purple-500/20', text: 'text-purple-600 dark:text-purple-400' },
    9:  { icon: 'bi-house-heart-fill', label: 'Rumah Tangga', bg: 'bg-indigo-500/10 dark:bg-indigo-500/20', text: 'text-indigo-600 dark:text-indigo-400' },
    10: { icon: 'bi-controller',       label: 'Mainan',       bg: 'bg-pink-500/10 dark:bg-pink-500/20',     text: 'text-pink-600 dark:text-pink-400' }
};
var defaultCatTheme = { icon: 'bi-box-seam-fill', label: 'Produk', bg: 'bg-slate-500/10 dark:bg-slate-500/20', text: 'text-slate-600 dark:text-slate-400' };

// Render Card Grid Produk (Ukuran Compact, Elegan & Responsif)
function renderCatalogGrid(items) {
    var grid = $('#catalogGrid');
    grid.empty();

    items.forEach(function(item) {
        var cartItem = cart.find(function(c) { return c.id === item.id_barang; });
        var inCartQty = cartItem ? cartItem.qty : 0;
        var badgeCartHtml = inCartQty > 0
            ? `<span class="badge bg-sky-600 text-white text-[9px] font-mono font-bold px-1.5 py-0.5 rounded-full shadow-xs absolute top-1.5 right-1.5 z-10">${inCartQty}x di keranjang</span>`
            : '';

        var catTheme = categoryThemes[item.categoryid] || defaultCatTheme;
        var mediaHtml = '';

        if (item.foto && String(item.foto).trim() !== '') {
            mediaHtml = `
                <img src="${item.image_url}" alt="${item.nama_barang}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" loading="lazy" onerror="this.outerHTML='<div class=\\'w-full h-full flex flex-col items-center justify-center ${catTheme.bg} ${catTheme.text} transition-transform group-hover:scale-105\\'><i class=\\'bi ${catTheme.icon} text-3xl mb-1\\'></i><span class=\\'text-[9px] font-bold tracking-wider uppercase opacity-80\\'>${catTheme.label}</span></div>'">
            `;
        } else {
            mediaHtml = `
                <div class="w-full h-full flex flex-col items-center justify-center ${catTheme.bg} ${catTheme.text} transition-transform duration-200 group-hover:scale-105 select-none">
                    <i class="bi ${catTheme.icon} text-3xl sm:text-4xl mb-1"></i>
                    <span class="text-[9px] font-bold tracking-wider uppercase opacity-80">${catTheme.label}</span>
                </div>
            `;
        }

        var cardHtml = `
            <div class="group relative flex flex-col bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700/90 overflow-hidden shadow-xs hover:shadow-md hover:border-sky-500 dark:hover:border-sky-500 transition-all duration-150 cursor-pointer product-card" data-id="${item.id_barang}">
                <!-- Thumbnail Foto atau Icon Kategori Ringkas -->
                <div class="relative w-full h-24 sm:h-28 bg-slate-100 dark:bg-slate-900 overflow-hidden shrink-0 border-b border-slate-100 dark:border-slate-700/50">
                    ${mediaHtml}
                    <!-- Category Chip -->
                    <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-900/80 text-white backdrop-blur-xs shadow-2xs truncate max-w-[75%]">
                        ${item.categoryname}
                    </span>
                    <!-- In-Cart Badge -->
                    <div id="cart-indicator-${item.id_barang}">${badgeCartHtml}</div>
                </div>

                <!-- Konten Body Card -->
                <div class="p-2.5 flex-1 flex flex-col justify-between gap-1.5">
                    <div>
                        <h4 class="font-bold text-xs text-slate-800 dark:text-slate-100 line-clamp-2 leading-tight group-hover:text-sky-600 dark:group-hover:text-cyan-400 transition-colors h-[2rem]" title="${item.nama_barang}">
                            ${item.nama_barang}
                        </h4>
                        <span class="text-[10px] text-slate-400 font-mono font-medium block mt-0.5">
                            /${item.satuan}
                        </span>
                    </div>

                    <!-- Row Harga & Tombol Tambah Ringkas -->
                    <div class="flex items-center justify-between gap-1 pt-1.5 border-t border-slate-100 dark:border-slate-700/60 mt-auto">
                        <span class="font-mono font-extrabold text-xs sm:text-sm text-sky-600 dark:text-cyan-400 tabular-nums">
                            ${formatRupiahJS(item.harga_jual)}
                        </span>
                        <button type="button" onclick="event.stopPropagation(); tambahItemLangsung(${item.id_barang})" class="btn-add-product inline-flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-sky-600 hover:bg-sky-700 text-white active:scale-90 transition-all shadow-xs cursor-pointer focus-visible:ring-2 focus-visible:ring-sky-500" title="Tambah ke keranjang" aria-label="Tambah ${item.nama_barang} ke keranjang">
                            <i class="bi bi-plus text-base font-extrabold"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        grid.append(cardHtml);
    });

    // Event Klik Seluruh Area Card Produk untuk Tambah ke Keranjang
    $('.product-card').off('click').on('click', function() {
        var id = $(this).data('id');
        tambahItemLangsung(id);
    });
}

// Render Navigasi Paginasi Server-Side
function renderPagination(p) {
    var info = $('#paginationInfo');
    var nav = $('#paginationNav');
    nav.empty();

    var start = ((p.page - 1) * p.per_page) + 1;
    var end = Math.min(p.page * p.per_page, p.total);
    info.text(`Menampilkan ${start}-${end} dari ${p.total} produk`);

    if (p.total_pages <= 1) {
        return;
    }

    // Tombol Sebelumnya
    var prevDisabled = p.page <= 1 ? 'disabled opacity-40 cursor-not-allowed' : 'hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer';
    var prevBtn = `<button type="button" onclick="gantiHalaman(${p.page - 1})" class="px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 ${prevDisabled} transition-all focus-visible:ring-2 focus-visible:ring-sky-500" ${p.page <= 1 ? 'disabled' : ''} aria-label="Halaman Sebelumnya">
        <i class="bi bi-chevron-left text-xs"></i>
    </button>`;
    nav.append(prevBtn);

    // Nomor Halaman
    var maxButtons = 5;
    var startPage = Math.max(1, p.page - 2);
    var endPage = Math.min(p.total_pages, startPage + maxButtons - 1);
    if (endPage - startPage < maxButtons - 1) {
        startPage = Math.max(1, endPage - maxButtons + 1);
    }

    if (startPage > 1) {
        nav.append(`<button type="button" onclick="gantiHalaman(1)" class="w-7 h-7 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-mono text-xs transition-all focus-visible:ring-2 focus-visible:ring-sky-500">1</button>`);
        if (startPage > 2) {
            nav.append(`<span class="px-1 text-slate-400">&hellip;</span>`);
        }
    }

    for (var i = startPage; i <= endPage; i++) {
        var activeClass = i === p.page
            ? 'bg-sky-600 text-white font-bold border-sky-600 shadow-2xs'
            : 'border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-mono';

        var pageBtn = `<button type="button" onclick="gantiHalaman(${i})" class="w-7 h-7 rounded-lg border text-xs transition-all focus-visible:ring-2 focus-visible:ring-sky-500 ${activeClass}">${i}</button>`;
        nav.append(pageBtn);
    }

    if (endPage < p.total_pages) {
        if (endPage < p.total_pages - 1) {
            nav.append(`<span class="px-1 text-slate-400">&hellip;</span>`);
        }
        nav.append(`<button type="button" onclick="gantiHalaman(${p.total_pages})" class="w-7 h-7 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-mono text-xs transition-all focus-visible:ring-2 focus-visible:ring-sky-500">${p.total_pages}</button>`);
    }

    // Tombol Selanjutnya
    var nextDisabled = p.page >= p.total_pages ? 'disabled opacity-40 cursor-not-allowed' : 'hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer';
    var nextBtn = `<button type="button" onclick="gantiHalaman(${p.page + 1})" class="px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 ${nextDisabled} transition-all focus-visible:ring-2 focus-visible:ring-sky-500" ${p.page >= p.total_pages ? 'disabled' : ''} aria-label="Halaman Selanjutnya">
        <i class="bi bi-chevron-right text-xs"></i>
    </button>`;
    nav.append(nextBtn);
}

// Navigasi Ganti Halaman
function gantiHalaman(hal) {
    if (hal < 1 || hal > catalogFilter.totalPages || hal === catalogFilter.page) return;
    catalogFilter.page = hal;
    loadCatalog();
}

// Menambah Item ke Keranjang dari Katalog
function tambahItemLangsung(idBarang) {
    var item = catalogData.find(function(c) { return c.id_barang === idBarang; });
    if (!item) return;

    var harga = parseFloat(item.harga_jual) || 0;
    var existingIndex = cart.findIndex(function(c) { return c.id === idBarang; });

    if (existingIndex > -1) {
        cart[existingIndex].qty += 1;
        cart[existingIndex].jumlah += 1;
        cart[existingIndex].subtotal = cart[existingIndex].qty * cart[existingIndex].harga;
    } else {
        cart.push({
            id_barang: item.id_barang,
            id: item.id_barang,
            nama_barang: item.nama_barang,
            satuan: item.satuan || 'pcs',
            image_url: item.image_url,
            harga: harga,
            jumlah: 1,
            qty: 1,
            subtotal: harga
        });
    }

    renderCart();
    updateCardBadge(idBarang);
}

// Update Badge Counter pada Card Katalog
function updateCardBadge(idBarang) {
    var cartItem = cart.find(function(c) { return c.id === idBarang; });
    var container = $('#cart-indicator-' + idBarang);
    if (!container.length) return;

    if (cartItem && cartItem.qty > 0) {
        container.html(`<span class="badge bg-sky-600 text-white text-[9px] font-mono font-bold px-1.5 py-0.5 rounded-full shadow-xs absolute top-1.5 right-1.5 z-10">${cartItem.qty}x di keranjang</span>`);
    } else {
        container.empty();
    }
}

// Render Tampilan Keranjang Belanja
function renderCart() {
    var list = $('#cartList');
    list.empty();
    var total = 0;
    var totalQty = 0;

    if (cart.length === 0) {
        $('#cartEmpty').removeClass('hidden');
        $('#cartContainer').addClass('hidden');
        $('#btnSimpan').addClass('opacity-50 cursor-not-allowed').prop('disabled', true);
        $('#cartCountBadge').text('0 Item');
        $('#cartTotal').text('Rp 0');
        currentTotalBelanja = 0;
        hitungKembalian();

        // Bersihkan seluruh indicator di catalog cards
        catalogData.forEach(function(item) {
            updateCardBadge(item.id_barang);
        });
        return;
    }

    $('#cartEmpty').addClass('hidden');
    $('#cartContainer').removeClass('hidden');
    $('#btnSimpan').removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);

    cart.forEach(function(item, index) {
        total += item.subtotal;
        totalQty += item.qty;

        var rowHtml = `
            <div class="py-2 flex items-center justify-between gap-2.5">
                <!-- Info Produk & Thumbnail Ringkas -->
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <div class="w-9 h-9 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-700 shrink-0 border border-slate-200 dark:border-slate-600 flex items-center justify-center">
                        <img src="${item.image_url}" alt="${item.nama_barang}" class="w-full h-full object-cover" onerror="this.src='<?= base_url('public/images/default-product.svg') ?>'">
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="font-bold text-xs text-slate-800 dark:text-slate-100 block truncate leading-tight" title="${item.nama_barang}">${item.nama_barang}</span>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400 mt-0.5">
                            <span class="font-mono text-sky-600 dark:text-cyan-400 font-bold">${formatRupiahJS(item.harga)}</span>
                            <span>&bull;</span>
                            <span class="font-mono">${item.satuan}</span>
                        </div>
                    </div>
                </div>

                <!-- Stepper Qty & Subtotal -->
                <div class="flex items-center gap-2 shrink-0">
                    <div class="inline-flex items-center h-6 rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 overflow-hidden shadow-2xs">
                        <button type="button" onclick="updateQty(${index}, ${item.qty - 1})" class="w-5 h-full flex items-center justify-center bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition-colors cursor-pointer select-none" title="Kurangi Qty">-</button>
                        <input type="number" min="1" value="${item.qty}" onchange="updateQty(${index}, this.value)" class="w-8 h-full text-xs font-bold font-mono text-center text-slate-900 dark:text-white bg-transparent border-x border-y-0 border-slate-300 dark:border-slate-600 focus:outline-none no-spinner px-0.5 tabular-nums">
                        <button type="button" onclick="updateQty(${index}, ${item.qty + 1})" class="w-5 h-full flex items-center justify-center bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition-colors cursor-pointer select-none" title="Tambah Qty">+</button>
                    </div>

                    <span class="font-mono font-bold text-xs text-slate-800 dark:text-slate-100 w-16 text-right tabular-nums">
                        ${formatRupiahJS(item.subtotal)}
                    </span>

                    <button type="button" onclick="hapusItem(${index})" class="btn btn-soft-danger p-1 text-xs rounded transition-colors" title="Hapus item">
                        <i class="bi bi-trash text-xs"></i>
                    </button>
                </div>
            </div>
        `;
        list.append(rowHtml);
    });

    currentTotalBelanja = total;
    $('#cartTotal').text(formatRupiahJS(total));
    $('#hiddenItems').val(JSON.stringify(cart));
    $('#cartCountBadge').text(totalQty + ' Item');
    hitungKembalian();
}

// Update Qty Item di Keranjang
function updateQty(index, val) {
    var qty = parseInt(val) || 1;
    var idBarang = cart[index].id;

    if (qty < 1) {
        hapusItem(index);
        return;
    }
    cart[index].qty = qty;
    cart[index].jumlah = qty;
    cart[index].subtotal = qty * cart[index].harga;

    renderCart();
    updateCardBadge(idBarang);
}

// Hapus Satu Item dari Keranjang
function hapusItem(index) {
    var idBarang = cart[index].id;
    cart.splice(index, 1);
    renderCart();
    updateCardBadge(idBarang);
}

// Kosongkan Seluruh Item Keranjang
function kosongkanKeranjang() {
    if (cart.length === 0) return;

    Swal.fire({
        title: 'Kosongkan Keranjang?',
        text: 'Seluruh item belanja di keranjang akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Kosongkan',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'hyperui-swal',
            title: 'hyperui-swal-title',
            htmlContainer: 'hyperui-swal-text'
        }
    }).then(function(res) {
        if (res.isConfirmed) {
            cart = [];
            $('#inputBayar').val('');
            renderCart();
        }
    });
}

// Set Nominal Cepat untuk Pembayaran
function setNominalBayar(nominal) {
    if (nominal === 'pas') {
        $('#inputBayar').val(currentTotalBelanja);
    } else {
        $('#inputBayar').val(nominal);
    }
    hitungKembalian();
}

// Hitung Nilai Kembalian Transaksi
function hitungKembalian() {
    var bayar = parseFloat($('#inputBayar').val()) || 0;
    var kembalian = bayar - currentTotalBelanja;
    var label = $('#labelKembalian');

    if (bayar === 0 || currentTotalBelanja === 0) {
        label.text('Rp 0').removeClass('text-rose-500 text-emerald-600 dark:text-emerald-400').addClass('text-slate-500 dark:text-slate-400');
    } else if (kembalian < 0) {
        label.text('Kurang ' + formatRupiahJS(Math.abs(kembalian))).removeClass('text-emerald-600 dark:text-emerald-400 text-slate-500').addClass('text-rose-500 font-bold');
    } else {
        label.text(formatRupiahJS(kembalian)).removeClass('text-rose-500 text-slate-500').addClass('text-emerald-600 dark:text-emerald-400 font-bold');
    }
}

// Eksekusi Simpan Transaksi Kasir ke Server
function prosesSimpanTransaksi() {
    var csrf = decrypter($("#csrf_token").val());
    $("#csrf_token_form").val(csrf);
    var btn = $('#btnSimpan');
    var oldHtml = btn.html();
    btn.html('<i class="bi bi-arrow-repeat animate-spin text-base"></i> Menyimpan...').attr('disabled', 'disabled');

    $.ajax({
        type: 'POST',
        url: '<?= getURL('kasir/simpan') ?>',
        data: $('#formTransaksi').serialize(),
        dataType: 'json',
        success: function(response) {
            btn.html(oldHtml);
            if (response.csrfToken) {
                $("#csrf_token").val(encrypter(response.csrfToken));
            }
            $("#csrf_token_form").val('');

            if (response.sukses == 1 || response.success) {
                var bayar = parseFloat($('#inputBayar').val()) || currentTotalBelanja;
                var kembali = bayar - currentTotalBelanja;

                cart = [];
                $('#inputBayar').val('');
                renderCart();

                Swal.fire({
                    title: 'Transaksi Berhasil!',
                    html: `
                        <div class="space-y-2 text-left p-2">
                            <div class="flex justify-between text-xs text-slate-600 dark:text-slate-300">
                                <span>Total Belanja:</span>
                                <strong class="font-mono text-sky-600 dark:text-cyan-400">${formatRupiahJS(response.data ? response.data.total_bayar : currentTotalBelanja)}</strong>
                            </div>
                            <div class="flex justify-between text-xs text-slate-600 dark:text-slate-300">
                                <span>Uang Tunai:</span>
                                <strong class="font-mono">${formatRupiahJS(bayar)}</strong>
                            </div>
                            <div class="flex justify-between text-xs text-slate-600 dark:text-slate-300 border-t border-slate-200 dark:border-slate-700 pt-1.5 font-bold">
                                <span>Kembalian:</span>
                                <strong class="font-mono text-emerald-600 dark:text-emerald-400">${formatRupiahJS(kembali >= 0 ? kembali : 0)}</strong>
                            </div>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonColor: '#0284c7',
                    confirmButtonText: 'Selesai & Transaksi Baru',
                    customClass: {
                        popup: 'hyperui-swal',
                        title: 'hyperui-swal-title',
                        htmlContainer: 'hyperui-swal-text'
                    }
                });
            } else {
                btn.removeAttr('disabled');
                showError(response.pesan || response.msg || 'Gagal menyimpan transaksi.');
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            btn.html(oldHtml).removeAttr('disabled');
            showError(thrownError || 'Terjadi kesalahan koneksi ke server.');
        }
    });
}
</script>
