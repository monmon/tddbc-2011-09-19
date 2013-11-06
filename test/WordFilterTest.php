<?php
require_once dirname(__FILE__) . '/../src/WordFilter.class.php';

class WordFilterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider data_NGワードが入っていたらtrue、入っていなかったらfalseを返すdetectメソッドがある
     */
    public function test_NGワードが入っていたらtrue、入っていなかったらfalseを返すdetectメソッドがある($message, $expected, $params)
    {
        $filter = new WordFilter('Arsenal');

        $this->assertSame($expected, $filter->detect($params), $message);
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
}
