@extends('layouts.app')

@section('title', 'POS Billing Terminal')

@section('content')
<div class="lg:h-[calc(100vh-6.5rem)] overflow-y-auto flex flex-col lg:flex-row gap-4">
    <!-- Left Column: Products Grid & Filter (Flexible width) -->
    <div class="flex-1 min-h-[65vh] lg:min-h-0 flex flex-col bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Top Toolbar: Search & Category Pills -->
        <div class="p-3 sm:p-4 border-b border-slate-200 bg-slate-50/70 space-y-3">
            <div class="flex items-center gap-3">
                <div class="relative flex-1">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" id="pos-search" placeholder="Search menu by name or SKU... (e.g. Momo, Chiya, Beer)" 
                           class="w-full pl-9 pr-4 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <button type="button" id="clear-search-btn" class="hidden px-2.5 py-2 text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-200/70 rounded-lg">
                    Clear
                </button>
            </div>

            <!-- Categories Horizontal Filter Scroll -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar" id="category-pills">
                <button type="button" data-cat="all" class="cat-pill active px-3 py-1.5 rounded-full font-semibold bg-emerald-600 text-white shadow-sm transition whitespace-nowrap">
                    All Menu Items
                </button>
                @foreach($categories as $category)
                <button type="button" data-cat="{{ $category->id }}" class="cat-pill px-3 py-1.5 rounded-full font-medium bg-white text-slate-600 border border-slate-200 hover:bg-slate-100 transition whitespace-nowrap">
                    {{ $category->name }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Products Grid Container -->
        <div class="flex-1 overflow-y-auto p-3 sm:p-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 xl:grid-cols-4 gap-3" id="products-grid">
                @foreach($items as $item)
                <div class="product-card bg-white rounded-xl border border-slate-200 p-3 hover:border-emerald-400 hover:shadow-md transition-all flex flex-col justify-between cursor-pointer select-none group"
                     data-id="{{ $item->id }}"
                     data-name="{{ $item->name }}"
                     data-price="{{ (float)$item->price }}"
                     data-cost="{{ (float)$item->cost }}"
                     data-unit="{{ $item->unit }}"
                     data-category="{{ $item->category_id }}"
                     data-sku="{{ $item->sku }}">
                    <div>
                        <!-- Header with Category -->
                        <div class="flex items-center justify-between gap-1 mb-1.5">
                            <span class="text-[10px] font-semibold text-slate-400 uppercase truncate">
                                {{ $item->category->name ?? 'Menu' }}
                            </span>
                        </div>

                        <!-- Item Name -->
                        <h3 class="text-sm font-bold text-slate-900 leading-snug group-hover:text-emerald-700 transition line-clamp-2">
                            {{ $item->name }}
                        </h3>
                        <p class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $item->sku }}</p>
                    </div>

                    <!-- Bottom Price & Add Action -->
                    <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-sm font-extrabold text-slate-900 font-mono">
                            {{ format_npr($item->price) }}
                        </span>
                        <span class="text-[10px] text-slate-400">{{ $item->category->name ?? '' }}</span>
                        <button type="button" class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Empty Search State -->
            <div id="no-products-found" class="hidden py-16 text-center text-slate-400">
                <i data-lucide="package-x" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                <p class="text-sm font-medium">No menu items match your search filter.</p>
            </div>
        </div>
    </div>

    <!-- Right Column: Cart / Billing Terminal (Fixed width on desktop) -->
    <div class="w-full lg:w-96 xl:w-[420px] lg:h-full max-h-full flex flex-col bg-white rounded-xl border border-slate-200 shadow-sm overflow-y-auto flex-shrink-0">
        <!-- Terminal Header -->
        <div class="p-3.5 border-b border-emerald-200 bg-emerald-600 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded bg-white/20 flex items-center justify-center text-white">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-white">Active Order Cart</h2>
                    <p class="text-[10px] text-emerald-100">Cashier: {{ auth()->user()->name }}</p>
                </div>
            </div>
            <button type="button" id="clear-cart-btn" class="text-xs text-emerald-100 hover:text-rose-300 flex items-center gap-1">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                <span>Clear</span>
            </button>
        </div>

        <!-- Customer Name Input -->
        <div class="p-3 border-b border-slate-100 bg-slate-50/80">
            <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Customer Name / Table / Note</label>
            <input type="text" id="cart-customer" value="Walk-in Customer" 
                   class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <!-- Cart Items List (grows — the whole terminal column scrolls as one section) -->
        <div class="flex-1 p-3 divide-y divide-slate-100" id="cart-items-container">
            <!-- Dynamically populated by JS -->
            <div id="cart-empty-placeholder" class="py-12 text-center text-slate-400">
                <i data-lucide="shopping-cart" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                <p class="text-xs font-medium">Cart is empty</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Click any menu item to add it to the order</p>
            </div>
        </div>

        <!-- Calculations & Checkout Controls (Sticky at bottom of cart) -->
        <div class="p-3.5 border-t border-slate-200 bg-slate-50 space-y-3">
            <!-- Calculations Breakdown -->
            <div class="space-y-1.5 text-xs text-slate-600">
                <div class="flex items-center justify-between">
                    <span>Subtotal</span>
                    <span id="summary-subtotal" class="font-mono font-semibold text-slate-900">Rs. 0.00</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-1">
                        <span>Discount (NPR)</span>
                    </span>
                    <div class="w-28">
                        <input type="number" id="cart-discount" min="0" step="1" value="0" 
                               class="w-full text-right px-2 py-1 text-xs border border-slate-300 rounded bg-white font-mono focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <span>VAT ({{ $taxPercentage }}%)</span>
                    <span id="summary-tax" class="font-mono text-slate-700">Rs. 0.00</span>
                </div>

                <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-sm font-extrabold text-slate-900">
                    <span>Grand Total</span>
                    <span id="summary-grand-total" class="text-lg font-extrabold text-emerald-700 font-mono">Rs. 0.00</span>
                </div>
            </div>

            <!-- Payment Method Buttons -->
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Payment Method</label>
                <div class="grid grid-cols-2 gap-1.5" id="payment-methods">
                    <button type="button" data-method="Cash" class="pay-btn active py-2 px-1 rounded-lg border text-xs font-bold text-center transition bg-emerald-600 text-white border-emerald-600 shadow-sm">
                        Cash
                    </button>
                    <button type="button" data-method="Online" class="pay-btn py-2 px-1 rounded-lg border text-xs font-bold text-center transition bg-white text-slate-700 border-slate-200 hover:border-blue-500">
                        Online
                    </button>
                </div>
            </div>

            <!-- Cash Tendered & Change Due Section (Visible when Cash selected) -->
            <div id="cash-tender-box" class="p-2.5 rounded-lg bg-emerald-50/70 border border-emerald-200 space-y-2">
                <div class="flex items-center justify-between gap-2">
                    <label class="text-[11px] font-semibold text-emerald-900">Cash Received (Rs.)</label>
                    <input type="number" id="cash-tendered" placeholder="0.00" min="0" step="10"
                           class="w-32 px-2.5 py-1 text-sm font-bold font-mono text-right bg-white border border-emerald-300 rounded focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                <!-- Quick Cash Presets -->
                <div class="flex items-center justify-between gap-1 text-[10px]">
                    <button type="button" class="quick-cash px-2 py-0.5 rounded bg-white border border-emerald-200 hover:bg-emerald-100 font-mono" data-add="exact">Exact</button>
                    <button type="button" class="quick-cash px-2 py-0.5 rounded bg-white border border-emerald-200 hover:bg-emerald-100 font-mono" data-add="100">100</button>
                    <button type="button" class="quick-cash px-2 py-0.5 rounded bg-white border border-emerald-200 hover:bg-emerald-100 font-mono" data-add="500">500</button>
                    <button type="button" class="quick-cash px-2 py-0.5 rounded bg-white border border-emerald-200 hover:bg-emerald-100 font-mono" data-add="1000">1,000</button>
                    <button type="button" class="quick-cash px-2 py-0.5 rounded bg-white border border-emerald-200 hover:bg-emerald-100 font-mono" data-add="2000">2,000</button>
                </div>
                <div class="flex items-center justify-between text-xs font-bold pt-1 border-t border-emerald-200/80">
                    <span class="text-emerald-900">Change Due to Customer:</span>
                    <span id="cash-change-display" class="font-mono text-base font-extrabold text-emerald-700">Rs. 0.00</span>
                </div>
            </div>

            <!-- Optional Transaction Reference for Online payments -->
            <div id="txn-ref-box" class="hidden">
                <input type="text" id="txn-reference" placeholder="Transaction / Reference ID (Optional)"
                       class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs focus:ring-1 focus:ring-emerald-500">
            </div>

            <!-- Complete Order CTA Button -->
            <button type="button" id="complete-sale-btn" disabled
                    class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-sm shadow-md transition flex items-center justify-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>Complete Sale & Print Invoice</span>
            </button>
        </div>
    </div>
