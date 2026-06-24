<x-layouts.app>
    @php
        $r = $ringkasan;
    @endphp

    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        {{-- HEADER --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <flux:heading size="xl" level="1">Halo, {{ auth()->user()->name }} 👋</flux:heading>
                <flux:subheading>
                    Ringkasan produksi pertanian Kabupaten Grobogan
                    @if ($tahunTerbaru)
                        &mdash; Tahun {{ $tahunTerbaru }}
                    @endif
                </flux:subheading>
            </div>

            <div class="flex flex-wrap gap-2">
                <flux:button href="{{ route('peta') }}" icon="map" wire:navigate>Buka Peta</flux:button>
                <flux:button href="{{ route('tabel') }}" icon="table-cells" variant="ghost" wire:navigate>
                    Lihat Tabel
                </flux:button>
            </div>
        </div>

        {{-- STAT CARDS --}}
        <div class="grid auto-rows-min gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <div class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Kecamatan Tercatat</div>
                <div class="mt-1 text-2xl font-semibold">{{ $jumlahKecamatan }}</div>
            </div>

            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <div class="text-xs font-medium text-neutral-500 dark:text-neutral-400">🌾 Produksi Padi ({{ $tahunTerbaru ?? '-' }})</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($r->total_padi ?? 0, 0, ',', '.') }} ton</div>
                <div class="text-xs text-neutral-500">Produktivitas rata-rata {{ number_format($r->rata_padi ?? 0, 2, ',', '.') }} ton/ha</div>
            </div>

            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <div class="text-xs font-medium text-neutral-500 dark:text-neutral-400">🌽 Produksi Jagung ({{ $tahunTerbaru ?? '-' }})</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($r->total_jagung ?? 0, 0, ',', '.') }} ton</div>
                <div class="text-xs text-neutral-500">Produktivitas rata-rata {{ number_format($r->rata_jagung ?? 0, 2, ',', '.') }} ton/ha</div>
            </div>

            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <div class="text-xs font-medium text-neutral-500 dark:text-neutral-400">🫘 Produksi Kedelai ({{ $tahunTerbaru ?? '-' }})</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($r->total_kedelai ?? 0, 0, ',', '.') }} ton</div>
                <div class="text-xs text-neutral-500">Produktivitas rata-rata {{ number_format($r->rata_kedelai ?? 0, 2, ',', '.') }} ton/ha</div>
            </div>
        </div>

        {{-- TREN + TOP KECAMATAN --}}
        <div class="grid gap-4 lg:grid-cols-3">

            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700 lg:col-span-2">
                <flux:heading size="sm">Tren Produksi per Tahun</flux:heading>
                <div class="mt-3 h-72">
                    <canvas id="trenChart"></canvas>
                </div>
            </div>

            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <flux:heading size="sm">Top 5 Kecamatan &mdash; Padi {{ $tahunTerbaru ?? '' }}</flux:heading>

                <ul class="mt-3 space-y-2">
                    @forelse ($topPadi as $row)
                        <li class="flex items-center justify-between text-sm">
                            <span>{{ $row->kecamatan }}</span>
                            <span class="font-medium">{{ number_format($row->padi_produksi ?? 0, 0, ',', '.') }} ton</span>
                        </li>
                    @empty
                        <li class="text-sm text-neutral-500">Belum ada data.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- DATA TERBARU + AKSI --}}
        <div class="grid gap-4 lg:grid-cols-3">

            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700 lg:col-span-2">
                <flux:heading size="sm">Data Terbaru Ditambahkan</flux:heading>

                <div class="mt-3 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-neutral-500">
                                <th class="py-1 pr-3">Kecamatan</th>
                                <th class="py-1 pr-3">Tahun</th>
                                <th class="py-1 pr-3">Padi (ton)</th>
                                <th class="py-1 pr-3">Jagung (ton)</th>
                                <th class="py-1 pr-3">Kedelai (ton)</th>
                                <th class="py-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dataTerbaru as $row)
                                <tr class="border-t border-neutral-100 dark:border-neutral-800">
                                    <td class="py-2 pr-3">{{ $row->kecamatan }}</td>
                                    <td class="py-2 pr-3">{{ $row->tahun }}</td>
                                    <td class="py-2 pr-3">{{ number_format($row->padi_produksi ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-2 pr-3">{{ number_format($row->jagung_produksi ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-2 pr-3">{{ number_format($row->kedelai_produksi ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-2 text-right">
                                        <flux:button
                                            href="{{ route('pertanian.edit', $row->kecamatan) }}"
                                            size="sm"
                                            variant="ghost"
                                        >
                                            Edit
                                        </flux:button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-neutral-500">
                                        Belum ada data pertanian. Tambahkan lewat halaman Peta.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <flux:heading size="sm">Aksi Cepat</flux:heading>

                <div class="mt-3 flex flex-col gap-2">
                    <flux:button href="{{ route('peta') }}" icon="map" wire:navigate class="w-full justify-start">
                        Tambah / Edit Data via Peta
                    </flux:button>
                    <flux:button href="{{ route('tabel') }}" icon="table-cells" variant="ghost" wire:navigate class="w-full justify-start">
                        Lihat Semua Data
                    </flux:button>
                    <flux:button href="{{ route('settings.profile') }}" icon="user" variant="ghost" wire:navigate class="w-full justify-start">
                        Pengaturan Akun
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        (function () {
            const ctx = document.getElementById('trenChart');
            if (!ctx) return;

            const labels = @json($trenTahunan->pluck('tahun'));

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Padi (ton)',
                            data: @json($trenTahunan->pluck('padi')),
                            borderColor: '#4CAF50',
                            backgroundColor: '#4CAF5022',
                            tension: 0.3,
                        },
                        {
                            label: 'Jagung (ton)',
                            data: @json($trenTahunan->pluck('jagung')),
                            borderColor: '#FFC107',
                            backgroundColor: '#FFC10722',
                            tension: 0.3,
                        },
                        {
                            label: 'Kedelai (ton)',
                            data: @json($trenTahunan->pluck('kedelai')),
                            borderColor: '#2196F3',
                            backgroundColor: '#2196F322',
                            tension: 0.3,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: { y: { beginAtZero: true } },
                },
            });
        })();
    </script>
</x-layouts.app>
