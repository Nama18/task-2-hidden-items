# Hidden Item Game

Program CLI PHP untuk mencari kemungkinan lokasi item tersembunyi berdasarkan pola pergerakan pemain pada sebuah grid.

## Deskripsi

Pemain memulai dari posisi `X` pada grid.

Pergerakan dilakukan dengan urutan:

1. Bergerak ke **Utara (Up)** sebanyak `A` langkah
2. Bergerak ke **Timur (Right)** sebanyak `B` langkah
3. Bergerak ke **Selatan (Down)** sebanyak `C` langkah

Karena jumlah langkah sebenarnya dapat berada dalam rentang `1` hingga nilai maksimum yang diberikan (`A`, `B`, dan `C`), program akan mencoba seluruh kombinasi langkah yang memungkinkan dan menampilkan semua lokasi item yang valid.

## Struktur Grid

```text
########
#......#
#.###..#
#...#.##
#X#....#
########
```

Keterangan:

- `#` = Obstacle / tembok
- `.` = Jalur yang bisa dilewati
- `X` = Posisi awal pemain
- `$` = Kemungkinan lokasi item (hasil output)

---

## Requirements

- PHP 8.0 atau lebih baru

Cek versi PHP:

```bash
php -v
```

---

## Cara Menjalankan

Format:

```bash
php hidden_item.php <A> <B> <C>
```

Keterangan:

| Parameter | Deskripsi                   |
| --------- | --------------------------- |
| A         | Maksimum langkah ke Utara   |
| B         | Maksimum langkah ke Timur   |
| C         | Maksimum langkah ke Selatan |

### Contoh

```bash
php hidden_item.php 3 2 1
```

---

## Contoh Output

```text
=== Hidden Item Game ===
Posisi awal player (X): baris=4, kolom=1
Navigasi: Utara 3 langkah → Timur 2 langkah → Selatan 1 langkah

Probable item locations (2 titik):
  1. (baris=2, kolom=1)
  2. (baris=3, kolom=2)

Grid ($ = probable item location):
########
#......#
#$###..#
#.$.#.##
#X#....#
########
```

> Output dapat berbeda tergantung kombinasi nilai `A`, `B`, dan `C`.

---

## Cara Kerja Program

1. Mencari posisi awal pemain (`X`).
2. Mencoba seluruh kombinasi langkah:
   - `1..A` langkah ke Utara
   - `1..B` langkah ke Timur
   - `1..C` langkah ke Selatan
3. Memastikan setiap langkah:
   - Tidak keluar dari batas grid
   - Tidak menabrak obstacle (`#`)
4. Menyimpan semua posisi akhir yang valid.
5. Menampilkan:
   - Daftar koordinat kemungkinan lokasi item
   - Visualisasi grid dengan marker `$`

---

## File

```text
hidden_item.php
README.md
```

## Author

Created for Hidden Item Game Assessment.
