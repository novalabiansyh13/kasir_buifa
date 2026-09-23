<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>

<div class="space-y-6">
    <!-- Header Bar (Style Identik Dashboard Omzet) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-800 px-4 py-2.5 sm:py-3 rounded-[14px] border border-slate-200/80 dark:border-slate-700 shadow-xs">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-950/50 border border-sky-200 dark:border-sky-800/60 flex items-center justify-center text-sky-600 dark:text-cyan-400 text-base shadow-xs shrink-0">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <h1 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">Riwayat &amp; Rekap Transaksi</h1>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-tight">Daftar rekaman nota penjualan toko dan kalkulasi margin keuntungan</p>
            </div>
        </div>
        <div class="flex items-center gap-2 self-end sm:self-center">
            <a href="<?= getURL('kasir') ?>" class="btn btn-soft-primary btn-sm flex items-center gap-1.5 text-xs font-bold no-underline shadow-xs">
                <i class="bi bi-receipt-cutoff"></i> Buka Kasir POS
            </a>
        </div>
    </div>

    <!-- Metric Summary Cards (Style Identik Dashboard Omzet) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- Card 1: Omzet (Sky) -->
        <div class="card p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-700 border-t-4 border-t-sky-500 rounded-[14px] bg-gradient-to-b from-sky-50/60 via-white to-white dark:from-sky-950/20 dark:via-slate-800 dark:to-slate-800 flex flex-col justify-between relative overflow-hidden transition-all">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">TOTAL OMZET</span>
                <span class="w-7 h-7 rounded-lg bg-sky-100/80 dark:bg-sky-950 text-sky-600 dark:text-cyan-400 flex items-center justify-center text-xs border border-sky-200 dark:border-sky-800">
                    <i class="bi bi-cash-stack"></i>
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white font-mono tracking-tight my-0.5 truncate" id="summaryPenjualan">Rp 0</div>
            <span class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Total kotor uang masuk</span>
        </div>

        <!-- Card 2: Laba/Margin (Amber) -->
        <div class="card p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-700 border-t-4 border-t-amber-500 rounded-[14px] bg-gradient-to-b from-amber-50/60 via-white to-white dark:from-amber-950/20 dark:via-slate-800 dark:to-slate-800 flex flex-col justify-between relative overflow-hidden transition-all">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">TOTAL LABA / MARGIN</span>
                <span class="w-7 h-7 rounded-lg bg-amber-100/80 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs border border-amber-200 dark:border-amber-800">
                    <i class="bi bi-piggy-bank"></i>
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-extrabold text-amber-600 dark:text-amber-400 font-mono tracking-tight my-0.5 truncate" id="summaryMargin">Rp 0</div>
            <span class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Total keuntungan bersih</span>
        </div>

        <!-- Card 3: Transaksi (Emerald) -->
        <div class="card p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-700 border-t-4 border-t-emerald-500 rounded-[14px] bg-gradient-to-b from-emerald-50/60 via-white to-white dark:from-emerald-950/20 dark:via-slate-800 dark:to-slate-800 flex flex-col justify-between relative overflow-hidden transition-all">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">TOTAL TRANSAKSI</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-100/80 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs border border-emerald-200 dark:border-emerald-800">
                    <i class="bi bi-receipt"></i>
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 font-mono tracking-tight my-0.5 truncate" id="summaryTransaksi">0</div>
            <span class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Total nota / struk berhasil</span>
        </div>

        <!-- Card 4: Top Produk (Purple) -->
        <div class="card p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-700 border-t-4 border-t-purple-500 rounded-[14px] bg-gradient-to-b from-purple-50/60 via-white to-white dark:from-purple-950/20 dark:via-slate-800 dark:to-slate-800 flex flex-col justify-between relative overflow-hidden transition-all">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">PRODUK TERLARIS</span>
                <span class="w-7 h-7 rounded-lg bg-purple-100/80 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs border border-purple-200 dark:border-purple-800">
                    <i class="bi bi-trophy"></i>
                </span>
            </div>
            <div class="my-0.5 flex items-baseline justify-between gap-1.5 min-w-0">
                <span class="text-lg sm:text-xl font-extrabold text-purple-600 dark:text-purple-400 truncate" id="top1Name">-</span>
                <span class="text-xs font-mono font-bold text-purple-500 shrink-0" id="top1Qty">0 terjual</span>
            </div>
            <span class="text-[10px] text-slate-500 dark:text-slate-400 truncate block" id="top23Keterangan" title="Peringkat 2 & 3">Belum ada transaksi</span>
        </div>
    </div>

    <!-- Card Data Riwayat Transaksi -->
    <div class="card shadow-sm border border-slate-200/80 dark:border-slate-700 rounded-[14px] overflow-hidden bg-white dark:bg-slate-800">
        <div class="bg-[#0284c7] text-white flex flex-col sm:flex-row sm:items-center justify-between px-4 py-3 gap-2.5 font-bold text-xs sm:text-sm">
            <span class="flex items-center gap-2">
                <i class="bi bi-journal-text text-base"></i> Daftar Rekap Transaksi
            </span>
            <div class="flex items-center gap-2">
                <div class="relative flex items-center">
                    <i class="bi bi-calendar3 absolute left-2.5 text-slate-400 text-xs pointer-events-none z-10"></i>
                    <input type="text" id="filter-daterange" class="form-control input-daterange font-semibold cursor-pointer w-[195px] sm:w-[205px] bg-white/95 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-xs text-slate-800 dark:text-slate-200" readonly title="Filter Rentang Tanggal" />
                </div>
                <button type="button" id="btnRefresh" class="btn btn-soft-secondary btn-sm flex items-center gap-1 text-xs" title="Reset filter ke hari ini &amp; Refresh">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>

        <div class="p-4 overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-800 dark:text-slate-200 border-collapse table-rekap" id="tabelRiwayat" style="width: 100%;">
                <thead class="bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 text-xs font-bold border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="py-3 px-3 text-center w-12">No</th>
                        <th class="py-3 px-3 text-center w-28">Tanggal</th>
                        <th class="py-3 px-3 text-center w-20">Jam</th>
                        <th class="py-3 px-3 text-start">Detail Barang</th>
                        <th class="py-3 px-3 text-center w-32">Total Bayar</th>
                        <th class="py-3 px-3 text-center w-28">Margin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-900 dark:text-slate-100"></tbody>
                <tfoot class="bg-slate-50 dark:bg-slate-900 font-bold border-t border-slate-200 dark:border-slate-700">
                    <tr>
                        <td colspan="4" class="py-3 px-3 text-start text-slate-700 dark:text-slate-300"><i class="bi bi-calculator me-1"></i>TOTAL:</td>
                        <td class="py-3 px-3 text-center text-sky-600 dark:text-cyan-400 font-bold text-sm font-mono" id="footerPenjualan">Rp 0</td>
                        <td class="py-3 px-3 text-center text-amber-600 dark:text-amber-400 font-bold text-sm font-mono" id="footerMargin">Rp 0</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?= $this->include('template/v_footer') ?>

