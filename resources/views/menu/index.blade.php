@extends('layouts.app')

@section('title', 'Menu Transaksi')
@section('page-title', 'Menu Transaksi')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/transaksi.css') }}">
@endpush

@section('content')
<div class="pos-container">
    <!-- Menu Grid Panel (Left) -->
    <div class="menu-panel">
        <div class="menu-filter-bar">
            <!-- Search Box -->
            <div class="search-wrapper">
                <i class="ti ti-search"></i>
                <input type="text" id="search-menu" class="search-input" placeholder="Cari menu kopi, snack, dll...">
            </div>
            
            <!-- Category Tabs -->
            <div class="categories-tab">
                <button class="tab-btn active" data-category="all">Semua</button>
                <button class="tab-btn" data-category="coffee">☕ Coffee</button>
                <button class="tab-btn" data-category="noncoffee">🍵 Non-Coffee</button>
                <button class="tab-btn" data-category="refreshment">🥤 Refreshment</button>
                <button class="tab-btn" data-category="snack">🍟 Snack</button>
            </div>
        </div>
        
        <div class="menu-grid-wrapper">
            <div class="menu-grid" id="menu-grid">
                @foreach($menus as $menu)
                    @php
                        $inCart = isset($rawCart[$menu->id]);
                        $outOfStock = $menu->stok <= 0;
                    @endphp
                    <div class="menu-card {{ $inCart ? 'selected' : '' }} {{ $outOfStock ? 'out-of-stock' : '' }}" 
                         data-id="{{ $menu->id }}" 
                         data-name="{{ strtolower($menu->nama_menu) }}" 
                         data-category="{{ $menu->kategori }}"
                         data-stock="{{ $menu->stok }}">
                        <div class="menu-card-emoji">{{ $menu->icon ?: '☕' }}</div>
                        <div class="menu-card-name">{{ $menu->nama_menu }}</div>
                        <div class="menu-card-price">{{ App\Helpers\FormatHelper::rupiah($menu->harga) }}</div>
                        <div class="menu-card-stock">Stok: <span class="stock-num">{{ $menu->stok }}</span></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Order Panel (Right) -->
    <div class="order-panel">
        <div class="order-header">
            <h3 class="order-title">Pesanan Baru</h3>
            <div class="order-trx-id" id="trx-id-display">Generating...</div>
        </div>
        
        <!-- Order Items -->
        <div class="order-items-wrapper" id="cart-items-container">
            @if(empty($cartItems))
                <div class="order-empty">
                    <i class="ti ti-shopping-cart-off"></i>
                    <p>Belum ada pesanan</p>
                </div>
            @else
                @foreach($cartItems as $item)
                    <div class="order-item" data-id="{{ $item['menu']->id }}">
                        <div class="order-item-info">
                            <div class="order-item-name">{{ $item['menu']->nama_menu }}</div>
                            <div class="order-item-price" data-price="{{ $item['menu']->harga }}">
                                {{ App\Helpers\FormatHelper::rupiah($item['menu']->harga) }}
                            </div>
                        </div>
                        <div class="order-item-actions">
                            <button class="qty-btn btn-minus" data-id="{{ $item['menu']->id }}">−</button>
                            <span class="qty-num">{{ $item['jumlah'] }}</span>
                            <button class="qty-btn btn-plus" data-id="{{ $item['menu']->id }}">+</button>
                            <button class="remove-item-btn" data-id="{{ $item['menu']->id }}"><i class="ti ti-trash"></i></button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        
        <!-- Order Footer / Checkout -->
        <div class="order-footer">
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="summary-subtotal">{{ App\Helpers\FormatHelper::rupiah($subtotal) }}</span>
            </div>
            <div class="summary-row">
                <span>Pajak (10%)</span>
                <span id="summary-pajak">{{ App\Helpers\FormatHelper::rupiah($pajak) }}</span>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span id="summary-total" data-value="{{ $total }}">{{ App\Helpers\FormatHelper::rupiah($total) }}</span>
            </div>
            
            <div class="payment-input-group">
                <label for="cash-received">Uang Bayar</label>
                <input type="number" id="cash-received" class="payment-control" placeholder="Rp 0" min="0">
            </div>
            
            <div class="change-row">
                <span>Kembalian</span>
                <span class="change-amount" id="change-display">Rp 0</span>
            </div>
            
            <button id="btn-submit-order" class="btn-checkout" {{ empty($cartItems) ? 'disabled' : '' }}>
                <i class="ti ti-circle-check"></i> Simpan & Cetak Struk
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Seed some initial variables needed by our JS
        window.posConfig = {
            storeUrl: "{{ route('transaction.store') }}",
            cartAddUrl: "{{ route('cart.add') }}",
            cartUpdateUrl: "{{ route('cart.update') }}",
            cartRemoveUrl: "{{ route('cart.remove') }}",
            token: "{{ csrf_token() }}",
            emptyCart: {{ empty($cartItems) ? 'true' : 'false' }}
        };
    </script>
    <script src="{{ asset('js/transaksi.js') }}"></script>
@endpush
