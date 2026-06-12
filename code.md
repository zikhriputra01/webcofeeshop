# Code Guideline — Sistem Kasir Coffee Shop (Clean Code untuk web-artifacts-builder)

Panduan implementasi clean code saat menerapkan `design.md` ke project React + TypeScript + Tailwind + shadcn/ui menggunakan `init-artifact.sh`.

## 1. Struktur Folder

```
src/
├── types/
│   └── index.ts              # Tipe domain: Menu, CartItem, Transaction, User, Role
├── data/
│   └── mock-data.ts           # Data dummy menu & riwayat (pengganti DB sementara)
├── lib/
│   ├── format.ts              # fmt(), formatDate(), generateTrxId()
│   └── calculations.ts        # hitung subtotal, pajak, total, kembalian
├── hooks/
│   ├── useCart.ts              # state & logic keranjang
│   ├── useMenuFilter.ts        # state kategori + search + hasil filter
│   └── useAuth.ts              # state user login & role (mock)
├── components/
│   ├── layout/
│   │   ├── AppShell.tsx        # bungkus sidebar + topbar + content
│   │   ├── Sidebar.tsx
│   │   └── Topbar.tsx
│   ├── transaksi/
│   │   ├── CategoryTabs.tsx
│   │   ├── SearchBox.tsx
│   │   ├── MenuGrid.tsx
│   │   ├── MenuCard.tsx
│   │   ├── OrderPanel.tsx
│   │   ├── OrderItem.tsx
│   │   └── PaymentSummary.tsx
│   ├── riwayat/
│   │   ├── HistoryTable.tsx
│   │   └── HistorySummaryCards.tsx
│   ├── pengaturan/
│   │   ├── AccountSettingsCard.tsx
│   │   ├── StoreSettingsCard.tsx
│   │   └── MenuManagementCard.tsx
│   ├── struk/
│   │   └── ReceiptPrint.tsx
│   └── auth/
│       └── LoginForm.tsx
├── pages/
│   ├── LoginPage.tsx
│   ├── TransaksiPage.tsx
│   ├── HistoryPage.tsx
│   └── SettingPage.tsx
└── App.tsx                     # routing antar halaman + role guard
```

Prinsip: **1 komponen = 1 tanggung jawab**. Komponen presentational (UI murni, terima props) dipisah dari komponen/hook yang menyimpan state & logic.

## 2. Tipe Data (`types/index.ts`)

Selaraskan dengan skema tabel di PRD (§5):

```ts
export type Role = 'admin' | 'kasir';
export type MenuCategory = 'coffee' | 'noncoffee' | 'refreshment' | 'snack';

export interface User {
  id: number;
  nama: string;
  username: string;
  role: Role;
}

export interface MenuItem {
  id: number;
  nama_menu: string;
  kategori: MenuCategory;
  harga: number;
  stok: number;
  emoji: string; // representasi visual sementara
}

export interface CartItem {
  menu: MenuItem;
  jumlah: number;
}

export interface Transaction {
  id: string;            // format TRX-YYYYXXXXX
  kasir: string;
  waktu: string;
  items: CartItem[];
  subtotal: number;
  pajak: number;
  total: number;
  uang_bayar: number;
  kembalian: number;
  status: 'lunas';
}

export interface StoreInfo {
  nama_toko: string;
  alamat: string;
  telepon: string;
}
```

## 3. Fungsi Murni & Reusable (`lib/`)

Pisahkan semua kalkulasi & formatting dari komponen agar mudah ditest (clean code: pure functions, no side effects).

```ts
// lib/format.ts
export const PAJAK_RATE = 0.1;

export function formatRupiah(n: number): string {
  return 'Rp ' + n.toLocaleString('id-ID');
}

export function formatTanggalIndonesia(date: Date): string {
  return date.toLocaleDateString('id-ID', {
    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
  });
}

export function generateTrxId(date: Date, sequence: number): string {
  const year = date.getFullYear();
  return `TRX-${year}${String(sequence).padStart(4, '0')}`;
}
```