<script>
var tbl_riwayat = null;

$(document).ready(function() {
    $('#filter-daterange').daterangepicker({
        startDate: moment(),
        endDate: moment(),
        linkedCalendars: false,
        showCustomRangeLabel: false,
        alwaysShowCalendars: true,
        opens: 'left',
        locale: {
            format: 'DD/MM/YYYY',
            separator: ' - ',
            applyLabel: 'Terapkan',
            cancelLabel: 'Batal',
            daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
        }
    });

    $('#filter-daterange').on('apply.daterangepicker', function(ev, picker) {
        if (tbl_riwayat) tbl_riwayat.ajax.reload();
    });

    tbl_riwayat = $('#tabelRiwayat').DataTable({
        serverSide: true,
        destroy: true,
        autoWidth: false,
        ajax: {
            url: '<?= getURL('riwayat/table') ?>',
            type: 'post',
            dataType: 'json',
            data: function(param) {
                param["<?= csrf_token() ?>"] = decrypter($("#csrf_token").val());
                var drp = $('#filter-daterange').data('daterangepicker');
                if (drp) {
                    param.start_date = drp.startDate.format('YYYY-MM-DD');
                    param.end_date = drp.endDate.format('YYYY-MM-DD');
                }
                return param;
            },
            dataSrc: function(json) {
                if (json.csrfToken) {
                    $("#csrf_token").val(encrypter(json.csrfToken));
                }
                var totalPenjualan = 0;
                var totalMargin = 0;
                var totalTransaksi = 0;
                var topProducts = [];
                if (json.tambahan) {
                    totalPenjualan = json.tambahan.total_penjualan || 0;
                    totalMargin = json.tambahan.total_margin || 0;
                    totalTransaksi = json.tambahan.total_transaksi || 0;
                    topProducts = json.tambahan.top_products || [];
                }
                $('#summaryPenjualan').text(formatRupiah(totalPenjualan));
                $('#summaryMargin').text(formatRupiah(totalMargin));
                $('#summaryTransaksi').text(totalTransaksi.toLocaleString('id-ID'));
                $('#footerPenjualan').text(formatRupiah(totalPenjualan));
                $('#footerMargin').text(formatRupiah(totalMargin));

                if (topProducts && topProducts.length > 0) {
                    var top1 = topProducts[0];
                    $('#top1Name').text(top1.nama_barang).attr('title', top1.nama_barang);
                    $('#top1Qty').text(top1.total_qty + ' terjual');

                    var others = [];
                    for (var i = 1; i < topProducts.length; i++) {
                        others.push('#' + (i + 1) + ' ' + topProducts[i].nama_barang + ' (' + topProducts[i].total_qty + ')');
                    }
                    if (others.length > 0) {
                        $('#top23Keterangan').html(others.join(' &bull; ')).attr('title', others.join(' • '));
                    } else {
                        $('#top23Keterangan').text('Hanya 1 produk terjual').attr('title', '');
                    }
                } else {
                    $('#top1Name').text('-').attr('title', '');
                    $('#top1Qty').text('0 terjual');
                    $('#top23Keterangan').text('Belum ada transaksi').attr('title', '');
                }
                return json.data || [];
            }
        },
        columns: [
            { data: 0, className: 'text-center' },
            { data: 1, className: 'text-center' },
            { data: 2, className: 'text-center' },
            { data: 3, className: 'text-start' },
            { data: 4, className: 'text-center font-mono' },
            { data: 5, className: 'text-center font-mono' }
        ]
    });

    $('#btnRefresh').on('click', function() {
        var drp = $('#filter-daterange').data('daterangepicker');
        if (drp) {
            drp.setStartDate(moment());
            drp.setEndDate(moment());
        }
        if (tbl_riwayat) {
            tbl_riwayat.ajax.reload();
        }
    });
});
</script>
