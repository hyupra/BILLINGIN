@extends('mockup.dashboard.layout')

@section('title', 'Laporan — BILLINGIN')
@section('active', 'reports')
@section('page-title', 'Laporan')
@section('page-subtitle')
    Pendapatan dan tunggakan per periode.
    <span class="ml-1 inline-block px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400 text-xs font-semibold align-middle">data contoh</span>
@endsection
@section('page-actions')
    <button type="button" onclick="mockupToast('Ekspor CSV belum tersedia di mockup ini')" class="h-11 px-4 inline-flex items-center gap-2 rounded-lg border border-white/15 font-semibold hover:bg-white/5">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
        Ekspor CSV
    </button>
    <button type="button" onclick="mockupToast('Ekspor Excel belum tersedia di mockup ini')" class="h-11 px-4 inline-flex items-center gap-2 rounded-lg border border-white/15 font-semibold hover:bg-white/5">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
        Ekspor Excel
    </button>
@endsection

@section('content')

    <div class="flex items-center gap-2 mb-6">
        <button type="button" onclick="setActiveFilterPill(this); setReportPeriod('okt')" class="px-4 py-2 rounded-full text-sm font-semibold bg-indigo-600 text-white">Okt 2026</button>
        <button type="button" onclick="setActiveFilterPill(this); setReportPeriod('sep')" class="px-4 py-2 rounded-full text-sm font-semibold border border-white/15 text-gray-300 hover:bg-white/5">Sep 2026</button>
        <button type="button" onclick="setActiveFilterPill(this); setReportPeriod('agu')" class="px-4 py-2 rounded-full text-sm font-semibold border border-white/15 text-gray-300 hover:bg-white/5">Agu 2026</button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-5">
            <p class="text-gray-400 text-sm mb-2">Pendapatan</p>
            <p class="text-2xl font-bold" id="kpi-pendapatan">Rp 14.260.000</p>
            <p class="text-gray-500 text-sm mt-1" id="kpi-pendapatan-sub">dari 116 pembayaran</p>
        </div>
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-5">
            <p class="text-gray-400 text-sm mb-2">Tunggakan</p>
            <p class="text-2xl font-bold text-amber-400" id="kpi-tunggakan">Rp 4.190.000</p>
            <p class="text-gray-500 text-sm mt-1" id="kpi-tunggakan-sub">34 tagihan</p>
        </div>
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-5">
            <p class="text-gray-400 text-sm mb-2">Tingkat tertagih</p>
            <p class="text-2xl font-bold text-emerald-400" id="kpi-tertagih">77%</p>
            <p class="text-gray-500 text-sm mt-1">terbayar dari total tagihan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6">
            <h2 class="font-bold mb-5">Pendapatan per metode bayar</h2>
            <div class="space-y-4">
                @foreach ([
                    ['label' => 'QRIS', 'pct' => 48, 'key' => 'qris'],
                    ['label' => 'Virtual Account', 'pct' => 27, 'key' => 'va'],
                    ['label' => 'Minimarket', 'pct' => 14, 'key' => 'minimarket'],
                    ['label' => 'E-wallet', 'pct' => 8, 'key' => 'ewallet'],
                    ['label' => 'Transfer manual', 'pct' => 3, 'key' => 'transfer'],
                ] as $row)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span>{{ $row['label'] }}</span>
                            <span class="font-semibold" id="chart-pct-{{ $row['key'] }}">{{ $row['pct'] }}%</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-white/10 overflow-hidden">
                            <div class="h-full bg-indigo-500" id="chart-bar-{{ $row['key'] }}" style="width: {{ $row['pct'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-xl border border-white/10 bg-[#0B0F24] overflow-hidden">
            <div class="px-6 py-4 border-b border-white/10">
                <h2 class="font-bold">Umur tunggakan</h2>
                <p class="text-gray-500 text-xs mt-1" id="aging-subtitle">Dari 12 tagihan yang lewat jatuh tempo.</p>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400">
                        <th class="px-6 py-3 font-medium">Keterlambatan</th>
                        <th class="px-3 py-3 font-medium">Tagihan</th>
                        <th class="px-6 py-3 font-medium text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach ([
                        ['label' => '1-7 hari', 'count' => 7, 'amount' => 'Rp 980.000', 'key' => 'd1_7'],
                        ['label' => '8-14 hari', 'count' => 3, 'amount' => 'Rp 700.000', 'key' => 'd8_14'],
                        ['label' => 'Lebih dari 14 hari', 'count' => 2, 'amount' => 'Rp 480.000', 'key' => 'd15'],
                    ] as $row)
                        <tr>
                            <td class="px-6 py-4">{{ $row['label'] }}</td>
                            <td class="px-3 py-4" id="aging-count-{{ $row['key'] }}">{{ $row['count'] }}</td>
                            <td class="px-6 py-4 text-right font-semibold" id="aging-amount-{{ $row['key'] }}">{{ $row['amount'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Per-period demo data for the KPI cards, payment-method chart and aging
    // table. Okt 2026 matches the numbers already baked into the markup;
    // Sep/Agu are invented-but-consistent mockup figures (tertagih% ~=
    // paid / (paid + unpaid) pembayaran, chart pct's sum to 100).
    const reportsData = {
        okt: {
            pendapatan: 'Rp 14.260.000', pendapatanSub: 'dari 116 pembayaran',
            tunggakan: 'Rp 4.190.000', tunggakanSub: '34 tagihan',
            tertagih: '77%',
            chart: { qris: 48, va: 27, minimarket: 14, ewallet: 8, transfer: 3 },
            aging: {
                subtitle: 'Dari 12 tagihan yang lewat jatuh tempo.',
                d1_7: { count: 7, amount: 'Rp 980.000' },
                d8_14: { count: 3, amount: 'Rp 700.000' },
                d15: { count: 2, amount: 'Rp 480.000' }
            }
        },
        sep: {
            pendapatan: 'Rp 13.120.000', pendapatanSub: 'dari 109 pembayaran',
            tunggakan: 'Rp 4.850.000', tunggakanSub: '39 tagihan',
            tertagih: '74%',
            chart: { qris: 45, va: 29, minimarket: 15, ewallet: 7, transfer: 4 },
            aging: {
                subtitle: 'Dari 14 tagihan yang lewat jatuh tempo.',
                d1_7: { count: 8, amount: 'Rp 1.120.000' },
                d8_14: { count: 4, amount: 'Rp 920.000' },
                d15: { count: 2, amount: 'Rp 560.000' }
            }
        },
        agu: {
            pendapatan: 'Rp 12.480.000', pendapatanSub: 'dari 103 pembayaran',
            tunggakan: 'Rp 5.220.000', tunggakanSub: '42 tagihan',
            tertagih: '71%',
            chart: { qris: 43, va: 30, minimarket: 16, ewallet: 7, transfer: 4 },
            aging: {
                subtitle: 'Dari 16 tagihan yang lewat jatuh tempo.',
                d1_7: { count: 9, amount: 'Rp 1.260.000' },
                d8_14: { count: 5, amount: 'Rp 1.150.000' },
                d15: { count: 2, amount: 'Rp 610.000' }
            }
        }
    };

    function setReportPeriod(period) {
        const d = reportsData[period];
        if (!d) return;

        document.getElementById('kpi-pendapatan').textContent = d.pendapatan;
        document.getElementById('kpi-pendapatan-sub').textContent = d.pendapatanSub;
        document.getElementById('kpi-tunggakan').textContent = d.tunggakan;
        document.getElementById('kpi-tunggakan-sub').textContent = d.tunggakanSub;
        document.getElementById('kpi-tertagih').textContent = d.tertagih;

        Object.keys(d.chart).forEach(function (key) {
            const pct = d.chart[key];
            const pctEl = document.getElementById('chart-pct-' + key);
            const barEl = document.getElementById('chart-bar-' + key);
            if (pctEl) pctEl.textContent = pct + '%';
            if (barEl) barEl.style.width = pct + '%';
        });

        document.getElementById('aging-subtitle').textContent = d.aging.subtitle;
        ['d1_7', 'd8_14', 'd15'].forEach(function (key) {
            document.getElementById('aging-count-' + key).textContent = d.aging[key].count;
            document.getElementById('aging-amount-' + key).textContent = d.aging[key].amount;
        });
    }
</script>
@endsection
