<?php
/**
 * Impor foto dari folder Google Drive publik MTsN 1 Wonosobo.
 *
 * Jalankan:  php scripts/import-drive-photos.php
 * Opsi:      php scripts/import-drive-photos.php --only=voli,osim --force
 *
 * Hasil:
 *  - public/images/galeri/<album>/NN.webp
 *  - public/images/galeri/manifest.json
 *  - public/images/hero/*.webp
 *  - public/images/ekstra/<slug>.webp (banner ekskul yang punya folder Drive)
 */

declare(strict_types=1);

set_time_limit(0);
ini_set('memory_limit', '768M');

const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36';
const MAX_PHOTOS = 6;

$root = dirname(__DIR__);
$public = $root . '/public';
$galleryDir = $public . '/images/galeri';
$heroDir = $public . '/images/hero';
$ekstraDir = $public . '/images/ekstra';

$only = null;
$force = in_array('--force', $argv, true);
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--only=')) {
        $only = array_filter(array_map('trim', explode(',', substr($arg, 7))));
    }
}

/* ------------------------------------------------------------------ */
/* Konfigurasi album                                                   */
/* ------------------------------------------------------------------ */

$groups = [
    [
        'key'    => 'kegiatan',
        'label'  => 'Kegiatan',
        'folder' => '1DrG8bxRMtG69UY40uiPALUw3-3UiVSLH',
        'mode'   => 'subfolders',
        'albums' => [
            'BACA AL QURAN'               => ['slug' => 'baca-al-quran', 'judul' => 'Baca Al-Quran'],
            'PAK KAMAD'                   => ['slug' => 'pak-kamad', 'judul' => 'Pak Kamad'],
            'PEMBELAJARAN ERA DIGITALISASI' => ['slug' => 'pembelajaran-era-digitalisasi', 'judul' => 'Pembelajaran Era Digitalisasi'],
            'SHOLAT BERJAMAAH'            => ['slug' => 'sholat-berjamaah', 'judul' => 'Sholat Berjamaah'],
        ],
    ],
    [
        'key'    => 'guru-organisasi',
        'label'  => 'Guru & Organisasi',
        'folder' => '1ra0qhvaKDz7pXss0I21N9o2Q6JBhlGQ7',
        'mode'   => 'subfolders',
        'albums' => [
            'FOTO BERSAMA GURU & STAFF' => ['slug' => 'foto-guru-staff', 'judul' => 'Foto Guru & Staff'],
            'OSIM'                      => ['slug' => 'osim', 'judul' => 'OSIM', 'ekskul' => 'osis'],
            'PAK KAMAD & WAKA'          => ['slug' => 'pak-kamad-waka', 'judul' => 'Pak Kamad & Waka'],
            'PRAMUKA'                   => ['slug' => 'pramuka', 'judul' => 'Pramuka', 'ekskul' => 'pramuka'],
        ],
    ],
    [
        'key'    => 'ekstrakurikuler',
        'label'  => 'Ekstrakurikuler',
        'folder' => '17o2zwaSoZ1MRD6S56FyiO3ddWGDNElR0',
        'mode'   => 'subfolders',
        'albums' => [
            'ADIWIYATA'       => ['slug' => 'adiwiyata', 'judul' => 'Adiwiyata'],
            'ATLETIK'         => ['slug' => 'atletik', 'judul' => 'Atletik', 'ekskul' => 'atletik'],
            'B. INGGRIS'      => ['slug' => 'b-inggris', 'judul' => 'B. Inggris'],
            'BULU TANGKIS'    => ['slug' => 'bulu-tangkis', 'judul' => 'Bulu Tangkis'],
            'FUTSAL'          => ['slug' => 'futsal', 'judul' => 'Futsal'],
            'IPA'             => ['slug' => 'ipa', 'judul' => 'IPA'],
            'MATEMATIKA'      => ['slug' => 'matematika', 'judul' => 'Matematika'],
            'MUAITHAI'        => ['slug' => 'muai-thai', 'judul' => 'Muai Thai'],
            'PMR'             => ['slug' => 'pmr', 'judul' => 'PMR'],
            'PUSTAKAWAN MUDA' => ['slug' => 'pustakawan-muda', 'judul' => 'Pustakawan Muda'],
            "SILAT'"          => ['slug' => 'pencak-silat', 'judul' => 'Pencak Silat', 'ekskul' => 'pencak-silat'],
            'TAEKWONDO'       => ['slug' => 'taekwondo', 'judul' => 'Taekwondo'],
            'TAKRAW'          => ['slug' => 'takraw', 'judul' => 'Takraw'],
            'TARI'            => ['slug' => 'tari', 'judul' => 'Tari'],
            'TENIS MEJA'      => ['slug' => 'tenis-meja', 'judul' => 'Tenis Meja'],
            'VOKAL'           => ['slug' => 'vokal', 'judul' => 'Vokal'],
            'VOLY'            => ['slug' => 'voli', 'judul' => 'Voli', 'ekskul' => 'voli'],
            'WUSHU'           => ['slug' => 'wushu', 'judul' => 'Wushu'],
        ],
    ],
    [
        'key'    => 'unit',
        'label'  => 'Unit Kerja',
        'folder' => null,
        'mode'   => 'single',
        'albums' => [
            ['slug' => 'humas', 'judul' => 'Humas', 'folder' => '1Mwv_D65wA4y5ynuqd1kN4BC5jyPimZtS'],
            ['slug' => 'kesiswaan', 'judul' => 'Kesiswaan', 'folder' => '1QqCPkFA8RCG9iUaM0oiokaJhn-VKJsCr'],
            ['slug' => 'kurikulum', 'judul' => 'Kurikulum', 'folder' => '1DkfTXpadOryPhk3frSez0RdM-WLN_RjT'],
            ['slug' => 'sarpras', 'judul' => 'Sarpras', 'folder' => '1997EWnEZp2uMQBSU_3jJ_F-CFLcyHzjM'],
        ],
    ],
];

