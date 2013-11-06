<?php
require_once dirname(__FILE__) . '/../src/WordFilter.class.php';

class WordFilterTest extends PHPUnit_Framework_TestCase
{
    protected $_filter;

    public function setUp()
    {
        $this->_filter = new WordFilter('Arsenal');
    }

    /**
     * @dataProvider data_NGワードが入っていたらtrue、入っていなかったらfalseを返すdetectメソッドがある
     */
    public function test_NGワードが入っていたらtrue、入っていなかったらfalseを返すdetectメソッドがある($message, $expected, $params)
    {
        $this->assertSame($expected, $this->_filter->detect($params), $message);
    }

    public function data_NGワードが入っていたらtrue、入っていなかったらfalseを返すdetectメソッドがある()
    {
        return array(
            array(
                'Arsenalの文字があったときにtrueを返しているか',
                true,
                't_wada: 昨日のArsenal vs Chelsea 熱かった!',
            ),
            array(
                'Arsenalの文字があったときにfalseを返しているか',
                false,
                't_wada: ManU vs Liverpool はそうでもなかった',
            ),
        );
    }

    /**
     * @dataProvider data_NGワードが入っていたらcensoredに置き換えるcensorメソッドがある
     */
    public function test_NGワードが入っていたらcensoredに置き換えるcensorメソッドがある($message, $expected, $params)
    {
        $this->assertSame($expected, $this->_filter->censor($params), $message);
    }

    public function data_NGワードが入っていたらcensoredに置き換えるcensorメソッドがある()
    {
        return array(
            array(
                'Arsenalの文字があったときにcensoredに置換されているか',
                't_wada: 昨日の<censored> vs Chelsea 熱かった!',
                't_wada: 昨日のArsenal vs Chelsea 熱かった!',
            ),
            array(
                'Arsenalの文字がなかったときに元のテキストのままになっているか',
                't_wada: ManU vs Liverpool はそうでもなかった',
                't_wada: ManU vs Liverpool はそうでもなかった',
            ),
        );
    }

    public function test_addNgWordメソッドを使ってNGワードを複数登録できる()
    {
        $filter = new WordFilter('Arsenal');
        $filter->addNgWord('ManU');

        $this->assertTrue(
            $filter->detect('t_wada: 昨日のArsenal vs Celsea 熱かった!') && $filter->detect('t_wada: ManU vs Liverpool はそうでもなかった'),
            'ArsenalとManUの文字をfilterできているか'
        );
        $this->assertSame(
            't_wada: 昨日の<censored> vs <censored> 熱かった!',
            $filter->censor('t_wada: 昨日のArsenal vs ManU 熱かった!'),
            'ArsenalのとManUの文字を置換できているか'
        );
    }
}
