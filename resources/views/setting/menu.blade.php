@extends('layouts.app')

@section('title', 'Kelola Menu')
@section('page-title', 'Kelola Menu')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/setting.css') }}">
    <link rel="stylesheet" href="{{ asset('css/history.css') }}"> {{-- Reuse table styles --}}
@endpush

@section('content')
<div class="settings-container">
    <div class="menu-header-actions">
        <h3 class="card-title" style="font-size: 15px;"><i class="ti ti-tools-kitchen-2"></i> Daftar Menu Coffee Shop</h3>
        <button class="btn btn-primary" onclick="openAddModal()">
            <i class="ti ti-plus"></i> Tambah Menu
        </button>
    </div>

    <!-- Table of Menus -->
    <div class="table-card">
        <div class="table-wrapper">
            <table class="history-table">
                <thead>
                    <tr>
                        <th style="width: 80px; text-align: center;">Icon</th>
                        <th>Nama Menu</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th style="text-align: center;">Stok</th>
                        <th style="text-align: center; width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menus as $menu)
                        <tr>
                            <td style="font-size: 24px; text-align: center; line-height: 1;">{{ $menu->icon ?: '☕' }}</td>
                            <td style="font-weight: 600;">{{ $menu->nama_menu }}</td>
                            <td>
                                @if($menu->kategori == 'coffee')
                                    ☕ Coffee
                                @elseif($menu->kategori == 'noncoffee')
                                    🍵 Non-Coffee
                                @elseif($menu->kategori == 'refreshment')
                                    🥤 Refreshment
                                @else
                                    🍟 Snack
                                @endif
                            </td>
                            <td style="font-weight: 600; color: var(--coffee);">{{ App\Helpers\FormatHelper::rupiah($menu->harga) }}</td>
                            <td style="text-align: center; font-weight: 500;">{{ $menu->stok }}</td>
                            <td>
                                <div class="crud-actions">
                                    <button class="btn-icon btn-icon-edit" title="Edit Menu"
                                            onclick="openEditModal({{ json_encode($menu) }})">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    
                                    <form action="{{ route('setting.menu.destroy', $menu->id) }}" method="POST" 
                                          style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-icon-delete" title="Hapus Menu">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-tertiary); padding: 32px 20px;">
                                <i class="ti ti-tools-kitchen-off" style="font-size: 36px; display: block; margin-bottom: 8px;"></i>
                                Belum ada data menu. Klik Tambah Menu untuk membuat menu baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal" id="add-menu-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Tambah Menu Baru</h4>
            <button class="modal-close" onclick="closeAddModal()">&times;</button>
        </div>
        <form action="{{ route('setting.menu.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="add_nama_menu">Nama Menu</label>
                    <input type="text" id="add_nama_menu" name="nama_menu" class="form-control" required placeholder="Contoh: Caramel Latte">
                </div>
                
                <div class="form-group">
                    <label for="add_kategori">Kategori</label>
                    <select id="add_kategori" name="kategori" class="form-control" style="-webkit-appearance: none; appearance: none; background: url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2216%22 height=%2216%22 fill=%22%23666%22><path d=%22M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z%22/></svg>') no-repeat right 12px center; background-size: 12px; padding-right: 32px;" required>
                        <option value="coffee">☕ Coffee</option>
                        <option value="noncoffee">🍵 Non-Coffee</option>
                        <option value="refreshment">🥤 Refreshment</option>
                        <option value="snack">🍟 Snack</option>
                    </select>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label for="add_harga">Harga (Rupiah)</label>
                        <input type="number" id="add_harga" name="harga" class="form-control" min="0" required placeholder="25000">
                    </div>
                    <div class="form-group">
                        <label for="add_stok">Stok Awal</label>
                        <input type="number" id="add_stok" name="stok" class="form-control" min="0" required placeholder="50">
                    </div>
                </div>

                <div class="form-group">
                    <label>Pilih Icon Emoji</label>
                    <input type="hidden" name="icon" id="add_icon_val" value="☕">
                    <div class="emoji-select" id="add-emoji-grid">
                        @foreach(['☕','🍵','🥤','🍟','🥐','🍰','🍩','🍔'] as $emoji)
                            <div class="emoji-option {{ $emoji === '☕' ? 'selected' : '' }}" 
                                 onclick="selectEmoji('add', '{{ $emoji }}', this)">
                                {{ $emoji }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Menu</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal" id="edit-menu-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Edit Detail Menu</h4>
            <button class="modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="edit-menu-form" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_nama_menu">Nama Menu</label>
                    <input type="text" id="edit_nama_menu" name="nama_menu" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_kategori">Kategori</label>
                    <select id="edit_kategori" name="kategori" class="form-control" style="-webkit-appearance: none; appearance: none; background: url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2216%22 height=%2216%22 fill=%22%23666%22><path d=%22M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z%22/></svg>') no-repeat right 12px center; background-size: 12px; padding-right: 32px;" required>
                        <option value="coffee">☕ Coffee</option>
                        <option value="noncoffee">🍵 Non-Coffee</option>
                        <option value="refreshment">🥤 Refreshment</option>
                        <option value="snack">🍟 Snack</option>
                    </select>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label for="edit_harga">Harga (Rupiah)</label>
                        <input type="number" id="edit_harga" name="harga" class="form-control" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_stok">Stok</label>
                        <input type="number" id="edit_stok" name="stok" class="form-control" min="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Pilih Icon Emoji</label>
                    <input type="hidden" name="icon" id="edit_icon_val" value="☕">
                    <div class="emoji-select" id="edit-emoji-grid">
                        @foreach(['☕','🍵','🥤','🍟','🥐','🍰','🍩','🍔'] as $emoji)
                            <div class="emoji-option" data-emoji="{{ $emoji }}"
                                 onclick="selectEmoji('edit', '{{ $emoji }}', this)">
                                {{ $emoji }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const addModal = document.getElementById('add-menu-modal');
    const editModal = document.getElementById('edit-menu-modal');
    const editForm = document.getElementById('edit-menu-form');

    function openAddModal() {
        addModal.classList.add('active');
    }

    function closeAddModal() {
        addModal.classList.remove('active');
    }

    function openEditModal(menu) {
        // Set action URL dynamically
        editForm.action = `/setting/menu/${menu.id}`;
        
        // Populate inputs
        document.getElementById('edit_nama_menu').value = menu.nama_menu;
        document.getElementById('edit_kategori').value = menu.kategori;
        document.getElementById('edit_harga').value = Math.round(menu.harga);
        document.getElementById('edit_stok').value = menu.stok;
        document.getElementById('edit_icon_val').value = menu.icon || '☕';
        
        // Highlight active emoji in edit modal
        const options = document.querySelectorAll('#edit-emoji-grid .emoji-option');
        options.forEach(opt => {
            opt.classList.remove('selected');
            if (opt.getAttribute('data-emoji') === (menu.icon || '☕')) {
                opt.classList.add('selected');
            }
        });

        editModal.classList.add('active');
    }

    function closeEditModal() {
        editModal.classList.remove('active');
    }

    function selectEmoji(formType, emoji, element) {
        const gridId = formType === 'add' ? 'add-emoji-grid' : 'edit-emoji-grid';
        const inputId = formType === 'add' ? 'add_icon_val' : 'edit_icon_val';
        
        const options = document.querySelectorAll(`#${gridId} .emoji-option`);
        options.forEach(opt => opt.classList.remove('selected'));
        
        element.classList.add('selected');
        document.getElementById(inputId).value = emoji;
    }

    // Close modals on clicking outside content area
    window.addEventListener('click', function(e) {
        if (e.target === addModal) closeAddModal();
        if (e.target === editModal) closeEditModal();
    });
</script>
@endpush
