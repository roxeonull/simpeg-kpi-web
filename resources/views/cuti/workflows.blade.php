<x-app-layout title="Workflow Approval Cuti">
    {{-- Tab Navigation --}}
    <div class="mb-6 flex gap-1 overflow-x-auto border-b border-kpi-line dark:border-white/10">
        <a href="{{ route('cuti.index') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.index') && !request()->routeIs('cuti.kalender') && !request()->routeIs('cuti.analitik') && !request()->routeIs('cuti.rekomendasi') && !request()->routeIs('cuti.workflows') ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Daftar Pengajuan
        </a>
        <a href="{{ route('cuti.kalender') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.kalender') ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Kalender Tim
        </a>
        <a href="{{ route('cuti.analitik') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.analitik') ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Analitik
        </a>
        <a href="{{ route('cuti.rekomendasi') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.rekomendasi') ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Rekomendasi Cerdas
        </a>
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('cuti.workflows') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.workflows') ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Workflow Approval
        </a>
        @endif
    </div>

    <div x-data="workflowModal()" class="space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('error'))
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-300">
                {{ $errors->first('error') }}
            </div>
        @endif

        {{-- Page Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="font-serif text-2xl font-bold text-kpi-black dark:text-stone-100">Workflows</h1>
                <p class="mt-1 text-sm text-kpi-gray">Atur alur persetujuan cuti khusus per unit kerja</p>
            </div>
            <button @click="openCreateModal()" class="btn-primary flex items-center gap-2 shadow-[var(--shadow-card-hover)]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                + Buat Workflow
            </button>
        </div>

        {{-- Default Workflow Information Card --}}
        <div class="rounded-2xl border border-kpi-gold/30 bg-amber-500/5 p-4 dark:border-kpi-gold/20 dark:bg-kpi-gold/10">
            <div class="flex items-start gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-kpi-gold/20 text-kpi-gold dark:bg-kpi-gold/30">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-kpi-black dark:text-stone-100">Default Workflow (Standard Fallback)</h4>
                    <p class="mt-0.5 text-xs text-kpi-gray dark:text-stone-400 leading-relaxed">
                        Unit kerja yang belum diatur workflow khususnya akan otomatis menggunakan alur standar 2 tahap: <strong class="text-stone-800 dark:text-stone-200">Atasan Langsung &rarr; HR / Admin</strong>.
                    </p>
                </div>
            </div>
        </div>

        {{-- Workflow Cards List --}}
        @if ($workflows->isEmpty())
            <div class="card flex flex-col items-center justify-center py-12 text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-kpi-red-soft text-kpi-red dark:bg-kpi-red/20 mb-3">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h3 class="text-base font-semibold text-kpi-black dark:text-stone-100">Belum ada workflow custom</h3>
                <p class="mt-1 text-sm text-kpi-gray dark:text-stone-400 max-w-sm">Semua unit kerja saat ini masih memakai workflow default (Atasan Langsung &rarr; HR).</p>
                <button @click="openCreateModal()" class="btn-secondary mt-4">
                    + Buat Workflow Pertama
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                @foreach ($workflows as $wf)
                    <div class="card relative flex flex-col justify-between transition-all hover:shadow-lg">
                        <div>
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-kpi-red/10 text-kpi-red dark:bg-kpi-red/20 dark:text-kpi-red">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-base text-kpi-black dark:text-stone-100">{{ $wf->nama }}</h3>
                                        <p class="text-xs text-kpi-gray dark:text-stone-400">
                                            {{ $wf->unit?->nama_unit ?? 'Semua Unit' }} &middot; <span class="font-medium text-stone-700 dark:text-stone-300">{{ $wf->steps->count() }} approval levels</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button @click='openEditModal(@json($wf))' class="rounded-lg p-1.5 text-kpi-gray hover:bg-stone-100 hover:text-kpi-black dark:hover:bg-white/10 dark:hover:text-stone-200" title="Edit Workflow">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('cuti.workflows.destroy', $wf) }}" 
                                          @submit.prevent="if(confirm('Apakah Anda yakin ingin menghapus workflow \'{{ addslashes($wf->nama) }}\'?')) $el.submit()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg p-1.5 text-kpi-gray hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-500/10 dark:hover:text-rose-400" title="Hapus Workflow">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Approval Flow Visual Preview --}}
                            <div class="mt-6 border-t border-kpi-line pt-4 dark:border-white/10">
                                <p class="text-[11px] font-bold tracking-wider text-kpi-gray dark:text-stone-400 uppercase mb-3">APPROVAL FLOW</p>
                                <div class="flex items-center gap-2 overflow-x-auto pb-2">
                                    @foreach ($wf->steps as $idx => $step)
                                        <div class="flex items-center gap-2 shrink-0">
                                            <div class="flex flex-col items-center">
                                                @php
                                                    $nodeBg = match($step->tipe_step) {
                                                        'atasan_langsung' => 'bg-kpi-red text-white',
                                                        'hr_admin' => 'bg-kpi-gold text-white',
                                                        default => 'bg-emerald-600 text-white',
                                                    };
                                                @endphp
                                                <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold shadow-sm {{ $nodeBg }}">
                                                    L{{ $step->urutan }}
                                                </div>
                                                <span class="mt-1.5 text-[11px] font-medium text-stone-700 dark:text-stone-300 text-center max-w-[85px] leading-tight">
                                                    {{ $step->tipeStepLabel() }}
                                                </span>
                                            </div>
                                            @if (!$loop->last)
                                                <div class="flex items-center justify-center text-kpi-gray/60 dark:text-stone-500 mb-5">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Alpine.js Modal Create / Edit Workflow --}}
        <div x-show="showModal" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-stone-900 border border-kpi-line dark:border-white/10 my-8"
                 @click.away="closeModal()">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between border-b border-kpi-line pb-4 dark:border-white/10">
                    <h3 class="text-lg font-bold text-kpi-black dark:text-stone-100" x-text="isEdit ? 'Edit Workflow' : 'Create Workflow'"></h3>
                    <button @click="closeModal()" class="rounded-lg p-1 text-kpi-gray hover:bg-stone-100 dark:hover:bg-white/10">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Form --}}
                <form :action="isEdit ? updateUrl : '{{ route('cuti.workflows.store') }}'" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-kpi-gray dark:text-stone-300 mb-1">Workflow Name</label>
                        <input type="text" name="nama" x-model="form.nama" placeholder="e.g. Standard Approval, Workflow Sales" class="input w-full" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-kpi-gray dark:text-stone-300 mb-1">Unit Kerja / Department</label>
                        <select name="unit_id" x-model="form.unit_id" class="input w-full" required>
                            <option value="">-- Pilih Unit Kerja --</option>
                            @foreach ($units as $u)
                                <option value="{{ $u->id }}">{{ $u->nama_unit }} ({{ $u->kode_unit }})</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-[11px] text-kpi-gray dark:text-stone-400">Satu unit kerja hanya boleh memiliki 1 workflow khusus yang aktif.</p>
                    </div>

                    {{-- Dynamic Approval Steps --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-kpi-gray dark:text-stone-300 mb-2">Approval Steps</label>
                        
                        <div class="space-y-2.5 max-h-60 overflow-y-auto pr-1">
                            <template x-for="(step, index) in form.steps" :key="index">
                                <div class="flex items-center gap-2 rounded-xl border border-kpi-line bg-stone-50/60 p-3 dark:border-white/10 dark:bg-white/5">
                                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-kpi-red/10 text-xs font-bold text-kpi-red dark:bg-kpi-red/20 dark:text-kpi-red" x-text="index + 1"></div>
                                    
                                    <select :name="'steps[' + index + ']'" x-model="form.steps[index]" class="input flex-1 text-sm py-1.5" required>
                                        <option value="atasan_langsung">Atasan Langsung</option>
                                        <option value="hr_admin">HR / Admin</option>
                                    </select>

                                    <div class="flex items-center gap-1 shrink-0">
                                        <button type="button" @click="moveUp(index)" :disabled="index === 0" class="rounded p-1 text-kpi-gray hover:bg-stone-200 disabled:opacity-30 dark:hover:bg-white/10" title="Naikkan">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                        </button>
                                        <button type="button" @click="moveDown(index)" :disabled="index === form.steps.length - 1" class="rounded p-1 text-kpi-gray hover:bg-stone-200 disabled:opacity-30 dark:hover:bg-white/10" title="Turunkan">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                        <button type="button" @click="removeStep(index)" :disabled="form.steps.length <= 1" class="rounded p-1 text-rose-500 hover:bg-rose-100 disabled:opacity-30 dark:hover:bg-rose-500/20" title="Hapus Step">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <button type="button" @click="addStep()" class="mt-3 text-xs font-semibold text-kpi-red hover:underline flex items-center gap-1">
                            + Add Step
                        </button>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-kpi-line pt-4 dark:border-white/10">
                        <button type="button" @click="closeModal()" class="btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary" x-text="isEdit ? 'Save Changes' : 'Create Workflow'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function workflowModal() {
            return {
                showModal: false,
                isEdit: false,
                updateUrl: '',
                form: {
                    nama: '',
                    unit_id: '',
                    steps: ['atasan_langsung', 'hr_admin']
                },
                openCreateModal() {
                    this.isEdit = false;
                    this.updateUrl = '';
                    this.form = {
                        nama: '',
                        unit_id: '',
                        steps: ['atasan_langsung', 'hr_admin']
                    };
                    this.showModal = true;
                },
                openEditModal(wf) {
                    this.isEdit = true;
                    this.updateUrl = `/cuti/workflows/${wf.id}`;
                    this.form = {
                        nama: wf.nama,
                        unit_id: wf.unit_id,
                        steps: wf.steps.map(s => s.tipe_step)
                    };
                    if (this.form.steps.length === 0) {
                        this.form.steps = ['atasan_langsung', 'hr_admin'];
                    }
                    this.showModal = true;
                },
                closeModal() {
                    this.showModal = false;
                },
                addStep() {
                    this.form.steps.push('hr_admin');
                },
                removeStep(index) {
                    if (this.form.steps.length > 1) {
                        this.form.steps.splice(index, 1);
                    }
                },
                moveUp(index) {
                    if (index > 0) {
                        const temp = this.form.steps[index];
                        this.form.steps[index] = this.form.steps[index - 1];
                        this.form.steps[index - 1] = temp;
                    }
                },
                moveDown(index) {
                    if (index < this.form.steps.length - 1) {
                        const temp = this.form.steps[index];
                        this.form.steps[index] = this.form.steps[index + 1];
                        this.form.steps[index + 1] = temp;
                    }
                }
            }
        }
    </script>
</x-app-layout>
