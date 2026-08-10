@props([
    'options'   => [10, 15, 25, 50],
    'current'   => 15,
    'paramName' => 'per_page',
])

@php
    $current = (int) $current;
    $selectOptions = [];
    foreach ($options as $opt) {
        $selectOptions[] = ['value' => (string) $opt, 'label' => $opt . ' baris'];
    }
@endphp

<div class="flex items-center gap-2"
     @change="(function(e){
         var val = e.target.value;
         if (!val) return;
         var url = new URL(window.location.href);
         url.searchParams.set('{{ $paramName }}', val);
         url.searchParams.delete('page');
         window.location.href = url.toString();
     })($event)">
    <span class="text-xs font-medium text-kpi-gray dark:text-stone-400 whitespace-nowrap hidden sm:block">Tampilkan</span>
    <x-select :name="$paramName" :value="(string) $current" :options="$selectOptions" size="sm" direction="up" class="min-w-[115px]" />
    <span class="text-xs font-medium text-kpi-gray dark:text-stone-400 whitespace-nowrap hidden sm:block">per halaman</span>
</div>