</div>

<!-- Floating scroll-to-checkout button (appears when Complete Sale is off-screen) -->
<button type="button" id="scroll-to-checkout-btn" title="Scroll to Complete Sale & Print Invoice"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-2 px-4 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xl transition-all"
        style="display: none;">
    <i data-lucide="chevrons-down" class="w-4 h-4"></i>
    <span>Scroll to Complete Sale</span>
</button>

<!-- Checkout Success Modal -->
<div id="checkout-modal" class="fixed inset-0 z-50 bg-black/60 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 text-center space-y-4">
        <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
            <i data-lucide="check" class="w-8 h-8"></i>
        </div>

        <div>
            <h3 class="text-lg font-bold text-slate-900">Payment Completed!</h3>
            <p class="text-xs text-slate-500 mt-1">Invoice generated and inventory deducted successfully.</p>
        </div>

        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-700 space-y-1">
            <div class="flex justify-between">
                <span class="text-slate-500">Invoice Number:</span>
                <span id="modal-invoice-num" class="font-mono font-bold text-slate-900">-</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Paid Total:</span>
                <span id="modal-total-amt" class="font-mono font-bold text-emerald-700">Rs. 0.00</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 pt-2">
            <a id="modal-print-btn" href="#" target="_blank" class="py-2.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-xs flex items-center justify-center gap-1.5 shadow">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Print Thermal (80mm)</span>
            </a>
            <a id="modal-view-btn" href="#" class="py-2.5 px-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-semibold text-xs flex items-center justify-center gap-1.5 shadow">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span>View Full Invoice</span>
            </a>
        </div>

        <button type="button" id="modal-new-sale-btn" class="w-full py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 border border-slate-200 rounded-lg hover:bg-slate-50">
            Start New Order
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const TAX_RATE = {{ $taxPercentage }};
    let cart = [];
    let selectedPaymentMethod = 'Cash';

    const cartContainer = document.getElementById('cart-items-container');
    const emptyPlaceholder = document.getElementById('cart-empty-placeholder');
    const subtotalEl = document.getElementById('summary-subtotal');
    const taxEl = document.getElementById('summary-tax');
    const grandTotalEl = document.getElementById('summary-grand-total');
    const discountInput = document.getElementById('cart-discount');
    const customerInput = document.getElementById('cart-customer');
    const cashTenderedInput = document.getElementById('cash-tendered');
    const cashChangeDisplay = document.getElementById('cash-change-display');
    const checkoutBtn = document.getElementById('complete-sale-btn');
    const scrollToCheckoutBtn = document.getElementById('scroll-to-checkout-btn');
    const searchInput = document.getElementById('pos-search');
    const clearSearchBtn = document.getElementById('clear-search-btn');

    // 1. Add item to cart
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function() {
            const id = parseInt(this.dataset.id);
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price);
            const cost = parseFloat(this.dataset.cost);
            const unit = this.dataset.unit;

            const existingIndex = cart.findIndex(item => item.id === id);
            if (existingIndex > -1) {
                cart[existingIndex].quantity += 1;
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    cost: cost,
                    unit: unit,
                    quantity: 1
                });
            }

            renderCart();
        });
    });

    // 2. Render Cart
    function renderCart() {
        if (cart.length === 0) {
            cartContainer.innerHTML = '';
            cartContainer.appendChild(emptyPlaceholder);
            emptyPlaceholder.classList.remove('hidden');
            checkoutBtn.disabled = true;
            updateTotals(0, 0, 0);
            return;
        }

        emptyPlaceholder.classList.add('hidden');
        cartContainer.innerHTML = '';

        let subtotal = 0;

        cart.forEach((item, index) => {
            const lineSubtotal = item.price * item.quantity;
            subtotal += lineSubtotal;

            const row = document.createElement('div');
            row.className = 'py-2.5 flex items-center justify-between gap-2';
            row.innerHTML = `
                <div class="flex-1 min-w-0">
                    <h4 class="text-xs font-bold text-slate-900 truncate">${item.name}</h4>
                    <div class="text-[11px] text-slate-500 font-mono">
                        Rs. ${item.price.toFixed(2)} / ${item.unit}
                    </div>
                </div>
                <div class="flex items-center gap-1.5">
                    <button type="button" class="btn-qty-dec w-6 h-6 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs" data-index="${index}">-</button>
                    <span class="w-7 text-center font-bold font-mono text-xs">${item.quantity}</span>
                    <button type="button" class="btn-qty-inc w-6 h-6 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs" data-index="${index}">+</button>
                </div>
                <div class="text-right w-20">
                    <span class="font-mono font-bold text-xs text-slate-900">Rs. ${lineSubtotal.toFixed(2)}</span>
                    <button type="button" class="btn-item-remove block ml-auto mt-0.5 text-slate-400 hover:text-rose-600 text-[10px]" data-index="${index}">Remove</button>
                </div>
            `;
            cartContainer.appendChild(row);
        });

        // Add Listeners to Cart actions
        document.querySelectorAll('.btn-qty-dec').forEach(b => {
            b.addEventListener('click', function(e) {
                e.stopPropagation();
                const idx = parseInt(this.dataset.index);
                if (cart[idx].quantity > 1) {
                    cart[idx].quantity -= 1;
                } else {
                    cart.splice(idx, 1);
                }
                renderCart();
            });
        });

        document.querySelectorAll('.btn-qty-inc').forEach(b => {
            b.addEventListener('click', function(e) {
                e.stopPropagation();
                const idx = parseInt(this.dataset.index);
                cart[idx].quantity += 1;
                renderCart();
            });
        });

        document.querySelectorAll('.btn-item-remove').forEach(b => {
            b.addEventListener('click', function(e) {
                e.stopPropagation();
                const idx = parseInt(this.dataset.index);
                cart.splice(idx, 1);
                renderCart();
            });
        });

        const discount = parseFloat(discountInput.value) || 0;
        const taxable = Math.max(0, subtotal - discount);
        const tax = (taxable * TAX_RATE) / 100;
        const grandTotal = taxable + tax;

        updateTotals(subtotal, tax, grandTotal);
        checkoutBtn.disabled = false;
        calculateChange(grandTotal);
        updateScrollToCheckoutVisibility();
    }

    function updateTotals(subtotal, tax, grandTotal) {
        subtotalEl.textContent = 'Rs. ' + subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        taxEl.textContent = 'Rs. ' + tax.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        grandTotalEl.textContent = 'Rs. ' + grandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function calculateChange(grandTotal) {
        if (selectedPaymentMethod !== 'Cash') return;
        const tendered = parseFloat(cashTenderedInput.value) || 0;
        const change = tendered - grandTotal;
        if (change >= 0) {
            cashChangeDisplay.textContent = 'Rs. ' + change.toFixed(2);
            cashChangeDisplay.classList.remove('text-rose-600');
            cashChangeDisplay.classList.add('text-emerald-700');
        } else {
            cashChangeDisplay.textContent = 'Rs. 0.00 (Short Rs. ' + Math.abs(change).toFixed(2) + ')';
            cashChangeDisplay.classList.remove('text-emerald-700');
            cashChangeDisplay.classList.add('text-rose-600');
        }
    }

    discountInput.addEventListener('input', function() {
        renderCart();
    });

    cashTenderedInput.addEventListener('input', function() {
        const grandTotal = getGrandTotal();
        calculateChange(grandTotal);
    });

    function getGrandTotal() {
        let subtotal = 0;
        cart.forEach(i => subtotal += i.price * i.quantity);
        const discount = parseFloat(discountInput.value) || 0;
        const taxable = Math.max(0, subtotal - discount);
        const tax = (taxable * TAX_RATE) / 100;
        return taxable + tax;
    }

    // Quick cash buttons
    document.querySelectorAll('.quick-cash').forEach(btn => {
        btn.addEventListener('click', function() {
            const add = this.dataset.add;
            const gt = getGrandTotal();
            if (add === 'exact') {
                cashTenderedInput.value = Math.ceil(gt);
            } else {
                cashTenderedInput.value = parseInt(add);
            }
            calculateChange(gt);
        });
    });

    // Clear cart
    document.getElementById('clear-cart-btn').addEventListener('click', function() {
        if (cart.length > 0 && confirm('Are you sure you want to clear the active cart?')) {
            cart = [];
            renderCart();
        }
    });

    // Payment Methods Tabs
    document.querySelectorAll('.pay-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.pay-btn').forEach(b => {
                b.classList.remove('active', 'bg-emerald-600', 'text-white', 'border-emerald-600');
                b.classList.add('bg-white', 'text-slate-700', 'border-slate-200');
            });

            this.classList.add('active', 'bg-emerald-600', 'text-white', 'border-emerald-600');
            this.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');

            selectedPaymentMethod = this.dataset.method;
            const cashBox = document.getElementById('cash-tender-box');
            const refBox = document.getElementById('txn-ref-box');

            if (selectedPaymentMethod === 'Cash') {
                cashBox.classList.remove('hidden');
                refBox.classList.add('hidden');
            } else {
                cashBox.classList.add('hidden');
                refBox.classList.remove('hidden');
            }
        });
    });

    // Category Filter Pills
    document.querySelectorAll('.cat-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            document.querySelectorAll('.cat-pill').forEach(p => {
                p.classList.remove('active', 'bg-emerald-600', 'text-white');
                p.classList.add('bg-white', 'text-slate-600');
            });
            this.classList.add('active', 'bg-emerald-600', 'text-white');
            this.classList.remove('bg-white', 'text-slate-600');

            filterProducts();
        });
    });

    // Search input
    searchInput.addEventListener('input', function() {
        if (this.value.trim().length > 0) {
            clearSearchBtn.classList.remove('hidden');
        } else {
            clearSearchBtn.classList.add('hidden');
        }
        filterProducts();
    });

    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        this.classList.add('hidden');
        filterProducts();
    });

    function filterProducts() {
        const query = searchInput.value.toLowerCase().trim();
        const activeCatPill = document.querySelector('.cat-pill.active');
        const activeCat = activeCatPill ? activeCatPill.dataset.cat : 'all';

        let visibleCount = 0;
        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.dataset.name.toLowerCase();
            const sku = card.dataset.sku.toLowerCase();
            const catId = card.dataset.category;

            const matchesSearch = query === '' || name.includes(query) || sku.includes(query);
            const matchesCat = activeCat === 'all' || catId === activeCat;

            if (matchesSearch && matchesCat) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        const noItems = document.getElementById('no-products-found');
        if (visibleCount === 0) {
            noItems.classList.remove('hidden');
        } else {
            noItems.classList.add('hidden');
        }
    }

    // 3. Floating "Scroll to Complete Sale" button — appears only when the
    //    Complete Sale button is out of view and scrolls you straight to it.
    function updateScrollToCheckoutVisibility() {
        if (!scrollToCheckoutBtn || !checkoutBtn) return;
        const rect = checkoutBtn.getBoundingClientRect();
        const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
        scrollToCheckoutBtn.style.display = isVisible ? 'none' : 'flex';
    }

    scrollToCheckoutBtn.addEventListener('click', function() {
        checkoutBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    // Re-check whenever anything scrolls (page or cart panel) or the layout changes
    document.addEventListener('scroll', updateScrollToCheckoutVisibility, true);
    window.addEventListener('resize', updateScrollToCheckoutVisibility, { passive: true });
    document.addEventListener('click', function() {
        setTimeout(updateScrollToCheckoutVisibility, 60);
    }, { passive: true });
    updateScrollToCheckoutVisibility();

    // 4. Complete Sale / Checkout
    checkoutBtn.addEventListener('click', function() {
        if (cart.length === 0) return;

        const customerName = customerInput.value.trim() || 'Walk-in Customer';
        const discount = parseFloat(discountInput.value) || 0;
        const reference = document.getElementById('txn-reference').value.trim();

        const payload = {
            customer_name: customerName,
            discount: discount,
            payment_method: selectedPaymentMethod,
            transaction_reference: reference,
            items: cart.map(i => ({
                item_id: i.id,
                quantity: i.quantity
            }))
        };

        checkoutBtn.disabled = true;
        checkoutBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing Sale...
        `;

        fetch('{{ route("pos.checkout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.error || 'Checkout failed.');
            }
            return data;
        })
        .then(data => {
            // Success Modal
            document.getElementById('modal-invoice-num').textContent = data.invoice_number;
            document.getElementById('modal-total-amt').textContent = grandTotalEl.textContent;
            document.getElementById('modal-print-btn').href = data.print_url;
            document.getElementById('modal-view-btn').href = data.redirect_url;
            document.getElementById('checkout-modal').classList.remove('hidden');

            // Reset Cart
            cart = [];
            renderCart();
            discountInput.value = 0;
            cashTenderedInput.value = '';
            customerInput.value = 'Walk-in Customer';
            document.getElementById('txn-reference').value = '';
        })
        .catch(err => {
            alert('Error completing sale: ' + err.message);
        })
        .finally(() => {
            checkoutBtn.disabled = false;
            checkoutBtn.innerHTML = `
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>Complete Sale & Print Invoice</span>
            `;
            if (window.lucide) lucide.createIcons();
        });
    });

    // Modal New Sale Button
    document.getElementById('modal-new-sale-btn').addEventListener('click', function() {
        document.getElementById('checkout-modal').classList.add('hidden');
        // Reload page to refresh real-time inventory counts
        window.location.reload();
    });
});
</script>
@endpush
