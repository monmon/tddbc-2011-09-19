<?php
class WordFilter
{
    const DELIMITER = ': ';

    protected $_ngWords = array();
    protected $_censoredString = 'censored';

    public function __construct()
    {
        foreach (func_get_args() as $ngWord) {
            $this->_ngWords[] = $this->_escape($ngWord);
        }
    }

    public function addNgWord($ngWord)
    {
        $this->_ngWords[] = $this->_escape($ngWord);
    }

    /**
     * 現在のNGワードを新しいNGワードに置き換える
     * 戻り値は今の所何も使えない
     */
    public function updateNgWord($currentNgWord, $newNgWord)
    {
        $key = array_search($currentNgWord, $this->_ngWords);
        if ($key === false) {
            return;
        }

        $this->_ngWords[$key] = $newNgWord;
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
        if (empty($this->_ngWords)) {
            return false;
        }

        list($name, $message) = $this->_splitText($text);
        foreach ($this->_ngWords as $ngWord) {
            if (strpos($message, $ngWord) !== false) {
                return true;
            }
        }

        return false;
    }

    public function censor($text)
    {
        if (empty($this->_ngWords)) {
            return $text;
        }

        list($name, $message) = $this->_splitText($text);
        $joinedNgWords = implode('|', $this->_ngWords);
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
