@props([
    'name',
    'value'      => '',
    'options'    => [], // [['value' => '...', 'label' => '...'], ...]
    'placeholder' => 'Cari pegawai...',
    'clearLabel' => null, // If set, shows a "clear" option row at top (e.g. '— Tidak Ada —')
    'class'      => '',
])

@php
    // Pisahkan opsi placeholder (value kosong) dari opsi data
    $dataOptions = array_filter($options, fn($o) => $o['value'] !== '');
    $dataOptions = array_values($dataOptions);
    $defaultLabel = collect($options)->firstWhere('value', $value)['label']
        ?? (collect($options)->first()['label'] ?? $placeholder);
@endphp

<div x-data="{
        open: false,
        query: '',
        value: '{{ $value }}',
        label: {{ Js::from($defaultLabel) }},
        all: {{ Js::from($dataOptions) }},
        get filtered() {
            if (!this.query) return this.all;
            const q = this.query.toLowerCase();
            return this.all.filter(o => o.label.toLowerCase().includes(q));
        },
        select(opt) {
            this.value = opt.value;
            this.label = opt.label;
            this.query = '';
            this.open  = false;
            this.$nextTick(() => {
                const sel = this.$refs.hiddenSelect;
                sel.value = opt.value;
                sel.dispatchEvent(new Event('change', { bubbles: true }));
            });
        },
        clear() {
            this.value = '';
            this.label = '';
            this.query = '';
            this.open  = false;
            this.$nextTick(() => {
                const sel = this.$refs.hiddenSelect;
                sel.value = '';
                sel.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }
    }"
     :class="{ 'z-40': open }"
     class="relative inline-block text-left {{ $class }}"
     @click.outside="open = false; query = ''">

    {{-- Hidden native select for form submission --}}
    <select name="{{ $name }}" x-ref="hiddenSelect" class="hidden">
        <option value=""></option>
        @foreach ($dataOptions as $opt)
            <option value="{{ $opt['value'] }}" @selected($value == $opt['value'])>{{ $opt['label'] }}</option>
        @endforeach
    </select>

    {{-- Trigger: shows selected label or search input when open --}}
    <div @click="open = true; $nextTick(() => $refs.searchInput?.focus())"
         class="flex w-full cursor-pointer items-center justify-between gap-2 rounded-xl border border-stone-200 bg-white/90 px-3.5 py-2.5 text-sm font-medium shadow-[var(--shadow-card)] backdrop-blur-md transition-all duration-200 hover:border-stone-300 focus-within:border-kpi-red focus-within:ring-2 focus-within:ring-kpi-red/15 dark:border-white/5 dark:bg-kpi-dark-surface/90 dark:hover:border-white/10">

        <template x-if="!open">
            <span x-text="value ? label : '{{ $clearLabel ?? '— Pilih —' }}'"
                  :class="value ? 'text-kpi-black dark:text-stone-100' : 'text-stone-400 dark:text-stone-500'"
                  class="flex-1 truncate text-sm"></span>
        </template>

        <template x-if="open">
            <input x-ref="searchInput"
                   x-model="query"
                   type="text"
                   placeholder="{{ $placeholder }}"
                   @click.stop
                   @keydown.escape="open = false; query = ''"
                   @keydown.enter.prevent="filtered.length ? select(filtered[0]) : null"
                   class="flex-1 bg-transparent text-sm text-kpi-black outline-none placeholder:text-stone-400 dark:text-stone-100 dark:placeholder:text-stone-500">
        </template>

        <div class="flex shrink-0 items-center gap-1">
            {{-- Clear button --}}
            <template x-if="value && !open">
                <button type="button" @click.stop="clear()"
                        class="flex h-5 w-5 items-center justify-center rounded-full text-stone-400 transition hover:bg-stone-100 hover:text-kpi-red dark:hover:bg-white/10">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </template>
            {{-- Chevron --}}
            <svg class="h-4 w-4 text-kpi-gray transition-transform duration-200" :class="{ 'rotate-180': open }"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </div>

    {{-- Dropdown list --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute left-0 z-40 mt-1.5 w-full min-w-[240px] rounded-2xl border border-kpi-line bg-white/95 py-1.5 shadow-[var(--shadow-card-hover)] backdrop-blur-lg dark:border-white/10 dark:bg-kpi-dark-surface/95"
         x-cloak>

        {{-- Results list --}}
        <div class="max-h-56 overflow-y-auto px-1">
            @if ($clearLabel)
                {{-- "None / clear" option always visible at top --}}
                <button type="button" @click="clear()"
                        class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-stone-400 italic transition-colors hover:bg-kpi-cream/60 dark:text-stone-500 dark:hover:bg-white/[0.03]"
                        :class="{ 'bg-kpi-cream/40 font-semibold not-italic text-kpi-red dark:bg-white/[0.02] dark:text-kpi-gold': value === '' }">
                    {{ $clearLabel }}
                </button>
            @endif
            <template x-if="filtered.length === 0">
                <p class="px-3 py-3 text-center text-sm text-stone-400 dark:text-stone-500">
                    Tidak ada pegawai ditemukan.
                </p>
            </template>
            <template x-for="opt in filtered" :key="opt.value">
                <button type="button" @click="select(opt)"
                        class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-kpi-black transition-colors hover:bg-kpi-cream/60 dark:text-stone-200 dark:hover:bg-white/[0.03]"
                        :class="{ 'bg-kpi-cream/40 font-semibold text-kpi-red dark:bg-white/[0.02] dark:text-kpi-gold': value == opt.value }">
                    <span x-text="opt.label" class="truncate"></span>
                </button>
            </template>
        </div>
    </div>
</div>
