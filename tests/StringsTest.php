<?php

namespace Matecat\XliffParser\Tests;

use Matecat\XliffParser\Utils\Strings;

class StringsTest extends BaseTest
{
    /**
     * @test
     * @throws \Exception
     */
    public function can_detect_escaped_html()
    {
        $strings = [
            '&lt;ph id="1" /&gt;',
            '&lt;div class="test"&gt;This is an html string &lt; /div&gt;',
        ];

        foreach ($strings as $string){
            $this->assertTrue(Strings::isAnEscapedHTML($string));
        }

        $strings = [
                '<ph id="1" />',
                '<div class="test">This is an html string < /div>',
        ];

        foreach ($strings as $string){
            $this->assertFalse(Strings::isAnEscapedHTML($string));
        }
    }

    /**
     * @test
     */
    public function can_detect_escaped_html_additional_test()
    {
        $string = '<5 &lt;pc id="1"/&gt;';

        $this->assertTrue(Strings::isAnEscapedHTML($string));

        $string = '&lt;5 <pc id="1"/>';

        $this->assertFalse(Strings::isAnEscapedHTML($string));
    }

    /**
     * @test
     * @throws \Exception
     */
    public function can_detect_JSON()
    {
        $json = '{
            "key": "name",
            "key2": "name2",
            "key3": "name3"
        }';

        $notJson = "This is a sample text";

        $this->assertFalse(Strings::isJSON($notJson));
        $this->assertTrue(Strings::isJSON($json));
    }

    /**
     * @test
     */
    public function can_fix_not_well_formed_xml()
    {
        $original = '<g id="1">Hello</g>, 4 > 3 -> <g id="1">Hello</g>, 4 &gt; 3';
        $expected = '<g id="1">Hello</g>, 4 &gt; 3 -&gt; <g id="1">Hello</g>, 4 &gt; 3';

        $this->assertEquals($expected, Strings::fixNonWellFormedXml($original));

        $original = '<mrk id="1">Test1</mrk><mrk id="2">Test2<ex id="1">Another Test Inside</ex></mrk><mrk id="3">Test3<a href="https://example.org">ClickMe!</a></mrk>';
        $expected = '<mrk id="1">Test1</mrk><mrk id="2">Test2<ex id="1">Another Test Inside</ex></mrk><mrk id="3">Test3&lt;a href="https://example.org"&gt;ClickMe!&lt;/a&gt;</mrk>';

        $this->assertEquals($expected, Strings::fixNonWellFormedXml($original));

        $tests = array(
                '' => '',
                'just text' => 'just text',
                '<gap>Hey</gap>' => '&lt;gap&gt;Hey&lt;/gap&gt;',
                '<mrk>Hey</mrk>' => '<mrk>Hey</mrk>',
                '<g >Hey</g >' => '<g >Hey</g >',
                '<g    >Hey</g   >' => '<g    >Hey</g   >',
                '<g id="99">Hey</g>' => '<g id="99">Hey</g>',
                'Hey<x/>' => 'Hey<x/>',
                'Hey<x />' => 'Hey<x />',
                'Hey<x   />' => 'Hey<x   />',
                'Hey<x id="15"/>' => 'Hey<x id="15"/>',
                'Hey<bx id="1"/>' => 'Hey<bx id="1"/>',
                'Hey<ex id="1"/>' => 'Hey<ex id="1"/>',
                '<bpt id="1">Hey</bpt>' => '<bpt id="1">Hey</bpt>',
                '<ept id="1">Hey</ept>' => '<ept id="1">Hey</ept>',
                '<ph id="1">Hey</ph>' => '<ph id="1">Hey</ph>',
                '<it id="1">Hey</it>' => '<it id="1">Hey</it>',
                '<mrk mid="3" mtype="seg"><g id="2">Hey man! <x id="1"/><b id="dunno">Hey man & hey girl!</b></mrk>' => '<mrk mid="3" mtype="seg"><g id="2">Hey man! <x id="1"/>&lt;b id="dunno"&gt;Hey man &amp; hey girl!&lt;/b&gt;</mrk>',
        );

        foreach ($tests as $in => $expected) {
            $out = Strings::fixNonWellFormedXml($in);
            $this->assertEquals($expected, $out);
        }
    }

    /**
     * @test
     */
    public function can_validate_an_uuid()
    {
        $not_valid_uuid = 'xxx';
        $uuid = '4213862b-596b-4b03-b175-baf4a0ed6fd8';

        $this->assertFalse(Strings::isAValidUuid($not_valid_uuid));
        $this->assertTrue(Strings::isAValidUuid($uuid));
    }
}
