<?php
class QRrsItem {
    public $mm;
    public $nn;
    public $alpha_to;
    public $index_of;
    public $genpoly;
    public $nroots;
    public $fcr;
    public $prim;
    public $iprim;
    public $pad;
    
    public function __construct($symsize, $gfpoly, $fcr, $prim, $nroots, $pad) {
        $this->mm = $symsize;
        $this->nn = (1 << $symsize) - 1;
        $this->pad = $pad;
        
        $this->alpha_to = array_fill(0, $this->nn + 1, 0);
        $this->index_of = array_fill(0, $this->nn + 1, 0);
        
        // تولید جداول لوگ و آنتی‌لوگ
        $this->generateTables($gfpoly);
        
        $this->fcr = $fcr;
        $this->prim = $prim;
        $this->nroots = $nroots;
        
        // محاسبه چندجمله‌ای مولد
        $this->genpoly = array();
        $this->generatePoly();
    }
    
    private function generateTables($gfpoly) {
        $this->alpha_to[$this->mm] = 0;
        for ($i = 0; $i < $this->mm; $i++) {
            $this->alpha_to[$i] = 1 << $i;
        }
        
        for ($i = $this->mm + 1; $i < $this->nn; $i++) {
            $this->alpha_to[$i] = $this->alpha_to[$i - 1];
            for ($j = 1; $j < $this->mm; $j++) {
                if ($this->alpha_to[$i] & (1 << ($this->mm - 1))) {
                    $this->alpha_to[$i] &= ~(1 << ($this->mm - 1));
                    $this->alpha_to[$i] ^= $gfpoly;
                }
                $this->alpha_to[$i] = ($this->alpha_to[$i] << 1) & $this->nn;
            }
        }
        
        for ($i = 0; $i <= $this->nn; $i++) {
            $this->index_of[$this->alpha_to[$i]] = $i;
        }
        $this->index_of[0] = -1;
    }
    
    private function generatePoly() {
        $this->genpoly = array_fill(0, $this->nroots + 1, 0);
        $this->genpoly[0] = 1;
        
        for ($i = 0; $i < $this->nroots; $i++) {
            $this->genpoly[$i + 1] = 1;
            
            for ($j = $i; $j > 0; $j--) {
                if ($this->genpoly[$j] != 0) {
                    $this->genpoly[$j] = $this->genpoly[$j - 1] ^ 
                        $this->alpha_to[($this->index_of[$this->genpoly[$j]] + $this->fcr + $i) % $this->nn];
                } else {
                    $this->genpoly[$j] = $this->genpoly[$j - 1];
                }
            }
            $this->genpoly[0] = $this->alpha_to[($this->index_of[$this->genpoly[0]] + $this->fcr + $i) % $this->nn];
        }
    }
    
    public function encode($data, $parity) {
        for ($i = 0; $i < $this->nroots; $i++) {
            $parity[$i] = 0;
        }
        
        for ($i = 0; $i < count($data); $i++) {
            $feedback = $this->index_of[$data[$i] ^ $parity[0]];
            if ($feedback != -1) {
                for ($j = 1; $j < $this->nroots; $j++) {
                    $parity[$j] ^= $this->alpha_to[($feedback + $this->genpoly[$this->nroots - $j]) % $this->nn];
                }
            }
            
            array_shift($parity);
            array_push($parity, 0);
        }
        
        return $parity;
    }
    
    public function decode($data, $erasures = array()) {
        $syndromes = array_fill(0, $this->nroots, 0);
        
        // محاسبه سندرم‌ها
        for ($i = 0; $i < $this->nroots; $i++) {
            $syndromes[$i] = $data[0];
            for ($j = 1; $j < count($data); $j++) {
                $syndromes[$i] = $data[$j] ^ $this->alpha_to[($this->index_of[$syndromes[$i]] + $i) % $this->nn];
            }
        }
        
        // بررسی خطا
        $hasError = false;
        for ($i = 0; $i < $this->nroots; $i++) {
            if ($syndromes[$i] != 0) {
                $hasError = true;
                break;
            }
        }
        
        if (!$hasError) {
            return true;
        }
        
        // محاسبه چندجمله‌ای خطا
        $errorPoly = array(1);
        $omega = array();
        
        for ($i = 0; $i < $this->nroots; $i++) {
            $omega[$i] = $syndromes[$i];
        }
        
        // اصلاح خطا
        for ($i = 0; $i < count($data); $i++) {
            $delta = $data[$i];
            for ($j = 1; $j < count($errorPoly); $j++) {
                $delta ^= $this->alpha_to[($this->index_of[$errorPoly[$j]] + $i) % $this->nn];
            }
            
            if ($delta != 0) {
                $data[$i] ^= $delta;
            }
        }
        
        return true;
    }
} 