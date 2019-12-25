<?php
/**
 * @author chenxb
 * @version 1.0.0
 * @changelog
 * Date: 2019/12/24
 * Time: 16:51
 */

namespace Enychan\IdentityCard\Tests;

use Enychan\IdentityCard\IdentityCard;
use PHPUnit\Framework\TestCase;

class IdentityCardEnTest extends TestCase
{
    /**
     * @var IdentityCard
     */
    protected $idCard;

    public function setUp()
    {
        $this->idCard = IdentityCard::make('350521199010211013', 'en-us');
    }

    public function testMake()
    {
        $this->assertEquals(IdentityCard::class, get_class($this->idCard));
    }

    public function testArea()
    {
        $this->assertEquals('Fujian Sheng Quanzhou Shi Huian Xian', $this->idCard->getArea());
    }

    public function testGender()
    {
        $this->assertEquals('Male', $this->idCard->getGender());
    }

    public function testBirthday()
    {
        $this->assertEquals('1990-10-21', $this->idCard->getBirthDay());
    }

    public function testAge()
    {
        $this->assertEquals(29, $this->idCard->getAge());
    }

    public function testJson()
    {
        $this->assertJson($this->idCard->toJson());
    }
}