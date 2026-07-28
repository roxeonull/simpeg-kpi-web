@props([
    'name',
    'value' => '',
    'options' => [], // Array format: [['value' => '...', 'label' => '...'], ...]
    'placeholder' => 'Pilih...',
    'class' => '',
])

<div x-data="{
    open: false,
    value: '{{ $value }}',
    label: '',
    options: {{ json_encode($options) }},
    init() {
        const found = this.options.find(o => o.value == this.value);
        this.label = found ? found.label : (this.options[0] ? this.options[0].label : '{{ $placeholder }}');
        
        this.$watch('value', v => {
            const f = this.options.find(o => o.value == v);
            this.label = f ? f.label : '{{ $placeholder }}';
        });
    },
    select(opt) {
        this.value = opt.value;
        this.label = opt.label;
        this.open = false;
        
        this.$nextTick(() => {
            const selectEl = this.$refs.hiddenSelect;
            selectEl.value = opt.value;
            selectEl.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }
}" :class="{ 'z-50': open }" class="relative inline-block text-left {{ $class }}" @click.outside="open = false">
    
    <!-- Hidden native select to keep compatibility with standard PHP forms & JS handlers -->
    <select name="{{ $name }}" x-ref="hiddenSelect" class="hidden">
        @foreach ($options as $opt)
            <option value="{{ $opt['value'] }}" @selected($value == $opt['value'])>{{ $opt['label'] }}</option>
        @endforeach
    </select>

    <!-- Trigger Button -->
    <button type="button" @click="open = !open" 
            class="flex w-full items-center justify-between gap-2.5 rounded-xl border border-stone-200 bg-white/90 px-3.5 py-2.5 text-sm font-medium text-kpi-black shadow-[var(--shadow-card)] backdrop-blur-md transition-all duration-200 hover:border-stone-300 hover:bg-stone-50/50 focus:border-kpi-red focus:outline-none focus:ring-2 focus:ring-kpi-red/15 dark:border-white/5 dark:bg-kpi-dark-surface/90 dark:text-stone-100 dark:hover:border-white/10 dark:hover:bg-white/[0.02]">
        <span x-text="label" class="truncate"></span>
        <svg class="h-4 w-4 shrink-0 text-kpi-gray transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <!-- Floating Options Dropdown Menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute left-0 z-50 mt-1.5 max-h-60 w-full min-w-[200px] overflow-y-auto rounded-2xl border border-kpi-line bg-white/95 py-1.5 shadow-[var(--shadow-card-hover)] backdrop-blur-lg dark:border-white/10 dark:bg-kpi-dark-surface/95"
         x-cloak>
        <div class="px-1 space-y-0.5">
            <template x-for="opt in options" :key="opt.value">
                <button type="button" @click="select(opt)" 
                        class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-kpi-black transition-colors hover:bg-kpi-cream/60 dark:text-stone-200 dark:hover:bg-white/[0.03]"
                        :class="{ 'bg-kpi-cream/40 font-semibold dark:bg-white/[0.02] text-kpi-red dark:text-kpi-gold': value == opt.value }">
                    <span x-text="opt.label" class="truncate"></span>
                </button>
            </template>
        </div>
    </div>
</div>
