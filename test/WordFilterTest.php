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

    public function test_26ページ見てみたら複数NGワードの指定はconstructのタイミングで欲しいようなのでそれも可能にする()
    {
        $filter = new WordFilter('Arsenal', 'ManU');

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

    public function test_NGワードに特殊文字が入っていても機能する()
    {
        $filter = new WordFilter('NG|W/ord');

        $this->assertSame('t_wada: <censored> は検閲される',
            $filter->censor('t_wada: NG|W/ord は検閲される'),
            '特殊文字が入っていても正しく機能するか'
        );
    }

    public function test_NGワードを後から変更できる()
    {
        $filter = new WordFilter('Arsenal');
        $filter->updateNgWord('Arsenal', 'Celsea');

        $this->assertSame(
            't_wada: 昨日のArsenal vs <censored> 熱かった!',
            $filter->censor('t_wada: 昨日のArsenal vs Celsea 熱かった!'),
            '変更後のCelseaの文字を検閲できているか'
        );
    }

    public function test_censoredに変わる文字をセットすればその文字になる()
    {
        $filter = new WordFilter('Celsea');
        $filter->setCensoredString('covered');

        $this->assertSame(
            't_wada: 昨日のArsenal vs <covered> 熱かった!',
            $filter->censor('t_wada: 昨日のArsenal vs Celsea 熱かった!'),
            'censoredではなくcoveredになっているか'
        );
    }

    public function test_名前がNGワードでも引っかからない()
    {
        $filter = new WordFilter('Arsenal');

        $this->assertFalse(
            $filter->detect('Arsenal: 昨日のCelsea 熱かった!'),
            '名前のArsenalには引っかからないでいるか'
        );
        $this->assertSame(
            'Arsenal: 昨日の<censored> vs ManU 熱かった!',
            $filter->censor('Arsenal: 昨日のArsenal vs ManU 熱かった!'),
            '名前のArsenalの文字は置換されずにいるか'
        );
    }

    public function test_名前とメッセージのdelimiterがメッセージに入っていても機能する()
    {
        $filter = new WordFilter('Arsenal');

        $this->assertSame(
            'Arsenal: 昨日の<censored> vs ManU : 熱かった!',
            $filter->censor('Arsenal: 昨日のArsenal vs ManU : 熱かった!'),
            ': がメッセージの途中にあっても問題ないか'
        );
    }
}
