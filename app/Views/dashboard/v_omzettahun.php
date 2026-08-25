<?= $this->include('template/v_header') ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-slate-800 px-4 py-2.5 sm:py-3 rounded-[14px] border border-slate-200/80 dark:border-slate-700 shadow-xs">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-950/50 border border-sky-200 dark:border-sky-800/60 flex items-center justify-center text-sky-600 dark:text-cyan-400 text-base shadow-xs shrink-0">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div>
                <h1 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">Laporan Omzet & Klasemen Kategori</h1>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-tight">Analisis performa penjualan tahunan & pergerakan kategori terlaris</p>
            </div>
        </div>
        <div class="flex items-center gap-2 self-end sm:self-center">
            <div class="w-[115px]">
                <select id="selectTahun" class="form-control" style="width: 100%;">
                    <?php foreach ($years as $yr): ?>
                        <option value="<?= $yr ?>" <?= $yr == $defaultYear ? 'selected' : '' ?>>
                            <?= $yr ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="button" id="btnRefreshDashboard" class="btn btn-soft-secondary btn-sm flex items-center justify-center w-8 h-8 p-0 text-xs shadow-xs" title="Reset ke tahun sekarang & refresh data">
                <i class="bi bi-arrow-clockwise text-sm"></i>
            </button>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-700 border-t-4 border-t-sky-500 rounded-[14px] bg-gradient-to-b from-sky-50/60 via-white to-white dark:from-sky-950/20 dark:via-slate-800 dark:to-slate-800 flex flex-col justify-between relative overflow-hidden transition-all">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">TOTAL OMZET</span>
                <span class="w-7 h-7 rounded-lg bg-sky-100/80 dark:bg-sky-950 text-sky-600 dark:text-cyan-400 flex items-center justify-center text-xs border border-sky-200 dark:border-sky-800">
                    <i class="bi bi-cash-stack"></i>
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white font-mono tracking-tight my-0.5" id="kpiOmzet">Rp 0</div>
            <span class="text-[10px] text-slate-500 dark:text-slate-400" id="kpiOmzetSub">Akumulasi penjualan tahun <span class="lblTahunAktif"><?= $defaultYear ?></span></span>
        </div>
        <div class="card p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-700 border-t-4 border-t-amber-500 rounded-[14px] bg-gradient-to-b from-amber-50/60 via-white to-white dark:from-amber-950/20 dark:via-slate-800 dark:to-slate-800 flex flex-col justify-between relative overflow-hidden transition-all">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">TOTAL LABA / MARGIN</span>
                <span class="w-7 h-7 rounded-lg bg-amber-100/80 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs border border-amber-200 dark:border-amber-800">
                    <i class="bi bi-piggy-bank"></i>
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-extrabold text-amber-600 dark:text-amber-400 font-mono tracking-tight my-0.5" id="kpiMargin">Rp 0</div>
            <span class="text-[10px] text-slate-500 dark:text-slate-400">Total keuntungan bersih</span>
        </div>
        <div class="card p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-700 border-t-4 border-t-emerald-500 rounded-[14px] bg-gradient-to-b from-emerald-50/60 via-white to-white dark:from-emerald-950/20 dark:via-slate-800 dark:to-slate-800 flex flex-col justify-between relative overflow-hidden transition-all">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">TOTAL TRANSAKSI</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-100/80 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs border border-emerald-200 dark:border-emerald-800">
                    <i class="bi bi-receipt"></i>
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 font-mono tracking-tight my-0.5" id="kpiTransaksi">0</div>
            <span class="text-[10px] text-slate-500 dark:text-slate-400">Total nota / struk berhasil</span>
        </div>
        <div class="card p-3.5 sm:p-4 shadow-xs border border-slate-200/80 dark:border-slate-700 border-t-4 border-t-purple-500 rounded-[14px] bg-gradient-to-b from-purple-50/60 via-white to-white dark:from-purple-950/20 dark:via-slate-800 dark:to-slate-800 flex flex-col justify-between relative overflow-hidden transition-all">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">KATEGORI TERATAS</span>
                <span class="w-7 h-7 rounded-lg bg-purple-100/80 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs border border-purple-200 dark:border-purple-800">
                    <i class="bi bi-trophy"></i>
                </span>
            </div>
            <div class="text-lg sm:text-xl font-extrabold text-purple-600 dark:text-purple-400 truncate my-0.5" id="kpiTopKategori">-</div>
            <span class="text-[10px] text-slate-500 dark:text-slate-400" id="kpiTopKategoriSub">Kontributor omzet tertinggi</span>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-7">
            <div class="card shadow-xs border border-slate-200/80 dark:border-slate-700 rounded-[16px] overflow-hidden bg-white dark:bg-slate-800 flex flex-col h-full">
                <div class="bg-[#0284c7] text-white flex flex-col sm:flex-row sm:items-center justify-between px-4 py-3 gap-2 font-bold text-xs sm:text-sm">
                    <span class="flex items-center gap-2">
                        <i class="bi bi-bar-chart-line-fill text-base"></i> Grafik Omzet Bulanan
                    </span>
                    <div class="flex items-center gap-3 text-[11px] font-semibold">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-white inline-block"></span>
                            <span id="lblLegendaTahunCurrent"><?= $defaultYear ?> (Bar)</span>
                        </span>
                        <span class="flex items-center gap-1.5" id="wrapLegendaTahunPrev" style="display: none;">
                            <span class="w-3 h-0.5 bg-slate-300 inline-block border-t border-dashed"></span>
                            <span id="lblLegendaTahunPrev"><?= $defaultYear - 1 ?> (Garis)</span>
                        </span>
                    </div>
                </div>
                <div class="p-4 sm:p-5 flex-1 flex flex-col justify-center">
                    <div id="chartOmzetBulanan" class="w-full min-h-[320px]"></div>
                </div>
            </div>
        </div>
        <div class="lg:col-span-5">
            <div class="card shadow-xs border border-slate-200/80 dark:border-slate-700 rounded-[16px] overflow-hidden bg-white dark:bg-slate-800 flex flex-col h-full">
                <div class="bg-[#0284c7] text-white flex items-center justify-between px-4 py-3 font-bold text-xs sm:text-sm">
                    <span class="flex items-center gap-2">
                        <i class="bi bi-trophy-fill text-base text-amber-300"></i> Klasemen Top 5 Kategori
                    </span>
                    <span class="badge badge-soft-light badge-xs text-[10px] font-bold inline-flex items-center gap-1.5">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-300"></span>
                        </span>
                        Live Standings
                    </span>
                </div>
                <div class="p-4 sm:p-5 flex-1">
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mb-3 flex items-center justify-between">
                        <span>Peringkat kategori berdasarkan total omzet tahun <strong class="lblTahunAktif"><?= $defaultYear ?></strong></span>
                        <span class="text-[10px] font-mono text-slate-400">Status Terbaru</span>
                    </div>
                    <div id="wrapKlasemenKategori" class="space-y-3">
                        <div class="py-8 text-center text-slate-400 text-xs">
                            <i class="bi bi-arrow-repeat animate-spin text-lg inline-block mb-1"></i>
                            <p>Memuat data klasemen...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('template/v_footer') ?>
