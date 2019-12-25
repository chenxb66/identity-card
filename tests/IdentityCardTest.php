<?php
/**
 * @author chenxb
 * @version 1.0.0
 * @changelog
 * Date: 2019/12/24
 * Time: 16:51
 */

namespace Apichen\IdentityCard\Tests;

use Apichen\IdentityCard\IdentityCard;
use PHPUnit\Framework\TestCase;

class IdentityCardTest extends TestCase
{
    /**
     * @var IdentityCard
     */
    protected $idCard;

    public function setUp()
    {
        $this->idCard = IdentityCard::make('350521199010211013');
    }

    public function testMake()
    {
        $this->assertEquals(IdentityCard::class, get_class($this->idCard));
    }

    public function testArea()
    {
        $this->assertEquals('福建省 泉州市 惠安县', $this->idCard->getArea());
    }

    public function testGender()
    {
        $this->assertEquals('男', $this->idCard->getGender());
    }

    public function testBirthday()
    {
        $this->assertEquals('1990-10-21', $this->idCard->getBirthDay());
    }

    public function testAge()
    {
        $this->assertEquals(29, $this->idCard->getAge());
    }

    public function testConstellation()
    {
        $this->assertEquals('天秤座', $this->idCard->getConstellation());
    }

    public function testJson()
    {
        $this->assertJson($this->idCard->toJson());
    }
}