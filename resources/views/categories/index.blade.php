@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Menu & Stock Categories</h1>
            <p class="text-sm text-slate-500">Organize food dishes, beverages, raw materials, and packaging supplies.</p>
        </div>
        <button type="button" id="btn-create-category" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Category</span>
        </button>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($categories as $category)
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $category->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <span class="text-xs font-semibold text-slate-400">
                        {{ $category->menu_items_count }} menu · {{ $category->inventory_items_count }} stock
                    </span>
                </div>
                <h3 class="text-base font-bold text-slate-900">{{ $category->name }}</h3>
                <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $category->description ?: 'No description provided.' }}</p>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                @if($category->menu_items_count > 0 && auth()->user()->isAdmin())
                <a href="{{ route('menu.index', ['category' => $category->id]) }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700">
                    View Menu &rarr;
                </a>
                @endif
                <a href="{{ route('inventory.index', ['category' => $category->id]) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                    View Stock &rarr;
                </a>
                <div class="flex items-center gap-2">
                    <button type="button" class="btn-edit-cat text-xs text-slate-500 hover:text-slate-800 font-semibold"
                            data-id="{{ $category->id }}" data-name="{{ $category->name }}" data-desc="{{ $category->description }}" data-active="{{ $category->is_active ? 1 : 0 }}">
                        Edit
                    </button>
                    @if($category->menu_items_count == 0 && $category->inventory_items_count == 0)
                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-rose-500 hover:text-rose-700">Delete</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal: Create / Edit Category -->
<div id="modal-category" class="fixed inset-0 z-50 bg-black/60 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 id="cat-modal-title" class="text-sm font-bold text-slate-900">Add Category</h3>
            <button type="button" id="close-cat-modal" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="form-category" method="POST" action="{{ route('categories.store') }}" class="space-y-4">
            @csrf
            <div id="cat-method-container"></div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Category Name *</label>
                <input type="text" name="name" id="cat-name-input" required placeholder="e.g. Traditional Thali"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-1 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Description</label>
                <textarea name="description" id="cat-desc-input" rows="3" placeholder="Brief details regarding items in this category"
                          class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-1 focus:ring-emerald-500"></textarea>
            </div>

            <div id="cat-active-checkbox-container" class="hidden">
                <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_active" id="cat-active-input" value="1" class="rounded text-emerald-600 focus:ring-emerald-500">
                    <span>Active in POS menu filter</span>
                </label>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" id="cancel-cat-modal" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg shadow">Save Category</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal-category');
    const form = document.getElementById('form-category');
    const title = document.getElementById('cat-modal-title');
    const nameInput = document.getElementById('cat-name-input');
    const descInput = document.getElementById('cat-desc-input');
    const activeBoxContainer = document.getElementById('cat-active-checkbox-container');
    const activeInput = document.getElementById('cat-active-input');
    const methodContainer = document.getElementById('cat-method-container');

    // Create Click
    document.getElementById('btn-create-category').addEventListener('click', function() {
        form.action = "{{ route('categories.store') }}";
        methodContainer.innerHTML = '';
        title.textContent = 'Add Category';
        nameInput.value = '';
        descInput.value = '';
        activeBoxContainer.classList.add('hidden');
        modal.classList.remove('hidden');
    });

    // Edit Click
    document.querySelectorAll('.btn-edit-cat').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            form.action = `/categories/${id}`;
            methodContainer.innerHTML = '@method("PUT")';
            title.textContent = 'Edit Category';
            nameInput.value = this.dataset.name;
            descInput.value = this.dataset.desc;
            activeBoxContainer.classList.remove('hidden');
            activeInput.checked = this.dataset.active === '1';
            modal.classList.remove('hidden');
        });
    });

    // Close Modal
    function closeModal() {
        modal.classList.add('hidden');
    }
    document.getElementById('close-cat-modal').addEventListener('click', closeModal);
    document.getElementById('cancel-cat-modal').addEventListener('click', closeModal);
});
</script>
@endpush
