<?php
class QRvect {
    public static function eps($frame, $filename = false, $pixelPerPoint = 4, $outerFrame = 4) {
        $h = count($frame);
        $w = strlen($frame[0]);
        
        $imgW = $w + 2 * $outerFrame;
        $imgH = $h + 2 * $outerFrame;
        
        $output = '';
        $output .= '%!PS-Adobe-3.0 EPSF-3.0' . "\n";
        $output .= '%%Creator: PHP QR Code' . "\n";
        $output .= '%%Title: QR Code' . "\n";
        $output .= '%%BoundingBox: 0 0 ' . $imgW * $pixelPerPoint . ' ' . $imgH * $pixelPerPoint . "\n";
        $output .= '%%EndComments' . "\n";
        $output .= '%%EndProlog' . "\n";
        $output .= '%%Page: 1 1' . "\n";
        $output .= "\n";
        $output .= '/q { gsave } bind def' . "\n";
        $output .= '/Q { grestore } bind def' . "\n";
        $output .= '/cm { 6 array astore concat } bind def' . "\n";
        $output .= '/w { setlinewidth } bind def' . "\n";
        $output .= '/J { setlinecap } bind def' . "\n";
        $output .= '/j { setlinejoin } bind def' . "\n";
        $output .= '/M { setmiterlimit } bind def' . "\n";
        $output .= '/d { setdash } bind def' . "\n";
        $output .= '/m { moveto } bind def' . "\n";
        $output .= '/l { lineto } bind def' . "\n";
        $output .= '/c { curveto } bind def' . "\n";
        $output .= '/h { closepath } bind def' . "\n";
        $output .= '/re { exch dup neg 3 1 roll 5 3 roll moveto 0 rlineto' . "\n";
        $output .= '    0 exch rlineto 0 rlineto closepath } bind def' . "\n";
        $output .= '/S { stroke } bind def' . "\n";
        $output .= '/f { fill } bind def' . "\n";
        $output .= '/f* { eofill } bind def' . "\n";
        $output .= '/n { newpath } bind def' . "\n";
        $output .= '/W { clip } bind def' . "\n";
        $output .= '/W* { eoclip } bind def' . "\n";
        $output .= '/B { fill } bind def' . "\n";
        $output .= '/B* { eofill } bind def' . "\n";
        $output .= '/b { fill } bind def' . "\n";
        $output .= '/b* { eofill } bind def' . "\n";
        $output .= '/Z { showpage } bind def' . "\n";
        $output .= '/s { show } bind def' . "\n";
        $output .= '/S { stroke } bind def' . "\n";
        $output .= '/s { show } bind def' . "\n";
        $output .= "\n";
        
        $output .= $pixelPerPoint . ' ' . $pixelPerPoint . ' scale' . "\n";
        $output .= $outerFrame . ' ' . $outerFrame . ' translate' . "\n";
        $output .= "\n";
        
        $output .= '/p { newpath moveto lineto lineto lineto closepath } bind def' . "\n";
        $output .= "\n";
        
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                if ($frame[$y][$x] == '1') {
                    $output .= $x . ' ' . ($h - $y) . ' p f' . "\n";
                }
            }
        }
        
        $output .= "\n";
        $output .= 'showpage' . "\n";
        $output .= '%%EOF' . "\n";
        
        if ($filename !== false) {
            file_put_contents($filename, $output);
        } else {
            header('Content-Type: application/postscript');
            echo $output;
        }
    }
    
    public static function svg($frame, $filename = false, $pixelPerPoint = 4, $outerFrame = 4) {
        $h = count($frame);
        $w = strlen($frame[0]);
        
        $imgW = $w + 2 * $outerFrame;
        $imgH = $h + 2 * $outerFrame;
        
        $output = '';
        $output .= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $output .= '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">' . "\n";
        $output .= '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 ' . $imgW * $pixelPerPoint . ' ' . $imgH * $pixelPerPoint . '">' . "\n";
        $output .= "\t<g transform=\"translate(" . $outerFrame * $pixelPerPoint . "," . $outerFrame * $pixelPerPoint . ") scale(" . $pixelPerPoint . "," . $pixelPerPoint . ")\">\n";
        
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                if ($frame[$y][$x] == '1') {
                    $output .= "\t\t<rect x=\"" . $x . "\" y=\"" . $y . "\" width=\"1\" height=\"1\" fill=\"black\"/>\n";
                }
            }
        }
        
        $output .= "\t</g>\n";
        $output .= '</svg>' . "\n";
        
        if ($filename !== false) {
            file_put_contents($filename, $output);
        } else {
            header('Content-Type: image/svg+xml');
            echo $output;
        }
    }
} 