<script>
var chartOmzet = null;
function loadDashboardData() {
    var tahun = $('#selectTahun').val();
    $('.lblTahunAktif').text(tahun);
    var csrf = decrypter($("#csrf_token").val());
    $("#csrf_token_form").val(csrf);
    $('#btnRefreshDashboard i').addClass('animate-spin');
    $.ajax({
        type: 'post',
        url: '<?= getURL('omzetboard/getdata') ?>',
        data: {
            '<?= csrf_token() ?>': csrf,
            'tahun': tahun
        },
        dataType: 'json',
        success: function(response) {
            $('#btnRefreshDashboard i').removeClass('animate-spin');
            if (response.csrfToken) {
                $("#csrf_token").val(encrypter(response.csrfToken));
            }
            $("#csrf_token_form").val('');
            if (response.sukses == '1') {
                renderKpi(response.kpi, response.tahun);
                renderChart(response.chart);
                renderKlasemen(response.klasemen);
            }
        },
        error: function() {
            $('#btnRefreshDashboard i').removeClass('animate-spin');
        }
    });
}

function renderKpi(kpi, tahun) {
    $('#kpiOmzet').text(formatRupiah(kpi.total_omzet || 0));
    $('#kpiMargin').text(formatRupiah(kpi.total_margin || 0));
    $('#kpiTransaksi').text(new Intl.NumberFormat('id-ID').format(kpi.total_transaksi || 0) + ' Struk');
    if (kpi.top_kategori_nama && kpi.top_kategori_nama !== '-') {
        $('#kpiTopKategori').text(kpi.top_kategori_nama);
        $('#kpiTopKategoriSub').html(formatRupiah(kpi.top_kategori_omzet) + ' <span class="font-bold font-mono text-purple-600 dark:text-purple-400">(' + kpi.top_kategori_persen + '%)</span>');
    } else {
        $('#kpiTopKategori').text('-');
        $('#kpiTopKategoriSub').text('Belum ada transaksi');
    }
}