$heroSources = [
    ['key' => 'beranda', 'album' => 'pembelajaran-era-digitalisasi', 'index' => 1, 'w' => 1400, 'h' => 1050],
    ['key' => 'ekstrakurikuler', 'album' => 'voli', 'index' => 0, 'w' => 1920, 'h' => 900],
    ['key' => 'kesiswaan', 'album' => 'osim', 'index' => 0, 'w' => 1920, 'h' => 900],
    ['key' => 'akademik', 'album' => 'pembelajaran-era-digitalisasi', 'index' => 0, 'w' => 1920, 'h' => 900],
    ['key' => 'galeri', 'album' => 'pembelajaran-era-digitalisasi', 'index' => 2, 'w' => 1920, 'h' => 900],
];

/* ------------------------------------------------------------------ */
/* Helper                                                              */
/* ------------------------------------------------------------------ */

function http_get(string $url): ?string
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_USERAGENT      => USER_AGENT,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($body === false || $code >= 400) {
        return null;
    }

    return $body;
}

/** Ambil daftar item (file/folder) dari halaman folder Drive publik. */
function drive_list(string $folderId): array
{
    $html = http_get('https://drive.google.com/drive/folders/' . $folderId);
    if ($html === null) {
        return [];
    }

    $needle = "window['_DRIVE_ivd'] = '";
    $pos = strpos($html, $needle);
    if ($pos === false) {
        return [];
    }

    $i = $pos + strlen($needle);
    $len = strlen($html);
    $raw = '';
    while ($i < $len) {
        $c = $html[$i];
        if ($c === '\\') {
            $raw .= $c . ($html[$i + 1] ?? '');
            $i += 2;
            continue;
        }
        if ($c === "'") {
            break;
        }
        $raw .= $c;
        $i++;
    }

    $decoded = json_decode(stripcslashes($raw), true);
    if (!is_array($decoded) || !isset($decoded[0]) || !is_array($decoded[0])) {
        return [];
    }

    $items = [];
    foreach ($decoded[0] as $row) {
        if (!isset($row[0], $row[2], $row[3])) {
            continue;
        }
        $items[] = [
            'id'   => (string) $row[0],
            'name' => (string) $row[2],
            'mime' => (string) $row[3],
        ];
    }

    return $items;
}

function is_image(array $item): bool
{
    if (str_starts_with($item['mime'], 'image/')) {
        return true;
    }

    return (bool) preg_match('/\.(jpe?g|png|webp)$/i', $item['name']);
}

function drive_download(string $id): ?string
{
    $body = http_get('https://drive.google.com/uc?export=download&id=' . $id);
    if ($body === null) {
        return null;
    }

    $head = substr($body, 0, 16);
    $isBinary = str_starts_with($head, "\xFF\xD8")
        || str_starts_with($head, "\x89PNG")
        || str_starts_with($head, 'RIFF');

    if ($isBinary) {
        return $body;
    }

    if (preg_match('/name="confirm" value="([^"]+)"/', $body, $m)) {
        $url = 'https://drive.usercontent.google.com/download?id=' . $id . '&export=download&confirm=' . urlencode($m[1]);
        $retry = http_get($url);
        if ($retry !== null && !str_starts_with(substr($retry, 0, 16), '<')) {
            return $retry;
        }
    }

    return null;
}

