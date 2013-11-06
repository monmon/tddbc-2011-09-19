<?php
class WordFilter
{
    protected $_ngWord;

    public function __construct($ngWord)
    {
        $this->_ngWord = $ngWord;
    }

    public function detect($text)
    {
        if (strpos($text, $this->_ngWord) === false) {
            return false;
        }

        return true;
    }
}
