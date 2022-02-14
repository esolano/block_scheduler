<?php

namespace Drupal\Tests\block_scheduler\Functional;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Test the Block Scheduler module install without errors.
 *
 * @group block_scheduler
 */
class InstallTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['block_scheduler'];

  /**
   * Assert that the block_scheduler module installed correctly.
   */
  public function testModuleInstalls() {
    // If we get here, then the module was successfully installed during the
    // setUp phase without throwing any Exceptions. Assert that TRUE is true,
    // so at least one assertion runs, and then exit.
    $this->assertTrue(TRUE, 'Module installed correctly.');
  }

}
