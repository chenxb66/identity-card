<?php
/**
 * @author chenxb
 * @version 1.0.0
 * @changelog
 * Date: 2019/12/24
 * Time: 15:01
 */

namespace Apichen\IdentityCard;

use Apichen\IdentityCard\Exceptions\FormatException;
use Apichen\IdentityCard\Exceptions\LangException;

class IdentityCard
{

    /**
     * 身份证号
     *
     * @var string
     */
    protected $idCard;

    /**
     * 多语言
     *
     * @var array
     */
    protected $lang = [];

    /**
     * 构造函数
     *
     * @param string $idCard
     * @param string $lang
     */
    protected function __construct(string $idCard, string $lang = 'zh-cn')
    {
        $this->idCard = $idCard;

        $this->lang = require __DIR__ . "/Langs/{$lang}.php";
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * 创建对象（不是单例对象）
     *
     * @param string $idCard
     * @param string $locale
     * @return IdentityCard
     * @throws FormatException
     * @throws LangException
     * @throws \Exception
     */
    public static function make(string $idCard, string $locale = 'zh-cn'): IdentityCard
    {
        if (!static::check($idCard)) {
            throw new FormatException('Identity Card Format Error');
        }

        if (!in_array($locale, ['zh-cn', 'en-us'])) {
            throw new LangException("Language '{$locale}' Not Support");
        }

        return (new static($idCard, $locale));
    }

    /**
     * 检查身份证格式（只支持18位身份证）
     *
     * @param string $idCard
     * @return bool
     */
    public static function check(string $idCard): bool
    {
        // 验证长度
        if (!$idCard || strlen($idCard) != 18) {
            return false;
        }

        // 验证出生日期
        $birthday = substr($idCard, 6, 8);
        $rPattern = '/^((19[0-9]{2})|(20[0-9]{2}))((0[1-9]{1})|(1[012]{1}))((0[1-9]{1})|(1[0-9]{1})|(2[0-9]{1})|3[01]{1})$/';
        if (!preg_match($rPattern, $birthday)) {
            return false;
        }

        // 验证省份
        $area = [
            11, 12, 13, 14, 15, 21, 22, 23, 31, 32, 33, 34, 35, 36, 37, 41, 42,
            43, 44, 45, 46, 50, 51, 52, 53, 54, 61, 62, 63, 64, 65, 71, 81, 82, 91
        ];
        if (!array_search(substr($idCard, 0, 2), $area)) {
            return false;
        }

        // 验证规则
        $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        $vi = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $ni = 0;
        $value = (string)$idCard;
        $len = strlen($value) - 1;
        for ($i = 0, $max = $len; $i < $max; $i++) {
            $aiv = (int)($value[$i] ?: 0);
            $wiv = (int)($wi[$i] ?: 0);
            $ni += ($aiv * $wiv);
        }
        return (strcasecmp((string)($vi[$ni % 11]), (string)($value[$len])) === 0);
    }

    /**
     * 获取省市县
     *
     * @param string $separate
     * @return string
     */
    public function getArea(string $separate = ' '): string
    {
        return "{$this->getProvince()}{$separate}{$this->getCity()}{$separate}{$this->getCounty()}";
    }

    /**
     * 获取省份
     *
     * @return  string|null
     */
    public function getProvince(): ?string
    {
        $k = substr($this->idCard, 0, 2) . '0000';

        if (!isset($this->lang['regions'][$k])) {
            return null;
        }

        return $this->lang['regions'][$k];
    }

    /**
     * 获取城市
     *
     * @return  string|null
     */
    public function getCity(): ?string
    {
        $k = substr($this->idCard, 0, 4) . '00';

        if (!isset($this->lang['regions'][$k])) {
            return null;
        }

        return $this->lang['regions'][$k];
    }

    /**
     * 获取县市
     *
     * @return  string|null
     */
    public function getCounty(): ?string
    {
        $k = substr($this->idCard, 0, 6);

        if (!isset($this->lang['regions'][$k])) {
            return null;
        }

        return $this->lang['regions'][$k];
    }

    /**
     * 获取年龄
     *
     * @return int
     * @throws \Exception
     */
    public function getAge(): int
    {
        $birthday = substr($this->idCard, 6, 8);
        return (int)(new \DateTime())->diff((new \DateTime($birthday)))->format('%y');
    }

    /**
     * 获取生日
     *
     * @param string $format
     * @return string
     */
    public function getBirthDay(string $format = 'Y-m-d'): string
    {
        return date(
            $format,
            mktime(
                0,
                0,
                0,
                substr($this->idCard, 10, 2),
                substr($this->idCard, 12, 2),
                substr($this->idCard, 6, 4))
        );
    }

    /**
     * 获取生肖
     *
     * @return  string
     */
    public function getZodiac(): string
    {
        return $this->lang['zodiac'][abs(substr($this->idCard, 6, 4) - 1901) % 12];
    }

    /**
     * 获取性别
     *
     * @return  string
     */
    public function getGender(): string
    {
        return $this->lang['gender'][(substr($this->idCard, 16, 1) % 2 == 0) ? 'female' : 'male'];
    }

    /**
     * 获取星座
     *
     * @return  string
     */
    public function getConstellation(): string
    {
        $constellationEdgeDays = [21, 20, 21, 20, 21, 22, 23, 23, 23, 24, 22, 21];

        $constellations = $this->lang['constellations'];

        $month = (int)substr($this->idCard, 10, 2);

        $month = $month - 1;

        $day = (int)substr($this->idCard, 12, 2);

        if ($day < $constellationEdgeDays[$month]) {
            $month = $month - 1;
        }

        if ($month > 0) {
            return $constellations[$month];
        }

        return $constellations[11];
    }

    /**
     * 转成数组
     *
     * @return array
     * @throws \Exception
     */
    public function toArray(): array
    {
        return [
            'area' => $this->getArea(),
            'province' => $this->getProvince(),
            'city' => $this->getCity(),
            'county' => $this->getCounty(),
            'gender' => $this->getGender(),
            'birthday' => $this->getBirthday(),
            'zodiac' => $this->getZodiac(),
            'age' => $this->getAge(),
            'constellation' => $this->getConstellation()
        ];
    }

    /**
     * 转成JSON字符串
     *
     * @param int $options
     * @return string
     * @throws \Exception
     */
    public function toJson(int $options = JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this->toArray(), $options);
    }
}