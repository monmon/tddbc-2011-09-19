<?php
class WordFilter
{
    const REPLACE_STRING = '<censored>';

    protected $_ngWords = array();

    public function __construct($ngWord)
    {
        $this->_ngWords[] = $ngWord;
    }

    public function detect($text)
    {
        if (empty($this->_ngWords)) {
            return false;
        }

        if (strpos($text, $this->_ngWords[0]) === false) {
            return false;
        }

        return true;
    }

    public function censor($text)
    {
        if (empty($this->_ngWords)) {
            return $text;
        }

        return str_replace($this->_ngWords[0], self::REPLACE_STRING, $text);
    }
}
