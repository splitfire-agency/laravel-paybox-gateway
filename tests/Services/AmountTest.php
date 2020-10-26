<?php

namespace Tests\Services;

use Bnb\PayboxGateway\Services\Amount;
use Tests\UnitTestCase;

/**
 * Class AmountTest
 * @package Tests\Services
 * @group AmountTest
 */
class AmountTest extends UnitTestCase
{
  /**
   * Test amount with dot
   */
  public function testValidAmountWithDot()
  {
    $service = new Amount();
    $this->assertSame('10022', $service->get(100.22, false));
  }

  /**
   * Test amount with comma
   */
  public function testValidAmountWithComma()
  {
    $service = new Amount();
    $this->assertSame('210045', $service->get('2100,45', false));
  }

  /**
   * Test amount with dot and fill
   */
  public function testValidAmountWithDotAndFill()
  {
    $service = new Amount();
    $this->assertSame('0000010022', $service->get(100.22, true));
  }

  /**
   * Test amount with comma and fill
   */
  public function testValidAmountWithCommaAndFill()
  {
    $service = new Amount();
    $this->assertSame('0000210045', $service->get('2100,45', true));
  }

  /**
   * Test amount with integer number
   */
  public function testValidAmountWithIntegerNumber()
  {
    $service = new Amount();
    $this->assertSame('210000', $service->get(2100, false));
  }

  /**
   * Test amount with integer-float number
   */
  public function testValidAmountWithFloatIntegerNumber()
  {
    $service = new Amount();
    $this->assertSame('210000', $service->get(2100.0, false));
  }

  /**
   * Test amount with small number
   */
  public function testValidAmountWithVerySmallNumber()
  {
    $service = new Amount();
    $this->assertSame('1', $service->get(0.01, false));
  }

  /**
   * Test amount with small number and fill
   */
  public function testValidAmountWithVerySmallNumberWhenFill()
  {
    $service = new Amount();
    $this->assertSame('0000000001', $service->get(0.01, true));
  }
}
