<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="space-y-4">
    <div class="card p-4 bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 rounded-[14px] shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="badge badge-soft-primary badge-xs">POS Inventory</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">Toko Bu Ifa</span>
            </div>
            <h2 class="font-bold text-sm sm:text-base text-slate-900 dark:text-white mb-0">Manajemen Katalog Produk & Stok Barang</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 mt-0.5">Kelola harga beli, harga jual, dan margin keuntungan secara terpusat.</p>
        </div>
        <?php if ($akses["COMPO_" . COMADD]) : ?>
            <button type="button" onclick="openModal('Tambah Produk Baru', '<?= getURL('barang/form') ?>', {}, 'max-w-xl')" class="btn btn-primary btn-sm flex items-center gap-1.5 shadow-xs font-bold">
                <i class="bi bi-plus-lg text-base"></i> Tambah Produk Baru
            </button>
        <?php endif; ?>
    </div>
    <div class="card shadow-sm border border-slate-200/80 dark:border-slate-700 rounded-[14px] overflow-hidden bg-white dark:bg-slate-800 p-4">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-800 dark:text-slate-200 border-collapse table-master" style="width: 100%;">
                <thead class="bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 text-xs font-bold border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="py-3 px-3 text-center" style="width: 50px;">No</th>
                        <th class="py-3 px-3 text-start">Nama Produk</th>
                        <th class="py-3 px-3 text-center">Kategori</th>
                        <th class="py-3 px-3 text-end">Harga Beli</th>
                        <th class="py-3 px-3 text-end">Harga Jual</th>
                        <th class="py-3 px-3 text-end">Margin Laba</th>
                        <th class="py-3 px-3 text-center" style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-900 dark:text-slate-100"></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->include('template/v_footer') ?>
