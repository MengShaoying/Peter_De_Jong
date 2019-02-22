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
 * @param float $a
 * @param float $b
 * @param float $c
 * @param float $d
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
    /** @var string file location */
    private $path;
    /** @var resource a GD resource */
    private $gd;
    /** @var int color */
    private $color;

    /**
     * Create a Image instance
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->gd = imagecreate(4000, 4000);
        imagecolorallocate($this->gd, 0, 0, 0);
        $this->color = imagecolorallocate($this->gd, 255, 255, 255);
        $this->path = $path;
    }

    /**
     * draw a point
     *
     * @param float $x
     * @param float $y
     */
    public function draw($x, $y)
    {
        imagesetpixel($this->gd, intval(1000 * $x + 2000), intval(1000 * $y + 2000), $this->color);
    }

    /**
     * save image
     */
    public function save()
    {
        imagepng($this->gd, $this->path);
    }
}

$x = mt_rand() / mt_getrandmax() * 4 - 2;
$y = mt_rand() / mt_getrandmax() * 4 - 2;
$a = mt_rand() / mt_getrandmax() * 6 - 3;
$b = mt_rand() / mt_getrandmax() * 6 - 3;
$c = mt_rand() / mt_getrandmax() * 6 - 3;
$d = mt_rand() / mt_getrandmax() * 6 - 3;

$image = new Image('Peter_De_Jong.png');
$data = [$x, $y];
for ($i = 1; $i < 2000000; $i++) {
    list($x, $y) = $data;
    $image->draw($x, $y);
    $data = getNextPoint($x, $y, $a, $b, $c, $d);
}
$image->save();
