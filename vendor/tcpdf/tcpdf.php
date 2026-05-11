<?php
/**
 * TCPDF - PHP class for PDF
 * Minimal implementation for MindBridge reports
 */

class TCPDF
{
    private $buffer = '';
    private $pageCount = 0;
    private $currentPage = 0;
    private $fontSize = 12;
    private $fontFamily = 'helvetica';
    private $x = 10;
    private $y = 10;
    private $pageWidth = 210;
    private $pageHeight = 297;
    private $marginLeft = 15;
    private $marginRight = 15;
    private $marginTop = 27;
    private $marginBottom = 25;

    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4')
    {
        $this->AddPage();
    }

    public function AddPage($orientation = '')
    {
        if ($this->pageCount > 0) {
            $this->buffer .= "\n<!-- PAGE BREAK -->\n";
        }
        $this->pageCount++;
        $this->currentPage = $this->pageCount;
        $this->x = $this->marginLeft;
        $this->y = $this->marginTop;
    }

    public function SetFont($family, $style = '', $size = 0)
    {
        $this->fontFamily = $family;
        if ($size > 0) {
            $this->fontSize = $size;
        }
    }

    public function SetMargins($left, $top, $right = null)
    {
        $this->marginLeft = $left;
        $this->marginTop = $top;
        $this->marginRight = $right ?: $left;
    }

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        $this->buffer .= "<div style='position: absolute; left: {$this->x}mm; top: {$this->y}mm; width: {$w}mm; height: {$h}mm; font-size: {$this->fontSize}pt; font-family: {$this->fontFamily};'>{$txt}</div>\n";

        if ($ln > 0) {
            $this->y += $h;
            if ($ln == 1) {
                $this->x = $this->marginLeft;
            }
        } else {
            $this->x += $w;
        }
    }

    public function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false)
    {
        $lines = explode("\n", $txt);
        foreach ($lines as $line) {
            $this->Cell($w, $h, $line, $border, 2, $align, $fill);
        }
    }

    public function Ln($h = '')
    {
        $this->y += ($h ?: $this->fontSize * 0.4);
        $this->x = $this->marginLeft;
    }

    public function SetXY($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function GetY()
    {
        return $this->y;
    }

    public function GetX()
    {
        return $this->x;
    }

    public function Output($name = '', $dest = 'I')
    {
        if ($dest == 'I') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $name . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // For demo purposes, output as HTML that can be printed to PDF
            // In production, you'd use a proper PDF library
            echo $this->generateHTML();
        } elseif ($dest == 'D') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $name . '"');
            echo $this->generateHTML();
        }
    }

    private function generateHTML()
    {
        $html = "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>MindBridge Patient Report</title>
    <style>
        @page {
            size: A4;
            margin: {$this->marginTop}mm {$this->marginRight}mm {$this->marginBottom}mm {$this->marginLeft}mm;
        }
        body {
            font-family: {$this->fontFamily}, sans-serif;
            font-size: {$this->fontSize}pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .page-break {
            page-break-before: always;
        }
        div {
            position: absolute;
        }
    </style>
</head>
<body>
{$this->buffer}
</body>
</html>";
        return $html;
    }
}
?>