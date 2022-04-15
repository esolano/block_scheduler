<?php

namespace Drupal\Tests\block_scheduler\Unit;

use Drupal\block_scheduler\Plugin\Condition\Expiry;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * Expiry test class.
 *
 * @group auto_block_scheduler
 *
 * @coversDefaultClass \Drupal\block_scheduler\Plugin\Condition\Expiry
 */
class ExpiryTest extends UnitTestCase {

  /**
   * @covers ::getCacheMaxAge
   *
   * @dataProvider providerGetCacheMaxAge
   */
  public function testGetCacheMaxAge($current_time, $published_on, $unpublished_on, $expected_max_age) {
    $configuration = [
      'start' => $published_on,
      'end' => $unpublished_on,
    ];

    $container = new Container();

    // Mock the time service.
    $time = $this->prophesize(TimeInterface::class);
    $time->getRequestTime()->willReturn($current_time);
    $container->set('datetime.time', $time->reveal());

    $fixture = Expiry::create($container, $configuration, 'foo', []);
    $this->assertEquals($expected_max_age, $fixture->getCacheMaxAge());
  }

  /**
   * Get caching cases.
   */
  public function providerGetCacheMaxAge() {
    $cases = [];

    $cases['empty_dates'] = [
      10000,
      0,
      0,
      Cache::PERMANENT,
    ];

    $cases['old published on, no end date'] = [
      10000,
      5000,
      0,
      Cache::PERMANENT,
    ];

    $cases['future published on, no end date'] = [
      10000,
      11000,
      0,
      1000,
    ];

    $cases['no publish, has unpublish'] = [
      10000,
      0,
      12000,
      2000,
    ];

    $cases['past publish, has upublish'] = [
      10000,
      5000,
      13000,
      3000,
    ];

    $cases['future both'] = [
      10000,
      11000,
      12000,
      1000,
    ];

    $cases['past both'] = [
      10000,
      5000,
      6000,
      Cache::PERMANENT,
    ];

    return $cases;
  }

}
