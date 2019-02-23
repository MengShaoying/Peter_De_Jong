<?php
/**
 * Peter De Jong Attractor
 *
 * @author MengShaoying <mengshaoying@aliyun.com>
 */

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
    const SIZE = 400;
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
        if (log($this->matrix[$yIndex][$xIndex], 2) > $this->max) {
            $this->max = log($this->matrix[$yIndex][$xIndex], 2);
        }
    }

    /**
     * save image
     */
    public function save()
    {
        for ($i = 0; $i < self::SIZE; $i++) {
            for ($j = 0; $j < self::SIZE; $j++) {
                $color = intval(log($this->matrix[$i][$j], 2) * 255 / $this->max);
                $color = imagecolorallocate($this->gd, $color, $color, $color);
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
for ($i = 1; $i < 100000; $i++) {
    list($x, $y) = $data;
    $image->draw($x, $y);
    $data = getNextPoint($x, $y, $a, $b, $c, $d);
}
$image->save();