function renderChart(chartData) {
    var isDark = $('html').hasClass('dark');
    var series = [];
    $('#lblLegendaTahunCurrent').text(chartData.tahun_current + ' (Bar)');
    series.push({
        name: 'Omzet ' + chartData.tahun_current,
        type: 'column',
        data: chartData.series_current
    });
    if (chartData.has_prev_data) {
        $('#wrapLegendaTahunPrev').show();
        $('#lblLegendaTahunPrev').text(chartData.tahun_prev + ' (Garis)');
        series.push({
            name: 'Omzet ' + chartData.tahun_prev,
            type: 'line',
            data: chartData.series_prev
        });
    } else {
        $('#wrapLegendaTahunPrev').hide();
    }

    var options = {
        series: series,
        chart: {
            height: 330,
            type: 'line',
            toolbar: { show: false },
            fontFamily: 'inherit',
            background: 'transparent'
        },
        colors: ['#0284c7', '#94a3b8'],
        stroke: {
            width: chartData.has_prev_data ? [0, 3] : [0],
            curve: 'smooth',
            dashArray: chartData.has_prev_data ? [0, 5] : [0]
        },
        plotOptions: {
            bar: {
                columnWidth: '45%',
                borderRadius: 6
            }
        },
        fill: {
            opacity: chartData.has_prev_data ? [0.9, 1] : [0.9]
        },
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        markers: {
            size: chartData.has_prev_data ? [0, 4] : [0]
        },
        xaxis: {
            labels: {
                style: {
                    colors: isDark ? '#94a3b8' : '#64748b',
                    fontSize: '11px',
                    fontWeight: 600
                }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: {
                    colors: isDark ? '#94a3b8' : '#64748b',
                    fontSize: '11px',
                    fontWeight: 600
                },
                formatter: function (val) {
                    if (val >= 1000000) {
                        return 'Rp ' + (val / 1000000).toFixed(1) + ' jt';
                    } else if (val >= 1000) {
                        return 'Rp ' + (val / 1000).toFixed(0) + ' rb';
                    }
                    return 'Rp ' + val;
                }
            }
        },
        grid: {
            borderColor: isDark ? '#334155' : '#f1f5f9',
            strokeDashArray: 4
        },
        legend: { show: false },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            shared: true,
            intersect: false,
            custom: function(opts) {
                var dataPointIndex = opts.dataPointIndex;
                var monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                var bulanName = monthNames[dataPointIndex] || '';
                var omzetCurr = (chartData.series_current && chartData.series_current[dataPointIndex]) ? chartData.series_current[dataPointIndex] : 0;
                var marginCurr = (chartData.series_margin_current && chartData.series_margin_current[dataPointIndex]) ? chartData.series_margin_current[dataPointIndex] : 0;
                var persenMarginCurr = omzetCurr > 0 ? ((marginCurr / omzetCurr) * 100).toFixed(1) : 0;
                var html = '<div class="p-3 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl text-xs space-y-2 min-w-[210px]">' +
                    '<div class="font-extrabold text-xs text-slate-900 dark:text-white pb-1.5 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">' +
                        '<span><i class="bi bi-calendar3 text-sky-500 me-1"></i>' + bulanName + ' ' + chartData.tahun_current + '</span>' +
                    '</div>' +
                    '<div class="space-y-1">' +
                        '<div class="flex items-center justify-between gap-3 text-[11px]">' +
                            '<span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-sky-500 inline-block"></span>Omzet:</span>' +
                            '<span class="font-bold font-mono text-sky-600 dark:text-cyan-400">' + formatRupiah(omzetCurr) + '</span>' +
                        '</div>' +
                        '<div class="flex items-center justify-between gap-3 text-[11px]">' +
                            '<span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>Margin Laba:</span>' +
                            '<span class="font-bold font-mono text-amber-600 dark:text-amber-400">' + formatRupiah(marginCurr) + ' <span class="text-[10px] text-slate-400 font-normal">(' + persenMarginCurr + '%)</span></span>' +
                        '</div>' +
                    '</div>';

                if (chartData.has_prev_data) {
                    var omzetPrev = (chartData.series_prev && chartData.series_prev[dataPointIndex]) ? chartData.series_prev[dataPointIndex] : 0;
                    var marginPrev = (chartData.series_margin_prev && chartData.series_margin_prev[dataPointIndex]) ? chartData.series_margin_prev[dataPointIndex] : 0;
                    html += '<div class="pt-1.5 mt-1.5 border-t border-slate-100 dark:border-slate-700 space-y-1">' +
                        '<div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">' + chartData.tahun_prev + ' (Tahun Lalu)</div>' +
                        '<div class="flex items-center justify-between gap-3 text-[11px]">' +
                            '<span class="text-slate-400 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-slate-400 inline-block"></span>Omzet:</span>' +
                            '<span class="font-semibold font-mono text-slate-600 dark:text-slate-300">' + formatRupiah(omzetPrev) + '</span>' +
                        '</div>' +
                        '<div class="flex items-center justify-between gap-3 text-[11px]">' +
                            '<span class="text-slate-400 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-slate-400 inline-block"></span>Margin:</span>' +
                            '<span class="font-semibold font-mono text-slate-600 dark:text-slate-300">' + formatRupiah(marginPrev) + '</span>' +
                        '</div>' +
                    '</div>';
                }
                html += '</div>';
                return html;
            }
        }
    };
    if (chartOmzet) {
        chartOmzet.destroy();
    }
    if (typeof ApexCharts !== 'undefined') {
        chartOmzet = new ApexCharts(document.querySelector("#chartOmzetBulanan"), options);
        chartOmzet.render();
    }
}