/** Pilih maksimal N file gambar secara merata dari daftar terurut. */
function pick_evenly(array $files, int $max): array
{
    usort($files, fn ($a, $b) => strnatcasecmp($a['name'], $b['name']));
    $n = count($files);
    if ($n <= $max) {
        return $files;
    }

    $picked = [];
    for ($i = 0; $i < $max; $i++) {
        $idx = (int) floor($i * ($n - 1) / ($max - 1));
        $picked[$idx] = $files[$idx];
    }

    return array_values($picked);
}

function load_gd(string $binary)
{
    $tmp = tempnam(sys_get_temp_dir(), 'mtsn-img-');
    if ($tmp === false) {
        return false;
    }
    file_put_contents($tmp, $binary);
    $exif = @exif_read_data($tmp);
    @unlink($tmp);

    $im = @imagecreatefromstring($binary);
    if ($im === false) {
        return false;
    }

    $orientation = (int) ($exif['Orientation'] ?? 1);
    if ($orientation === 3) {
        $im = imagerotate($im, 180, 0);
    } elseif ($orientation === 6) {
        $im = imagerotate($im, -90, 0);
    } elseif ($orientation === 8) {
        $im = imagerotate($im, 90, 0);
    }

    return $im;
}

/** Simpan dengan aspect ratio tetap, sisi terpanjang <= $maxDim. */
function save_scaled($im, string $dest, int $maxDim, int $quality = 82): bool
{
    $sw = imagesx($im);
    $sh = imagesy($im);
    $scale = min(1, $maxDim / max($sw, $sh));
    $w = max(1, (int) round($sw * $scale));
    $h = max(1, (int) round($sh * $scale));

    $dst = imagecreatetruecolor($w, $h);
    imagecopyresampled($dst, $im, 0, 0, 0, 0, $w, $h, $sw, $sh);
    $ok = imagewebp($dst, $dest, $quality);
    imagedestroy($dst);

    return $ok;
}

/** Crop tengah ke rasio tertentu lalu simpan tepat $outW x $outH. */
function save_cover($im, string $dest, int $outW, int $outH, int $quality = 82): bool
{
    $sw = imagesx($im);
    $sh = imagesy($im);
    $targetRatio = $outW / $outH;
    $srcRatio = $sw / $sh;

    if ($srcRatio > $targetRatio) {
        $cropH = $sh;
        $cropW = (int) round($sh * $targetRatio);
        $srcX = (int) (($sw - $cropW) / 2);
        $srcY = 0;
    } else {
        $cropW = $sw;
        $cropH = (int) round($sw / $targetRatio);
        $srcX = 0;
        $srcY = (int) (($sh - $cropH) / 2);
    }

    $dst = imagecreatetruecolor($outW, $outH);
    imagecopyresampled($dst, $im, 0, 0, $srcX, $srcY, $outW, $outH, $cropW, $cropH);
    $ok = imagewebp($dst, $dest, $quality);
    imagedestroy($dst);

    return $ok;
}

function ensure_dir(string $dir): void
{
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

function rrmdir_files(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }
    foreach (glob($dir . '/*.webp') ?: [] as $file) {
        @unlink($file);
    }
}

/* ------------------------------------------------------------------ */
/* Eksekusi                                                            */
/* ------------------------------------------------------------------ */

ensure_dir($galleryDir);
ensure_dir($heroDir);

$manifestAlbums = [];
$albumSources = []; // slug => daftar file id terpilih

