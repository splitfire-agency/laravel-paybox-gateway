<?php

namespace Tests;

use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Class UnitTestCase
 * @package Tests
 */
class UnitTestCase extends TestCase
{
  /**
   * Close Mockery on tear down
   */
  public function tearDown(): void
  {
    Mockery::close();
  }
}