```ts
// lib/calculations.ts
import { CartItem } from '@/types';
import { PAJAK_RATE } from './format';

export function calcSubtotal(items: CartItem[]): number {
  return items.reduce((sum, item) => sum + item.menu.harga * item.jumlah, 0);
}

export function calcPajak(subtotal: number): number {
  return Math.round(subtotal * PAJAK_RATE);
}

export function calcTotal(subtotal: number, pajak: number): number {
  return subtotal + pajak;
}

export function calcKembalian(total: number, uangBayar: number): number {
  return Math.max(0, uangBayar - total);
}
```

Aturan: **jangan** hardcode `0.1` di banyak tempat (magic number) — gunakan konstanta `PAJAK_RATE`. Hindari duplikasi logic kalkulasi yang ada di mockup (`updateTotals`, `hitungKembalian`) — satukan di sini.

## 4. State Management dengan Hooks

### `useCart.ts`

```ts
export function useCart() {
  const [items, setItems] = useState<CartItem[]>([]);

  const addItem = (menu: MenuItem) => {
    setItems(prev => {
      const existing = prev.find(i => i.menu.id === menu.id);
      if (existing) {
        return prev.map(i =>
          i.menu.id === menu.id ? { ...i, jumlah: i.jumlah + 1 } : i
        );
      }
      return [...prev, { menu, jumlah: 1 }];
    });
  };

  const changeQty = (menuId: number, delta: number) => {
    setItems(prev =>
      prev
        .map(i => (i.menu.id === menuId ? { ...i, jumlah: i.jumlah + delta } : i))
        .filter(i => i.jumlah > 0)
    );
  };

  const removeItem = (menuId: number) =>
    setItems(prev => prev.filter(i => i.menu.id !== menuId));

  const clear = () => setItems([]);

  return { items, addItem, changeQty, removeItem, clear, isEmpty: items.length === 0 };
}
```

Hindari pola mockup yang memanggil `renderCart()` + `renderMenus()` manual di banyak fungsi — di React, state berubah → re-render otomatis. **Jangan** manipulasi DOM langsung (`document.getElementById`, `innerHTML`).

### `useMenuFilter.ts`

```ts
export function useMenuFilter(menus: MenuItem[]) {
  const [category, setCategory] = useState<'semua' | MenuCategory>('semua');
  const [query, setQuery] = useState('');

  const filtered = useMemo(() => {
    return menus.filter(m => {
      const matchCategory = category === 'semua' || m.kategori === category;
      const matchQuery = m.nama_menu.toLowerCase().includes(query.toLowerCase());
      return matchCategory && matchQuery;
    });
  }, [menus, category, query]);

  return { category, setCategory, query, setQuery, filtered };
}
```

Gunakan `useMemo` agar filtering tidak dihitung ulang setiap render tanpa perlu.

## 5. Validasi & Acceptance Criteria sebagai Guard

Implementasikan acceptance criteria PRD sebagai pengecekan eksplisit, bukan implisit:

```ts
// Sebelum simpan transaksi (FR-02 AC: "tidak bisa simpan jika keranjang kosong")
function handleSimpanTransaksi() {
  if (cart.isEmpty) return; // atau tampilkan toast peringatan
  if (kembalian < 0) return; // uang bayar belum cukup

  const trx = buildTransaction(cart.items, subtotal, pajak, total, uangBayar, kembalian, currentUser);
  saveTransaction(trx);
  openReceipt(trx);
  cart.clear();
}
```

- Login (FR-01): validasi field kosong + tampilkan pesan error spesifik dari hasil autentikasi (jangan generic "error").
- Pengaturan (FR-05): tombol "Simpan perubahan" memicu submit eksplisit (bukan auto-save tiap keystroke) — sesuai AC "tersimpan setelah klik Simpan, bukan real-time".
- Kelola Menu: hapus menu → tampilkan `Dialog` konfirmasi (shadcn) sebelum eksekusi; cegah hapus bila menu masih direferensikan transaksi aktif (cek di layer service/mock).

