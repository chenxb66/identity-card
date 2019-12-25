# IDENTITY CARD
分析用户身份证的扩展包

## 安装
```shell
$ composer require chenxiaobo/identity-card -vvv
```

## 配置
暂无配置

## 使用
```php
<?php

use Chenxiaobo\IdentityCard\IdentityCard;

$idCardObject = IdentityCard::make('xxxxxxxxxxxxxxxxxx', 'zh-cn');
```

### 获取身份证所在省市县
```php
<?php

// 获取获取身份证所在省市县
$idCardObject->getArea();  //福建省 泉州市 惠安县
$idCardObject->getProvince();
$idCardObject->getCity();
$idCardObject->getCounty();
$idCardObject->getAge();
$idCardObject->getBirthDay($format = 'Y-m-d');
$idCardObject->getZodiac();
$idCardObject->getGender();
$idCardObject->getConstellation();
$idCardObject->toArray();
$idCardObject->toJson($options = JSON_UNESCAPED_UNICODE);

IdentityCard::check(string $idCard);
```