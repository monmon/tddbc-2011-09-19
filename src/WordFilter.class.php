<?php
class WordFilter
{
    const REPLACE_STRING = '<censored>';

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

    public function censor($text)
    {
        return str_replace($this->_ngWord, self::REPLACE_STRING, $text);
    }
}