function renderKlasemen(klasemen) {
    var html = '';
    if (!klasemen || klasemen.length === 0) {
        html = '<div class="py-8 text-center text-slate-400 text-xs"><i class="bi bi-inbox text-2xl inline-block mb-1 opacity-50"></i><p>Belum ada data transaksi di tahun ini.</p></div>';
        $('#wrapKlasemenKategori').html(html);
        return;
    }
    klasemen.forEach(function(item) {
        var badgeRank = '<span class="w-6 h-6 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold font-mono text-xs flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-xs">' + item.rank + '</span>';
        var badgeStatus = '';
        if (item.status === 'up') {
            badgeStatus = '<span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800" title="Naik peringkat setelah transaksi terbaru"><i class="bi bi-caret-up-fill text-[9px]"></i>' + (item.diff > 0 ? '+' + item.diff : 'UP') + '</span>';
        } else if (item.status === 'down') {
            badgeStatus = '<span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800" title="Turun peringkat tergeser"><i class="bi bi-caret-down-fill text-[9px]"></i>' + (item.diff > 0 ? '-' + item.diff : 'DOWN') + '</span>';
        } else {
            badgeStatus = '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700" title="Peringkat stabil"><i class="bi bi-dash"></i></span>';
        }
        html += '<div class="p-3 rounded-xl bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-700/60 hover:border-sky-500/40 transition-all flex flex-col gap-1.5">' +
                    '<div class="flex items-center justify-between">' +
                        '<div class="flex items-center gap-2">' +
                            badgeRank +
                            badgeStatus +
                            '<span class="font-bold text-xs text-slate-900 dark:text-white truncate max-w-[140px] sm:max-w-[180px]">' + item.categoryname + '</span>' +
                        '</div>' +
                        '<div class="text-right">' +
                            '<div class="font-mono font-extrabold text-xs text-sky-600 dark:text-cyan-400">' + formatRupiah(item.total_omzet) + '</div>' +
                            '<div class="text-[10px] text-slate-400 font-mono">' + item.total_qty + ' pcs terjual</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">' +
                        '<div class="bg-gradient-to-r from-sky-500 to-cyan-400 h-full rounded-full" style="width: ' + item.persen_bar + '%;"></div>' +
                    '</div>' +
                '</div>';
    });
    $('#wrapKlasemenKategori').html(html);
}

$(document).ready(function() {
    $('#selectTahun').select2({
        width: '100%',
        minimumResultsForSearch: -1
    });
    loadDashboardData();
    $('#selectTahun').on('change', function() {
        loadDashboardData();
    });

    $('#btnRefreshDashboard').on('click', function() {
        var currentYear = '<?= $defaultYear ?>';
        if ($('#selectTahun').val() == currentYear) {
            loadDashboardData();
        } else {
            $('#selectTahun').val(currentYear).trigger('change');
        }
    });

    $('#theme-toggle').on('click', function() {
        setTimeout(function() {
            if (chartOmzet) {
                loadDashboardData();
            }
        }, 150);
    });
});
</script>
