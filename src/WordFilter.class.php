<?php
class WordFilter
{
    const DELIMITER = ': ';

    protected $_existsNgWord = array();  // array('ng_word1' => true, 'ng_word2' => true) のようなNGワード群
    protected $_censoredString = 'censored';

    public function __construct()
    {
        foreach (func_get_args() as $ngWord) {
            $this->_existsNgWord[$this->_escape($ngWord)] = true;
        }
    }

    public function addNgWord($ngWord)
    {
        $this->_existsNgWord[$this->_escape($ngWord)] = true;
    }

    public function updateNgWord($currentNgWord, $newNgWord)
    {
        unset($this->_existsNgWord[$currentNgWord]);
        $this->_existsNgWord[$newNgWord] = 1;
    }

    public function setCensoredString($string)
    {
        $this->_censoredString = $string;
    }

    /**
     * 複数登録されたngWordのうち、1つでもあればtrue、1つもなければfalse
     */
    public function detect($text)
    {
        if (empty($this->_existsNgWord)) {
            return false;
        }

        list($name, $message) = $this->_splitText($text);
        foreach (array_keys($this->_existsNgWord) as $ngWord) {
            if (strpos($message, $ngWord) !== false) {
                return true;
            }
        }

        return false;
    }

    public function censor($text)
    {
        if (empty($this->_existsNgWord)) {
            return $text;
        }

        list($name, $message) = $this->_splitText($text);
        $joinedNgWords = implode('|', array_keys($this->_existsNgWord));
        $censoredMessage =  implode("<$this->_censoredString>", preg_split("/$joinedNgWords/", $message));

        return $this->_createText($name, $censoredMessage);
    }

    /**
     * censorで正規表現を使うため、ngWordsに特殊文字が入らないようにするためのmethod
     */
    protected function _escape($word)
    {
        return preg_quote($word, '/');
    }

    /**
     * 「ユーザ名DELIMITERメッセージ」というテキスト形式を「ユーザ名」と「メッセージ」に分割して返す
     * @see _createText
     */
    protected function _splitText($text)
    {
        return explode(self::DELIMITER, $text, 2);
    }

    /**
     * 「ユーザ名」と「メッセージ」を「ユーザ名DELIMITERメッセージ」というテキスト形式にして返す
     * _splitText の逆
     * @see _splitText
     */
    protected function _createText($name, $message)
    {
        return implode(self::DELIMITER, array($name, $message));
    }
}
