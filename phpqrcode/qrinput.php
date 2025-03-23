<?php
require_once 'qrbitstream.php';

class QRinput {
    public $items;
    public $version;
    public $level;
    
    public function __construct($version = 1, $level = 'L') {
        $this->version = $version;
        $this->level = $level;
        $this->items = array();
    }
    
    public function getVersion() {
        return $this->version;
    }
    
    public function setVersion($version) {
        if ($version < 1 || $version > 40) {
            return false;
        }
        $this->version = $version;
        return true;
    }
    
    public function getErrorCorrectionLevel() {
        return $this->level;
    }
    
    public function setErrorCorrectionLevel($level) {
        if (!in_array($level, array('L', 'M', 'Q', 'H'))) {
            return false;
        }
        $this->level = $level;
        return true;
    }
    
    public function appendData($data, $mode = 0) {
        try {
            $entry = new QRinputItem($mode, strlen($data), $data);
            $this->items[] = $entry;
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function append($data, $mode = 0) {
        return $this->appendData($data, $mode);
    }
    
    public function getData() {
        $stream = new QRbitstream();
        
        foreach ($this->items as $item) {
            $stream->appendNum(4, $item->mode);
            $stream->appendNum(8, $item->size);
            $stream->appendBytes($item->size, array_values(unpack('C*', $item->data)));
        }
        
        return $stream;
    }
    
    public function getSize() {
        $size = 0;
        foreach ($this->items as $item) {
            $size += $item->size;
        }
        return $size;
    }
}

class QRinputItem {
    public $mode;
    public $size;
    public $data;
    
    public function __construct($mode, $size, $data) {
        $this->mode = $mode;
        $this->size = $size;
        $this->data = $data;
    }
} 