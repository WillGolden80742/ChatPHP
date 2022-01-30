<?php
   
    class StringT {
        private $value;
        private $regex = '/[^[:alpha:]_0-9]/';
        function __construct($value) {
            $this->value = preg_replace($this->regex,'',$value);
        }
        public function __toString():string {
            return $this->value;   
        }
    }

?>    