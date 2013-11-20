<?php
require_once "../PHPValidator/Validator.php";
require_once "../PHPValidator/Rule.php";
require_once "../PHPValidator/Rules/PhoneValidator.php";

class PhoneValidatorTest extends PHPUnit_FrameWork_TestCase
{
    public function setUp()
    { 
        parent::setUp();
    }
    public function tearDown()
    {
        parent::tearDown();
    }

    public function testPhoneValidator()
    {
        $validator = new PhoneValidator();
        $this->assertTrue($validator->validateValue('18810541532'));
        $this->assertTrue($validator->validateValue('13893604020'));

    }
}

