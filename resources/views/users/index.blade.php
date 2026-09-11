@extends('layouts.app')

@section('title', 'Staff & User Roles')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Staff & Roles Management</h1>
            <p class="text-sm text-slate-500">Manage user accounts and role permissions (Admin, Cashier, Stock Manager).</p>
        </div>
        <button type="button" id="btn-create-user" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Add Staff Member</span>
        </button>
    </div>

    <!-- Staff Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Staff Member</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4">Contact Phone</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $u)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900">{{ $u->name }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $u->email }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold 
                                {{ $u->role->slug === 'admin' ? 'bg-indigo-50 text-indigo-700' : '' }}
                                {{ $u->role->slug === 'cashier' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                {{ $u->role->slug === 'stock_manager' ? 'bg-amber-50 text-amber-700' : '' }}">
                                {{ $u->role->name ?? 'None' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 font-mono">
                            {{ $u->phone ?: '-' }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($u->is_active)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700">Active</span>
                            @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700">Deactivated</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" class="btn-edit-user px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px]"
                                        data-id="{{ $u->id }}" data-name="{{ $u->name }}" data-email="{{ $u->email }}" data-phone="{{ $u->phone }}" data-role="{{ $u->role_id }}" data-active="{{ $u->is_active ? 1 : 0 }}">
                                    Edit
                                </button>
                                @if($u->id !== auth()->id())
                                <form action="{{ route('users.toggle', $u->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 rounded text-[11px] font-semibold {{ $u->is_active ? 'text-rose-600 hover:bg-rose-50' : 'text-emerald-600 hover:bg-emerald-50' }}">
                                        {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Create / Edit User -->
<div id="modal-user" class="fixed inset-0 z-50 bg-black/60 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 id="user-modal-title" class="text-sm font-bold text-slate-900">Add Staff Member</h3>
            <button type="button" id="close-user-modal" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="form-user" method="POST" action="{{ route('users.store') }}" class="space-y-4">
            @csrf
            <div id="user-method-container"></div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Full Name *</label>
                <input type="text" name="name" id="user-name-input" required placeholder="e.g. Ramesh Adhikari"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-1 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Email Address *</label>
                <input type="email" name="email" id="user-email-input" required placeholder="staff@restaurant.com"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-1 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Phone Number</label>
                <input type="text" name="phone" id="user-phone-input" placeholder="+977-98XXXXXXXX"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-1 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Assigned Role *</label>
                <select name="role_id" id="user-role-input" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-1 focus:ring-emerald-500">
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}">{{ $r->name }} - {{ $r->description }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Password <span id="pass-hint" class="text-slate-400 font-normal"></span></label>
                <input type="password" name="password" id="user-password-input" placeholder="••••••••"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-1 focus:ring-emerald-500">
            </div>

            <div id="user-active-container" class="hidden">
                <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_active" id="user-active-input" value="1" class="rounded text-emerald-600 focus:ring-emerald-500">
                    <span>Account is active</span>
                </label>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" id="cancel-user-modal" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg shadow">Save Staff</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal-user');
    const form = document.getElementById('form-user');
    const title = document.getElementById('user-modal-title');
    const nameInput = document.getElementById('user-name-input');
    const emailInput = document.getElementById('user-email-input');
    const phoneInput = document.getElementById('user-phone-input');
    const roleInput = document.getElementById('user-role-input');
    const passInput = document.getElementById('user-password-input');
    const passHint = document.getElementById('pass-hint');
    const activeContainer = document.getElementById('user-active-container');
    const activeInput = document.getElementById('user-active-input');
    const methodContainer = document.getElementById('user-method-container');

    document.getElementById('btn-create-user').addEventListener('click', function() {
        form.action = "{{ route('users.store') }}";
        methodContainer.innerHTML = '';
        title.textContent = 'Add Staff Member';
        nameInput.value = '';
        emailInput.value = '';
        phoneInput.value = '';
        passInput.required = true;
        passHint.textContent = '*(Required)';
        activeContainer.classList.add('hidden');
        modal.classList.remove('hidden');
    });

    document.querySelectorAll('.btn-edit-user').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            form.action = `/users/${id}`;
            methodContainer.innerHTML = '@method("PUT")';
            title.textContent = 'Edit Staff Member';
            nameInput.value = this.dataset.name;
            emailInput.value = this.dataset.email;
            phoneInput.value = this.dataset.phone;
            roleInput.value = this.dataset.role;
            passInput.required = false;
            passHint.textContent = '(Leave blank to keep existing)';
            activeContainer.classList.remove('hidden');
            activeInput.checked = this.dataset.active === '1';
            modal.classList.remove('hidden');
        });
    });

    function closeModal() {
        modal.classList.add('hidden');
    }
    document.getElementById('close-user-modal').addEventListener('click', closeModal);
    document.getElementById('cancel-user-modal').addEventListener('click', closeModal);
});
</script>
@endpush
