<?php
class WordFilter
{
    const REPLACE_STRING = '<censored>';

    protected $_ngWords = array();

    public function __construct($ngWord)
    {
        $this->_ngWords[] = $this->_escape($ngWord);
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

    /**
     * 複数登録されたngWordのうち、1つでもあればtrue、1つもなければfalse
     */
    public function detect($text)
    {
        if (empty($this->_ngWords)) {
            return false;
        }

        foreach ($this->_ngWords as $ngWord) {
            if (strpos($text, $ngWord) !== false) {
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

        $joinedNgWords = implode('|', $this->_ngWords);
        return implode(self::REPLACE_STRING, preg_split("/$joinedNgWords/", $text));
    }

    /**
     * censorで正規表現を使うため、ngWordsに特殊文字が入らないようにするためのmethod
     */
    protected function _escape($word)
    {
        return preg_quote($word, '/');
    }
}
