<?php

namespace App\Support;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * QR codes for links people are meant to scan off a printed page.
 *
 * Uses bacon/bacon-qr-code, which is already a dependency — Fortify draws the
 * two-factor QR with it. The obvious package for this, simplesoftwareio/
 * simple-qrcode, pins bacon to ^2.0 and installing it would have forced a
 * downgrade of the library two-factor sign-in depends on.
 *
 * SVG rather than PNG: it stays sharp at any print size, needs no image
 * extension, and is small enough to inline in a page.
 */
class QrCode
{
    /**
     * An SVG QR code for the given text.
     *
     * @param  int  $size  Width and height in pixels.
     */
    public static function svg(string $text, int $size = 220): string
    {
        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle($size, 1, null, null, Fill::uniformColor(
                    new Rgb(255, 255, 255),
                    new Rgb(15, 23, 42),
                )),
                new SvgImageBackEnd
            )
        );

        return $writer->writeString($text);
    }

    /**
     * The same SVG with its XML declaration removed, for inlining in HTML.
     *
     * A second <?xml ...?> inside a page is invalid, and browsers handle it
     * inconsistently.
     */
    public static function inline(string $text, int $size = 220): string
    {
        $svg = self::svg($text, $size);

        $firstNewline = strpos($svg, "\n");

        return $firstNewline === false ? $svg : trim(substr($svg, $firstNewline + 1));
    }
}
