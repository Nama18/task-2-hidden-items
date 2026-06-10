<?php

/**
 * TASK 2: Hidden Item Game
 *
 * Cara pakai:
 *   php hidden_item.php <A> <B> <C>
 *
 * Contoh:
 *   php hidden_item.php 3 2 1
 *
 * Penjelasan parameter:
 *   A = jumlah langkah ke Utara (Up)
 *   B = jumlah langkah ke Timur (Right)
 *   C = jumlah langkah ke Selatan (Down)
 */

// ─── Konfigurasi Grid ────────────────────────────────────────────────────────

/**
 * Grid map sesuai dokumen soal.
 * Koordinat: [row][col], row 0 = atas.
 *
 * # = obstacle
 * . = clear path
 * X = posisi awal player
 */
$grid = [
    ['#','#','#','#','#','#','#','#'],
    ['#','.','.','.','.','.','.','#'],
    ['#','.','#','#','#','.','.','#'],
    ['#','.','.','.',  '#','.','#','#'],
    ['#','X','#','.','.','.','.',  '#'],
    ['#','#','#','#','#','#','#','#'],
];

// ─── Fungsi Bantu ─────────────────────────────────────────────────────────────

/**
 * Temukan posisi awal player (X) di grid.
 *
 * @param  array $grid
 * @return array{int, int}  [row, col]
 * @throws RuntimeException jika X tidak ditemukan
 */
function findStart(array $grid): array
{
    foreach ($grid as $r => $row) {
        foreach ($row as $c => $cell) {
            if ($cell === 'X') {
                return [$r, $c];
            }
        }
    }
    throw new \RuntimeException('Posisi awal (X) tidak ditemukan di grid.');
}

/**
 * Cek apakah koordinat [row, col] bisa dilalui (bukan obstacle, tidak out-of-bounds).
 *
 * @param  array $grid
 * @param  int   $row
 * @param  int   $col
 * @return bool
 */
function isWalkable(array $grid, int $row, int $col): bool
{
    $maxRow = count($grid) - 1;
    $maxCol = count($grid[0]) - 1;

    if ($row < 0 || $row > $maxRow || $col < 0 || $col > $maxCol) {
        return false;
    }

    return $grid[$row][$col] !== '#';
}

/**
 * Jalankan satu segmen pergerakan (bergerak step langkah ke arah tertentu).
 * Setiap posisi antara (intermediate) boleh dilalui.
 * Jika jalur terblokir obstacle sebelum mencapai step ke-N, jalur ini tidak valid.
 *
 * @param  array  $grid
 * @param  int    $startRow
 * @param  int    $startCol
 * @param  int    $dRow      delta row per langkah (-1=utara, +1=selatan)
 * @param  int    $dCol      delta col per langkah (-1=barat, +1=timur)
 * @param  int    $steps     jumlah langkah
 * @return array|null        [row, col] posisi akhir, atau null kalau terblokir
 */
function move(array $grid, int $startRow, int $startCol, int $dRow, int $dCol, int $steps): ?array
{
    $row = $startRow;
    $col = $startCol;

    for ($i = 0; $i < $steps; $i++) {
        $row += $dRow;
        $col += $dCol;

        if (!isWalkable($grid, $row, $col)) {
            return null; // terblokir
        }
    }

    return [$row, $col];
}

/**
 * Cari semua kemungkinan posisi item berdasarkan A, B, C langkah.
 *
 * Karena nilai A, B, C bisa berarti "sampai A langkah" (1 s/d A),
 * kita coba semua kombinasi langkah dari 1 s/d nilai masing-masing.
 * Posisi valid adalah yang bisa dilalui & merupakan clear path (.).
 *
 * @param  array $grid
 * @param  int   $startRow
 * @param  int   $startCol
 * @param  int   $A  max langkah ke Utara
 * @param  int   $B  max langkah ke Timur
 * @param  int   $C  max langkah ke Selatan
 * @return array     list of [row, col]
 */
function findProbableLocations(
    array $grid,
    int $startRow, int $startCol,
    int $A, int $B, int $C
): array {
    $results = [];

    // Coba semua kombinasi a (1..A), b (1..B), c (1..C)
    for ($a = 1; $a <= $A; $a++) {
        // Langkah 1: Utara (row berkurang)
        $afterNorth = move($grid, $startRow, $startCol, -1, 0, $a);
        if ($afterNorth === null) continue;

        for ($b = 1; $b <= $B; $b++) {
            // Langkah 2: Timur (col bertambah)
            $afterEast = move($grid, $afterNorth[0], $afterNorth[1], 0, 1, $b);
            if ($afterEast === null) continue;

            for ($c = 1; $c <= $C; $c++) {
                // Langkah 3: Selatan (row bertambah)
                $afterSouth = move($grid, $afterEast[0], $afterEast[1], 1, 0, $c);
                if ($afterSouth === null) continue;

                [$r, $col_] = $afterSouth;

                // Posisi harus clear path (.)
                if ($grid[$r][$col_] === '.') {
                    $key = "{$r},{$col_}";
                    $results[$key] = [$r, $col_]; // gunakan key agar unik
                }
            }
        }
    }

    return array_values($results);
}

/**
 * Render grid ke string, dengan probable item locations ditandai '$'.
 *
 * @param  array $grid
 * @param  array $probables  list of [row, col]
 * @return string
 */
function renderGrid(array $grid, array $probables): string
{
    // Buat set koordinat probable untuk lookup O(1)
    $probableSet = [];
    foreach ($probables as [$r, $c]) {
        $probableSet["{$r},{$c}"] = true;
    }

    $output = '';
    foreach ($grid as $r => $row) {
        foreach ($row as $c => $cell) {
            if (isset($probableSet["{$r},{$c}"])) {
                $output .= '$'; // tandai lokasi probable
            } else {
                $output .= $cell;
            }
        }
        $output .= PHP_EOL;
    }

    return $output;
}

// ─── Main Program ─────────────────────────────────────────────────────────────

// Validasi argumen CLI
if ($argc !== 4) {
    echo "Usage: php hidden_item.php <A> <B> <C>\n";
    echo "  A = langkah ke Utara\n";
    echo "  B = langkah ke Timur\n";
    echo "  C = langkah ke Selatan\n";
    exit(1);
}

$A = (int) $argv[1];
$B = (int) $argv[2];
$C = (int) $argv[3];

if ($A < 1 || $B < 1 || $C < 1) {
    echo "Error: A, B, C harus >= 1.\n";
    exit(1);
}

// Cari posisi awal
[$startRow, $startCol] = findStart($grid);

echo "=== Hidden Item Game ===\n";
echo "Posisi awal player (X): baris={$startRow}, kolom={$startCol}\n";
echo "Navigasi: Utara {$A} langkah → Timur {$B} langkah → Selatan {$C} langkah\n\n";

// Cari semua probable locations
$probables = findProbableLocations($grid, $startRow, $startCol, $A, $B, $C);

// Output list koordinat
if (empty($probables)) {
    echo "Tidak ada lokasi probable yang ditemukan.\n";
} else {
    echo "Probable item locations (" . count($probables) . " titik):\n";
    foreach ($probables as $idx => [$r, $c]) {
        echo "  " . ($idx + 1) . ". (baris={$r}, kolom={$c})\n";
    }
}

// Bonus: tampilkan grid dengan marker '$'
echo "\nGrid ($ = probable item location):\n";
echo renderGrid($grid, $probables);