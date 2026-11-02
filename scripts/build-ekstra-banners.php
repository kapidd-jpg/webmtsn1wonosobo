<?php
/**
 * Buat banner 16:9 untuk ekstrakurikuler dari cover album galeri.
 *
 * Jalankan: php scripts/build-ekstra-banners.php
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$manifestPath = $root . '/public/images/galeri/manifest.json';
$ekstraDir = $root . '/public/images/ekstra';

if (!is_dir($ekstraDir)) {
    mkdir($ekstraDir, 0777, true);
}

$manifest = json_decode((string) file_get_contents($manifestPath), true);
if (!is_array($manifest) || empty($manifest['albums'])) {
    fwrite(STDERR, "Manifest galeri tidak ditemukan atau kosong.\n");
    exit(1);
}

/** album galeri => file banner ekskul */
$map = [
    'adiwiyata'       => 'adiwiyata',
    'atletik'         => 'atletik',
    'b-inggris'       => 'b-inggris',
    'bulu-tangkis'    => 'bulu-tangkis',
    'futsal'          => 'futsal',
    'ipa'             => 'ipa',
    'matematika'      => 'matematika',
    'muai-thai'       => 'muai-thai',
    'pmr'             => 'pmr',
    'pustakawan-muda' => 'pustakawan-muda',
    'pencak-silat'    => 'pencak-silat',
    'taekwondo'       => 'taekwondo',
    'takraw'          => 'takraw',
    'tari'            => 'tari',
    'tenis-meja'      => 'tenis-meja',
    'vokal'           => 'vokal',
    'voli'            => 'voli',
    'wushu'           => 'wushu',
    'osim'            => 'osis',
    'pramuka'         => 'pramuka',
];

function save_cover(string $src, string $dest, int $outW, int $outH, int $quality = 84): bool
{
    $im = @imagecreatefromstring((string) file_get_contents($src));
    if ($im === false) {
        return false;
    }

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
    imagedestroy($im);

    return $ok;
}

$count = 0;
foreach ($manifest['albums'] as $album) {
    $slug = $album['slug'];
    if (!isset($map[$slug]) || empty($album['cover'])) {
        continue;
    }

    $src = $root . '/public' . $album['cover'];
    $dest = $ekstraDir . '/' . $map[$slug] . '.webp';

    if (!is_file($src)) {
        echo "  - lewati {$slug}: cover tidak ada\n";
        continue;
    }

    if (save_cover($src, $dest, 1280, 720)) {
        echo "  + {$map[$slug]}.webp <- {$album['cover']}\n";
        $count++;
    } else {
        echo "  ! gagal membuat banner {$slug}\n";
    }
}

echo "\nSelesai: {$count} banner dibuat.\n";
