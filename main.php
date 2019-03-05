<?php
/**
 * Peter De Jong Attractor
 *
 * @author MengShaoying <mengshaoying@aliyun.com>
 */

function toBW($val)
{
    $color = intval(255 * $val);
    $color = $color < 0 ? 0 : $color;
    return [$color, $color, $color];
}

/**
 *
 */
function toRGB($val)
{
    $label = $val * 7;
    if ($label < 1) {
        $r = intval($label * 256);
        $g = 0;
        $b = 0;
    } elseif ($label < 2) {
        $r = 255;
        $g = intval(($label - 1) * 256);
        $b = 0;
    } elseif ($label < 3) {
        $r = 255 - intval(($label - 2) * 256);
        $g = 255;
        $b = 0;
    } elseif ($label < 4) {
        $r = 0;
        $g = 255;
        $b = intval(($label - 3) * 256);
    } elseif ($label < 5) {
        $r = 0;
        $g = 255 - intval(($label - 4) * 256);
        $b = 255;
    } elseif ($label < 6) {
        $r = intval(($label - 5) * 256);
        $g = 0;
        $b = 255;
    } else {
        $r = 255;
        $g = intval(($label - 6) * 256);
        $b = 255;
    }
    return [$r, $g, $b];
}

/**
 * Get next point of Peter De Jong attractor
 *
 * @param float $x
 * @param float $y
 * @param float $z
 * @param float $a
 * @param float $b
 * @param float $c
 * @param float $d
 * @param float $e
 * @param float $f
 * @return array
 */
function getNextPoint($x, $y, $a, $b, $c, $d)
{
    $xNext = sin($a * $y) - cos($b * $x);
    $yNext = sin($c * $x) - cos($d * $y);
    return [$xNext, $yNext];
}

/**
 * Create and save a image use GD
 */
class Image
{
    /** @var int image size */
    const SIZE = 512;
    /** @var string file location */
    private $path;
    /** @var resource a GD resource */
    private $gd;
    /** @var array the image matrix */
    private $matrix = [];
    /** @var int max value in matrix */
    private $max = 0;

    /**
     * Create a Image instance
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->gd = imagecreatetruecolor(self::SIZE, self::SIZE);
        imageantialias($this->gd, true);
        $this->path = $path;
        for ($i = 0; $i < self::SIZE; $i++) {
            $this->matrix[] = [];
            for ($j = 0; $j < self::SIZE; $j++) {
                $this->matrix[$i][$j] = 1;
            }
        }
    }

    /**
     * draw a point
     *
     * @param float $x
     * @param float $y
     * @param float $z
     */
    public function draw($x, $y)
    {
        $xIndex = intval(($x + 2) / 4 * self::SIZE);
        $xIndex = $xIndex < 0 ? 0 : ($xIndex >= self::SIZE ? self::SIZE - 1 : $xIndex);
        $yIndex = intval(($y + 2) / 4 * self::SIZE);
        $yIndex = $yIndex < 0 ? 0 : ($yIndex >= self::SIZE ? self::SIZE - 1 : $yIndex);
        $this->matrix[$yIndex][$xIndex]++;
        if (log($this->matrix[$yIndex][$xIndex], M_E) > $this->max) {
            $this->max = log($this->matrix[$yIndex][$xIndex], M_E);
        }
    }

    /**
     * save image
     */
    public function save()
    {
        for ($i = 0; $i < self::SIZE; $i++) {
            for ($j = 0; $j < self::SIZE; $j++) {
                list($r, $g, $b) = toBW(log($this->matrix[$i][$j], M_E) / $this->max);
                $color = imagecolorallocate($this->gd, $r, $g, $b);
                imagesetpixel($this->gd, $j, $i, $color);
                imagecolordeallocate($this->gd, $color);
            }
        }
        imagepng($this->gd, $this->path);
    }
}

$x = mt_rand() / mt_getrandmax() * 4 - 2;
$y = mt_rand() / mt_getrandmax() * 4 - 2;
$a = mt_rand() / mt_getrandmax() * 6 - 3;
$b = mt_rand() / mt_getrandmax() * 6 - 3;
$c = mt_rand() / mt_getrandmax() * 6 - 3;
$d = mt_rand() / mt_getrandmax() * 6 - 3;

$fileNameId = 1;
while (file_exists("Peter_De_Jong_$fileNameId.png")) {
    $fileNameId++;
}

$image = new Image("Peter_De_Jong_$fileNameId.png");
$data = [$x, $y];
for ($i = 1; $i < 2000000; $i++) {
    list($x, $y) = $data;
    $image->draw($x, $y);
    $data = getNextPoint($x, $y, $a, $b, $c, $d);
}
$image->save();
