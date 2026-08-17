<?php
/**
 * Generator aset gambar MagangKu
 * - Logo perusahaan (SVG inisial + gradient)
 * - Cover event karier (SVG)
 * - Thumbnail artikel (SVG)
 * - App Icon PNG + Favicon PNG (GD)
 *
 * Jalankan: php scripts/generate-assets.php
 */

$base = dirname(__DIR__) . '/public/img';
@mkdir("$base/companies", 0777, true);
@mkdir("$base/events", 0777, true);
@mkdir("$base/articles", 0777, true);

/* ---------- Logo perusahaan ---------- */
$companies = [
    ['teknologi-nusantara', 'T', '#4FC3F7', '#0288D1'],
    ['digital-kreatif', 'D', '#26A69A', '#00695C'],
    ['bank-maju-bersama', 'B', '#5C6BC0', '#283593'],
    ['edukasi-pintar', 'E', '#FFA726', '#E65100'],
    ['karya-gemilang', 'K', '#66BB6A', '#2E7D32'],
    ['solusi-finansial', 'S', '#AB47BC', '#6A1B9A'],
    ['media-inovasi', 'M', '#EC407A', '#AD1457'],
    ['agro-lestari', 'A', '#8D6E63', '#4E342E'],
];

foreach ($companies as [$slug, $initial, $c1, $c2]) {
    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120" width="120" height="120">
  <defs>
    <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="$c1"/>
      <stop offset="100%" stop-color="$c2"/>
    </linearGradient>
  </defs>
  <rect x="4" y="4" width="112" height="112" rx="28" fill="url(#g)"/>
  <text x="60" y="78" text-anchor="middle" font-family="'Poppins','Montserrat','Segoe UI',Arial,sans-serif" font-size="52" font-weight="800" fill="#FFFFFF">$initial</text>
</svg>
SVG;
    file_put_contents("$base/companies/$slug.svg", $svg);
    echo "company: $slug\n";
}

/* ---------- Cover event karier ---------- */
$events = [
    ['webinar-karir', '💼', '#4FC3F7', '#0288D1'],
    ['jobfair-nasional', '🤝', '#5C6BC0', '#283593'],
    ['workshop-cv', '📝', '#FFA726', '#E65100'],
    ['campus-hiring', '🎓', '#26A69A', '#00695C'],
];

foreach ($events as [$slug, $emoji, $c1, $c2]) {
    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 420" width="800" height="420">
  <defs>
    <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="$c1"/>
      <stop offset="100%" stop-color="$c2"/>
    </linearGradient>
  </defs>
  <rect width="800" height="420" fill="url(#g)"/>
  <circle cx="700" cy="60" r="140" fill="#FFFFFF" opacity="0.12"/>
  <circle cx="90" cy="380" r="100" fill="#FFFFFF" opacity="0.10"/>
  <circle cx="420" cy="380" r="60" fill="#FFFFFF" opacity="0.08"/>
  <text x="400" y="250" text-anchor="middle" font-size="110">$emoji</text>
</svg>
SVG;
    file_put_contents("$base/events/$slug.svg", $emoji ? $svg : '');
    echo "event: $slug\n";
}

/* ---------- Thumbnail artikel ---------- */
$articles = [
    ['tips-cv-ats', '📄', '#4FC3F7', '#1E3A5F'],
    ['tips-interview', '🎤', '#5C6BC0', '#283593'],
    ['dunia-kerja', '🏢', '#26A69A', '#00695C'],
    ['tips-magang', '🧭', '#FFA726', '#E65100'],
    ['pengembangan-skill', '🚀', '#EC407A', '#AD1457'],
    ['fresh-graduate', '🎓', '#AB47BC', '#6A1B9A'],
];

foreach ($articles as [$slug, $emoji, $c1, $c2]) {
    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 360" width="640" height="360">
  <defs>
    <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="$c1"/>
      <stop offset="100%" stop-color="$c2"/>
    </linearGradient>
  </defs>
  <rect width="640" height="360" fill="url(#g)"/>
  <circle cx="560" cy="50" r="110" fill="#FFFFFF" opacity="0.12"/>
  <circle cx="60" cy="330" r="80" fill="#FFFFFF" opacity="0.10"/>
  <text x="320" y="205" text-anchor="middle" font-size="88">$emoji</text>
</svg>
SVG;
    file_put_contents("$base/articles/$slug.svg", $svg);
    echo "article: $slug\n";
}

/* ---------- App icon PNG (GD) ---------- */
if (function_exists('imagecreatetruecolor')) {
    makePngIcon(512, "$base/app-icon.png");
    makePngIcon(64, "$base/favicon.png");
    makePngIcon(192, "$base/icon-192.png");
    echo "PNG icons generated\n";
} else {
    echo "GD tidak tersedia, lewati PNG\n";
}

function makePngIcon(int $size, string $path): void
{
    $img = imagecreatetruecolor($size, $size);
    imagesavealpha($img, true);
    $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
    imagefill($img, 0, 0, $transparent);

    // Gradient background (rounded rect)
    $radius = (int) round($size * 0.22);
    $c1 = [79, 195, 247];   // #4FC3F7
    $c2 = [2, 136, 209];    // #0288D1
    for ($y = 0; $y < $size; $y++) {
        $t = $y / $size;
        $r = (int) ($c1[0] + ($c2[0] - $c1[0]) * $t);
        $g = (int) ($c1[1] + ($c2[1] - $c1[1]) * $t);
        $b = (int) ($c1[2] + ($c2[2] - $c1[2]) * $t);
        $color = imagecolorallocate($img, $r, $g, $b);
        for ($x = 0; $x < $size; $x++) {
            if (inRoundedRect($x, $y, $size, $radius)) {
                imagesetpixel($img, $x, $y, $color);
            }
        }
    }

    // White M + arrow
    $white = imagecolorallocate($img, 255, 255, 255);
    imagesetthickness($img, max(6, (int) round($size * 0.115)));
    $s = $size / 64;
    $x = fn ($v) => (int) round($v * $s);
    // Legs M
    imageline($img, $x(15), $x(50), $x(15), $x(26), $white);
    imageline($img, $x(15), $x(26), $x(32), $x(43), $white);
    imageline($img, $x(32), $x(43), $x(49), $x(26), $white);
    imageline($img, $x(49), $x(26), $x(49), $x(50), $white);
    // Arrow
    imageline($img, $x(32), $x(49), $x(32), $x(18), $white);
    imageline($img, $x(32), $x(18), $x(26), $x(25), $white);
    imageline($img, $x(32), $x(18), $x(38), $x(25), $white);

    imagepng($img, $path);
    imagedestroy($img);
}

function inRoundedRect(int $x, int $y, int $size, int $r): bool
{
    if ($x < $r && $y < $r) return dist($x, $y, $r, $r) <= $r * $r;
    if ($x >= $size - $r && $y < $r) return dist($x, $y, $size - $r - 1, $r) <= $r * $r;
    if ($x < $r && $y >= $size - $r) return dist($x, $y, $r, $size - $r - 1) <= $r * $r;
    if ($x >= $size - $r && $y >= $size - $r) return dist($x, $y, $size - $r - 1, $size - $r - 1) <= $r * $r;
    return true;
}

function dist(int $x, int $y, int $cx, int $cy): int
{
    return ($x - $cx) ** 2 + ($y - $cy) ** 2;
}