## 6. Role-based Rendering

```ts
// hooks/useAuth.ts
export function useAuth() {
  const [user, setUser] = useState<User | null>(null);
  const isAdmin = user?.role === 'admin';
  return { user, setUser, isAdmin };
}
```

Di `Sidebar.tsx` dan `SettingPage.tsx`, render item/sub-tab berdasarkan `isAdmin` — jangan duplikasi kondisi role di banyak file; idealnya buat helper `canAccess(role, feature)` di `lib/permissions.ts` jika kompleksitas bertambah.

## 7. Komponen Presentational — Contoh Konvensi

```tsx
// components/transaksi/MenuCard.tsx
interface MenuCardProps {
  menu: MenuItem;
  isSelected: boolean;
  onSelect: (menu: MenuItem) => void;
}

export function MenuCard({ menu, isSelected, onSelect }: MenuCardProps) {
  return (
    <button
      type="button"
      onClick={() => onSelect(menu)}
      className={cn(
        'rounded-md border p-3 text-left transition-colors hover:border-coffee-light',
        isSelected && 'border-coffee bg-coffee-pale'
      )}
    >
      <div className="mb-2 flex h-13 items-center justify-center rounded-md bg-surface-alt text-xl">
        {menu.emoji}
      </div>
      <p className="text-xs font-medium text-text-primary">{menu.nama_menu}</p>
      <p className="text-[11px] font-medium text-coffee">{formatRupiah(menu.harga)}</p>
      <p className="text-[10px] text-text-tertiary">Stok: {menu.stok}</p>
    </button>
  );
}
```

Konvensi:
- Props selalu di-tipekan dengan `interface ...Props`.
- Tidak ada logic kalkulasi di dalam JSX — panggil fungsi dari `lib/`.
- Gunakan `cn()` (utility shadcn) untuk conditional class, bukan template string manual.
- Nama event handler: `onX` untuk props, `handleX` untuk implementasi lokal.

## 8. Komponen Struk (Print)

- `ReceiptPrint.tsx` menerima `transaction: Transaction` dan `store: StoreInfo`, merender layout 2 kolom sesuai tabel di `design.md` §4.4.
- Gunakan CSS khusus print (`@media print` via Tailwind `print:` variants atau stylesheet terpisah) untuk lebar 58mm/80mm.
- Trigger: `window.print()` dipanggil dari handler tombol, dibungkus dalam komponen terpisah agar tidak mencampur logic cetak dengan halaman transaksi/riwayat (dipakai ulang di kedua halaman — FR-04 AC "cetak ulang identik").

## 9. Penamaan & Konsistensi Bahasa

- Konsisten gunakan istilah domain dalam Bahasa Indonesia sesuai PRD untuk nama field & label UI: `nama_menu`, `kategori`, `harga`, `stok`, `uang_bayar`, `kembalian`, `subtotal`, `pajak`, `total`.
- Nama file/komponen/fungsi tetap Bahasa Inggris standar (konvensi React), tapi label UI & domain terms boleh Bahasa Indonesia agar selaras PRD.

## 10. Checklist Sebelum Bundling

- [ ] Tidak ada `document.getElementById` / manipulasi DOM manual — semua via state React.
- [ ] Semua kalkulasi uang lewat `lib/calculations.ts`, format lewat `lib/format.ts`.
- [ ] Validasi keranjang kosong & uang bayar kurang sebelum simpan transaksi.
- [ ] Role admin vs kasir memengaruhi navigasi & akses Pengaturan.
- [ ] Komponen dipecah per tanggung jawab (lihat struktur folder §1), tidak ada file > ~150 baris untuk komponen UI.
- [ ] Warna & token mengikuti `design.md` §1 (tidak hardcode hex di banyak tempat — pakai Tailwind theme extend).
- [ ] Jalankan `bash scripts/bundle-artifact.sh` setelah semua halaman selesai, lalu tampilkan `bundle.html`.