foreach ($groups as $group) {
    $albums = [];

    if ($group['mode'] === 'subfolders') {
        echo "\n== {$group['label']} ==\n";
        $subfolders = drive_list($group['folder']);
        $byName = [];
        foreach ($subfolders as $sf) {
            if (str_contains($sf['mime'], 'folder')) {
                $byName[strtoupper(trim($sf['name']))] = $sf;
            }
        }

        foreach ($group['albums'] as $folderName => $meta) {
            $key = strtoupper(trim($folderName));
            if (!isset($byName[$key])) {
                echo "  - lewati (folder tidak ditemukan): {$folderName}\n";
                continue;
            }
            $meta['folder'] = $byName[$key]['id'];
            $albums[] = $meta;
        }
    } else {
        echo "\n== {$group['label']} ==\n";
        $albums = $group['albums'];
    }

    foreach ($albums as $album) {
        $slug = $album['slug'];

        if ($only !== null && !in_array($slug, $only, true)) {
            continue;
        }

        $outDir = $galleryDir . '/' . $slug;
        $existing = glob($outDir . '/*.webp') ?: [];

        if (!$force && count($existing) > 0) {
            echo "  = {$slug}: sudah ada (" . count($existing) . " foto), dilewati\n";
            $albumSources[$slug] = array_map(fn ($f) => basename($f, '.webp'), $existing);
            continue;
        }

        $items = drive_list($album['folder']);
        $files = array_values(array_filter($items, fn ($it) => is_image($it) && !str_contains($it['mime'], 'folder')));

        if (count($files) === 0) {
            echo "  - {$slug}: tidak ada foto, dilewati\n";
            continue;
        }

        $picked = pick_evenly($files, MAX_PHOTOS);
        ensure_dir($outDir);
        rrmdir_files($outDir);

        echo "  + {$slug}: {$album['judul']} (" . count($picked) . " dari " . count($files) . " foto)\n";

        $saved = [];
        $firstImage = null;

        foreach ($picked as $i => $file) {
            $bin = drive_download($file['id']);
            if ($bin === null) {
                echo "      ! gagal unduh {$file['name']}\n";
                continue;
            }

            $im = load_gd($bin);
            if ($im === false) {
                echo "      ! bukan gambar valid {$file['name']}\n";
                continue;
            }

            $target = sprintf('%s/%02d.webp', $outDir, $i + 1);
            save_scaled($im, $target, 1600, 82);
            $saved[] = $target;

            if ($firstImage === null) {
                $firstImage = $im;
            } else {
                imagedestroy($im);
            }

            echo "      ok {$file['name']} -> " . basename($target) . "\n";
        }

        if (count($saved) === 0) {
            continue;
        }

        // Banner ekskul (16:9) dari foto pertama.
        if (!empty($album['ekskul'])) {
            $ekstraTarget = $ekstraDir . '/' . $album['ekskul'] . '.webp';
            save_cover($firstImage, $ekstraTarget, 1280, 720, 82);
            echo "      banner -> images/ekstra/{$album['ekskul']}.webp\n";
        }

        if ($firstImage !== false) {
            imagedestroy($firstImage);
        }

        $photoUrls = array_map(
            fn ($path) => '/images/galeri/' . $slug . '/' . basename($path),
            $saved
        );

        $manifestAlbums[] = [
            'slug'   => $slug,
            'judul'  => $album['judul'],
            'group'  => $group['key'],
            'cover'  => $photoUrls[0],
            'count'  => count($photoUrls),
            'photos' => $photoUrls,
        ];

        $albumSources[$slug] = array_map(fn ($p) => basename($p, '.webp'), $saved);
    }
}

/* ------------------------------------------------------------------ */
/* Manifest — pindai ulang supaya hasil run parsial tetap lengkap      */
/* ------------------------------------------------------------------ */

$allAlbums = [];
foreach ($groups as $group) {
    $list = $group['mode'] === 'subfolders'
        ? array_values($group['albums'])
        : $group['albums'];

    foreach ($list as $meta) {
        $slug = $meta['slug'];
        $dir = $galleryDir . '/' . $slug;
        $files = glob($dir . '/*.webp') ?: [];
        if (count($files) === 0) {
            continue;
        }
        sort($files, SORT_NATURAL);
        $urls = array_map(fn ($p) => '/images/galeri/' . $slug . '/' . basename($p), $files);
        $allAlbums[] = [
            'slug'   => $slug,
            'judul'  => $meta['judul'],
            'group'  => $group['key'],
            'cover'  => $urls[0],
            'count'  => count($urls),
            'photos' => $urls,
        ];
    }
}

$manifest = [
    'generated_at' => date('c'),
    'groups'       => array_map(fn ($g) => ['key' => $g['key'], 'label' => $g['label']], $groups),
    'albums'       => $allAlbums,
];

file_put_contents(
    $galleryDir . '/manifest.json',
    json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
);

echo "\nManifest: " . count($allAlbums) . " album -> public/images/galeri/manifest.json\n";

/* ------------------------------------------------------------------ */
/* Hero — dibuat ulang dari foto album sumber                          */
/* ------------------------------------------------------------------ */

echo "\n== Hero ==\n";
foreach ($heroSources as $hero) {
    $slug = $hero['album'];
    $dir = $galleryDir . '/' . $slug;
    $files = glob($dir . '/*.webp') ?: [];
    sort($files, SORT_NATURAL);

    if (count($files) === 0) {
        echo "  - {$hero['key']}: album {$slug} belum ada, dilewati\n";
        continue;
    }

    $file = $files[min($hero['index'], count($files) - 1)];
    $bin = file_get_contents($file);
    $im = $bin !== false ? @imagecreatefromstring($bin) : false;
    if ($im === false) {
        echo "  - {$hero['key']}: gagal baca {$file}\n";
        continue;
    }

    $dest = $heroDir . '/' . $hero['key'] . '.webp';
    save_cover($im, $dest, $hero['w'], $hero['h'], 84);
    imagedestroy($im);
    echo "  + {$hero['key']}.webp <- {$slug}/" . basename($file) . "\n";
}

echo "\nSelesai.\n";
