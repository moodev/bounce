<?php
namespace MooDev\Bounce\Proxy\Utils;
require_once __DIR__ . '/../../../../TestInit.php';


class Base32HexTest extends \PHPUnit_Framework_TestCase {

    public function encodeData() {
        return array(
            array("A", "84"),
            array("AB", "8510"),
            array("ABC", "85146"),
            array("ABCD", "85146h0"),
            array("ABCDE", "85146h25"),
            array("ABCDEA", "85146h2584"),
        );
    }

    /**
     * @dataProvider encodeData
     */
    public function testEncode($in, $out) {
        $this->assertEquals($out, Base32Hex::encode($in));
    }
}